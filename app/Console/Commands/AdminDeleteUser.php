<?php

namespace App\Console\Commands;

use App\Actions\Stripe\CancelSubscription;
use App\Actions\User\DeleteUserResources;
use App\Actions\User\DeleteUserServers;
use App\Actions\User\DeleteUserTeams;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDeleteUser extends Command
{
    protected $signature = 'admin:delete-user {email}
                            {--dry-run : Preview what will be deleted without actually deleting}
                            {--skip-stripe : Skip Stripe subscription cancellation}
                            {--skip-resources : Skip resource deletion}
                            {--auto-confirm : Skip all confirmation prompts between phases}
                            {--force : Bypass the lock check and force deletion (use with caution)}';

    protected $description = 'Delete a user with comprehensive resource cleanup and phase-by-phase confirmation (works on cloud and self-hosted)';

    private bool $isDryRun = false;

    private bool $skipStripe = false;

    private bool $skipResources = false;

    private User $user;

    private $lock;

    private array $deletionState = [
        'phase_1_overview' => false,
        'phase_2_resources' => false,
        'phase_3_servers' => false,
        'phase_4_teams' => false,
        'phase_5_user_profile' => false,
        'phase_6_stripe' => false,
        'db_committed' => false,
    ];

    public function handle()
    {
        // Register signal handlers for graceful shutdown (Ctrl+C handling)
        $this->registerSignalHandlers();

        $email = $this->argument('email');
        $this->isDryRun = $this->option('dry-run');
        $this->skipStripe = $this->option('skip-stripe');
        $this->skipResources = $this->option('skip-resources');
        $force = $this->option('force');

        if ($force) {
            $this->warn(trans('console.admin_delete_user.intro.force_mode_title'));
            $this->warn(trans('console.admin_delete_user.intro.force_mode_warning'));
            $this->newLine();
        }

        if ($this->isDryRun) {
            $this->info(trans('console.admin_delete_user.intro.dry_run_mode'));
            $this->newLine();
        }

        if ($this->output->isVerbose()) {
            $this->info(trans('console.admin_delete_user.intro.verbose_mode'));
            $this->newLine();
        } else {
            $this->comment(trans('console.admin_delete_user.intro.verbose_tip'));
            $this->newLine();
        }

        if (! $this->isDryRun && ! $this->option('auto-confirm')) {
            $this->info(trans('console.admin_delete_user.intro.interactive_mode'));
            $this->comment(trans('console.admin_delete_user.intro.auto_confirm_tip'));
            $this->newLine();
        }

        // Notify about instance type and Stripe
        if (isCloud()) {
            $this->comment(trans('console.admin_delete_user.intro.cloud_instance'));
        } else {
            $this->comment(trans('console.admin_delete_user.intro.self_hosted_instance'));
        }
        $this->newLine();

        try {
            $this->user = User::whereEmail($email)->firstOrFail();
        } catch (\Exception $e) {
            $this->error("User with email '{$email}' not found.");

            return 1;
        }

        // Implement file lock to prevent concurrent deletions of the same user
        $lockKey = "user_deletion_{$this->user->id}";
        $this->lock = Cache::lock($lockKey, 600); // 10 minute lock

        if (! $force) {
            if (! $this->lock->get()) {
                $this->error('Another deletion process is already running for this user.');
                $this->error('Use --force to bypass this lock (use with extreme caution).');
                $this->logAction("Deletion blocked for user {$email}: Another process is already running");

                return 1;
            }
        } else {
            // In force mode, try to get lock but continue even if it fails
            if (! $this->lock->get()) {
                $this->warn('⚠️  Lock exists but proceeding due to --force flag');
                $this->warn('   There may be another deletion process running!');
                $this->newLine();
            }
        }

        try {
            $this->logAction("Starting user deletion process for: {$email}");

            // Phase 1: Show User Overview (outside transaction)
            if (! $this->showUserOverview()) {
                $this->info('User deletion cancelled by operator.');

                return 0;
            }
            $this->deletionState['phase_1_overview'] = true;

            // If not dry run, wrap DB operations in a transaction
            // NOTE: Stripe cancellations happen AFTER commit to avoid inconsistent state
            if (! $this->isDryRun) {
                try {
                    DB::beginTransaction();

                    // Phase 2: Delete Resources
                    // WARNING: This triggers Docker container deletion via SSH which CANNOT be rolled back
                    if (! $this->skipResources) {
                        if (! $this->deleteResources()) {
                            DB::rollBack();
                            $this->displayErrorState('Phase 2: Resource Deletion');
                            $this->error('❌ User deletion failed at resource deletion phase.');
                            $this->warn('⚠️  Some Docker containers may have been deleted on remote servers and cannot be restored.');
                            $this->displayRecoverySteps();

                            return 1;
                        }
                    }
                    $this->deletionState['phase_2_resources'] = true;

                    // Confirmation to continue after Phase 2
                    if (! $this->skipResources && ! $this->option('auto-confirm')) {
                        $this->newLine();
                        if (! $this->confirm(trans('console.admin_delete_user.confirm.phase_2_to_3'), true)) {
                            DB::rollBack();
                            $this->info('User deletion cancelled by operator after Phase 2.');
                            $this->info('Database changes have been rolled back.');

                            return 0;
                        }
                    }

                    // Phase 3: Delete Servers
                    // WARNING: This may trigger cleanup operations on remote servers which CANNOT be rolled back
                    if (! $this->deleteServers()) {
                        DB::rollBack();
                        $this->displayErrorState('Phase 3: Server Deletion');
                        $this->error('❌ User deletion failed at server deletion phase.');
                        $this->warn('⚠️  Some server cleanup operations may have been performed and cannot be restored.');
                        $this->displayRecoverySteps();

                        return 1;
                    }
                    $this->deletionState['phase_3_servers'] = true;

                    // Confirmation to continue after Phase 3
                    if (! $this->option('auto-confirm')) {
                        $this->newLine();
                        if (! $this->confirm(trans('console.admin_delete_user.confirm.phase_3_to_4'), true)) {
                            DB::rollBack();
                            $this->info('User deletion cancelled by operator after Phase 3.');
                            $this->info('Database changes have been rolled back.');

                            return 0;
                        }
                    }

                    // Phase 4: Handle Teams
                    if (! $this->handleTeams()) {
                        DB::rollBack();
                        $this->displayErrorState('Phase 4: Team Handling');
                        $this->error('❌ User deletion failed at team handling phase.');
                        $this->displayRecoverySteps();

                        return 1;
                    }
                    $this->deletionState['phase_4_teams'] = true;

                    // Confirmation to continue after Phase 4
                    if (! $this->option('auto-confirm')) {
                        $this->newLine();
                        if (! $this->confirm(trans('console.admin_delete_user.confirm.phase_4_to_5'), true)) {
                            DB::rollBack();
                            $this->info('User deletion cancelled by operator after Phase 4.');
                            $this->info('Database changes have been rolled back.');

                            return 0;
                        }
                    }

                    // Phase 5: Delete User Profile
                    if (! $this->deleteUserProfile()) {
                        DB::rollBack();
                        $this->displayErrorState('Phase 5: User Profile Deletion');
                        $this->error('❌ User deletion failed at user profile deletion phase.');
                        $this->displayRecoverySteps();

                        return 1;
                    }
                    $this->deletionState['phase_5_user_profile'] = true;

                    // CRITICAL CONFIRMATION: Database commit is next (PERMANENT)
                    if (! $this->option('auto-confirm')) {
                        $this->newLine();
                        $this->warn(trans('console.admin_delete_user.commit.critical_decision_point'));
                        $this->warn(trans('console.admin_delete_user.commit.next_step'));
                        $this->warn(trans('console.admin_delete_user.commit.cannot_be_undone'));
                        $this->warn(trans('console.admin_delete_user.commit.permanent_delete_warning'));
                        $this->newLine();
                        if (! $this->confirm(trans('console.admin_delete_user.confirm.phase_5_commit'), false)) {
                            DB::rollBack();
                            $this->info('User deletion cancelled by operator before commit.');
                            $this->info('Database changes have been rolled back.');
                            $this->warn(trans('console.admin_delete_user.commit.pre_commit_remote_warning'));

                            return 0;
                        }
                    }

                    // Commit the database transaction
                    DB::commit();
                    $this->deletionState['db_committed'] = true;

                    $this->newLine();
                    $this->info('✅ Database operations completed successfully!');
                    $this->info(trans('console.admin_delete_user.commit.transaction_committed'));
                    $this->logAction("Database deletion completed for: {$email}");

                    // Confirmation to continue to Stripe (after commit)
                    if (! $this->skipStripe && isCloud() && ! $this->option('auto-confirm')) {
                        $this->newLine();
                        $this->warn(trans('console.admin_delete_user.commit.post_commit_warning'));
                        $this->info(trans('console.admin_delete_user.commit.next_stripe_step'));
                        if (! $this->confirm(trans('console.admin_delete_user.confirm.phase_6_stripe'), true)) {
                            $this->warn(trans('console.admin_delete_user.stripe.stopped_after_commit'));
                            $this->error(trans('console.admin_delete_user.stripe.subscriptions_still_active'));
                            $this->error(trans('console.admin_delete_user.stripe.manual_dashboard_step').': https://dashboard.stripe.com/');
                            $this->error(trans('console.admin_delete_user.stripe.manual_search_step').': '.$email);

                            return 1;
                        }
                    }

                    // Phase 6: Cancel Stripe Subscriptions (AFTER DB commit)
                    // This is done AFTER commit because Stripe API calls cannot be rolled back
                    // If this fails, DB changes are already committed but subscriptions remain active
                    if (! $this->skipStripe && isCloud()) {
                        if (! $this->cancelStripeSubscriptions()) {
                            $this->newLine();
                            $this->error('═══════════════════════════════════════');
                            $this->error(trans('console.admin_delete_user.stripe.inconsistent_state'));
                            $this->error('═══════════════════════════════════════');
                            $this->error(trans('console.admin_delete_user.stripe.database_deleted'));
                            $this->error(trans('console.admin_delete_user.stripe.cancellation_failed'));
                            $this->newLine();
                            $this->displayErrorState('Phase 6: Stripe Cancellation (Post-Commit)');
                            $this->newLine();
                            $this->error(trans('console.admin_delete_user.stripe.manual_action_required'));
                            $this->error('1. '.trans('console.admin_delete_user.stripe.manual_dashboard_step').': https://dashboard.stripe.com/');
                            $this->error('2. '.trans('console.admin_delete_user.stripe.manual_search_customer_step').': '.$email);
                            $this->error('3. '.trans('console.admin_delete_user.stripe.manual_cancel_step'));
                            $this->error('4. '.trans('console.admin_delete_user.stripe.manual_check_log_step'));
                            $this->newLine();
                            $this->logAction("INCONSISTENT STATE: User {$email} deleted but Stripe cancellation failed");

                            return 1;
                        }
                    }
                    $this->deletionState['phase_6_stripe'] = true;

                    $this->newLine();
                    $this->info('✅ User deletion completed successfully!');
                    $this->logAction("User deletion completed for: {$email}");

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->newLine();
                    $this->error('═══════════════════════════════════════');
                    $this->error('❌ EXCEPTION DURING USER DELETION');
                    $this->error('═══════════════════════════════════════');
                    $this->error('Exception: '.get_class($e));
                    $this->error('Message: '.$e->getMessage());
                    $this->error('File: '.$e->getFile().':'.$e->getLine());
                    $this->newLine();

                    if ($this->output->isVerbose()) {
                        $this->error('Stack Trace:');
                        $this->error($e->getTraceAsString());
                        $this->newLine();
                    } else {
                        $this->info(trans('console.admin_delete_user.intro.verbose_retry_tip'));
                        $this->newLine();
                    }

                    $this->displayErrorState('Exception during execution');
                    $this->displayRecoverySteps();

                    $this->logAction("User deletion failed for {$email}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

                    return 1;
                }
            } else {
                // Dry run mode - just run through the phases without transaction
                // Phase 2: Delete Resources
                if (! $this->skipResources) {
                    if (! $this->deleteResources()) {
                        $this->info('User deletion would be cancelled at resource deletion phase.');

                        return 0;
                    }
                }

                // Phase 3: Delete Servers
                if (! $this->deleteServers()) {
                    $this->info('User deletion would be cancelled at server deletion phase.');

                    return 0;
                }

                // Phase 4: Handle Teams
                if (! $this->handleTeams()) {
                    $this->info('User deletion would be cancelled at team handling phase.');

                    return 0;
                }

                // Phase 5: Delete User Profile
                if (! $this->deleteUserProfile()) {
                    $this->info('User deletion would be cancelled at user profile deletion phase.');

                    return 0;
                }

                // Phase 6: Cancel Stripe Subscriptions (shown after DB operations in dry run too)
                if (! $this->skipStripe && isCloud()) {
                    if (! $this->cancelStripeSubscriptions()) {
                        $this->info('User deletion would be cancelled at Stripe cancellation phase.');

                        return 0;
                    }
                }

                $this->newLine();
                $this->info('✅ DRY RUN completed successfully! No data was deleted.');
            }

            return 0;
        } finally {
            // Ensure lock is always released
            $this->releaseLock();
        }
    }

    private function showUserOverview(): bool
    {
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.overview.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $teams = $this->user->teams()->get();
        $ownedTeams = $teams->filter(fn ($team) => $team->pivot->role === 'owner');
        $memberTeams = $teams->filter(fn ($team) => $team->pivot->role !== 'owner');

        // Collect servers and resources ONLY from teams that will be FULLY DELETED
        // This means: user is owner AND is the ONLY member
        //
        // Resources from these teams will NOT be deleted:
        // - Teams where user is just a member
        // - Teams where user is owner but has other members (will be transferred/user removed)
        $allServers = collect();
        $allApplications = collect();
        $allDatabases = collect();
        $allServices = collect();
        $activeSubscriptions = collect();

        foreach ($teams as $team) {
            $userRole = $team->pivot->role;
            $memberCount = $team->members->count();

            // Only show resources from teams where user is the ONLY member
            // These are the teams that will be fully deleted
            if ($userRole !== 'owner' || $memberCount > 1) {
                continue;
            }

            $servers = $team->servers()->get();
            $allServers = $allServers->merge($servers);

            foreach ($servers as $server) {
                $resources = $server->definedResources();
                foreach ($resources as $resource) {
                    if ($resource instanceof Application) {
                        $allApplications->push($resource);
                    } elseif ($resource instanceof Service) {
                        $allServices->push($resource);
                    } else {
                        $allDatabases->push($resource);
                    }
                }
            }

            // Only collect subscriptions on cloud instances
            if (isCloud() && $team->subscription && $team->subscription->stripe_subscription_id) {
                $activeSubscriptions->push($team->subscription);
            }
        }

        // Build table data
        $tableData = [
            [trans('console.admin_delete_user.overview.fields.user'), $this->user->email],
            [trans('console.admin_delete_user.overview.fields.user_id'), $this->user->id],
            [trans('console.admin_delete_user.overview.fields.created'), $this->user->created_at->format('Y-m-d H:i:s')],
            [trans('console.admin_delete_user.overview.fields.last_login'), $this->user->updated_at->format('Y-m-d H:i:s')],
            [trans('console.admin_delete_user.overview.fields.teams_total'), $teams->count()],
            [trans('console.admin_delete_user.overview.fields.teams_owner'), $ownedTeams->count()],
            [trans('console.admin_delete_user.overview.fields.teams_member'), $memberTeams->count()],
            [trans('console.admin_delete_user.overview.fields.servers'), $allServers->unique('id')->count()],
            [trans('console.admin_delete_user.overview.fields.applications'), $allApplications->count()],
            [trans('console.admin_delete_user.overview.fields.databases'), $allDatabases->count()],
            [trans('console.admin_delete_user.overview.fields.services'), $allServices->count()],
        ];

        // Only show Stripe subscriptions on cloud instances
        if (isCloud()) {
            $tableData[] = [trans('console.admin_delete_user.overview.fields.active_stripe_subscriptions'), $activeSubscriptions->count()];
        }

        $this->table([
            trans('console.admin_delete_user.overview.table_headers.property'),
            trans('console.admin_delete_user.overview.table_headers.value'),
        ], $tableData);

        $this->newLine();

        $this->warn(trans('console.admin_delete_user.overview.permanent_warning'));
        $this->newLine();

        if (! $this->confirm(trans('console.admin_delete_user.overview.continue_prompt'), false)) {
            return false;
        }

        return true;
    }

    private function deleteResources(): bool
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.resources.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $action = new DeleteUserResources($this->user, $this->isDryRun);
        $resources = $action->getResourcesPreview();

        if ($resources['applications']->isEmpty() &&
            $resources['databases']->isEmpty() &&
            $resources['services']->isEmpty()) {
            $this->info(trans('console.admin_delete_user.resources.no_resources'));

            return true;
        }

        $this->info(trans('console.admin_delete_user.resources.summary_title'));
        $this->newLine();

        if ($resources['applications']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.resources.applications_title', ['count' => $resources['applications']->count()]));
            $this->table(
                [
                    trans('console.admin_delete_user.resources.table_headers.name'),
                    trans('console.admin_delete_user.resources.table_headers.uuid'),
                    trans('console.admin_delete_user.resources.table_headers.server'),
                    trans('console.admin_delete_user.resources.table_headers.status'),
                ],
                $resources['applications']->map(function ($app) {
                    return [
                        $app->name,
                        $app->uuid,
                        $app->destination->server->name,
                        $app->status ?? 'unknown',
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        if ($resources['databases']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.resources.databases_title', ['count' => $resources['databases']->count()]));
            $this->table(
                [
                    trans('console.admin_delete_user.resources.table_headers.name'),
                    trans('console.admin_delete_user.resources.table_headers.type'),
                    trans('console.admin_delete_user.resources.table_headers.uuid'),
                    trans('console.admin_delete_user.resources.table_headers.server'),
                ],
                $resources['databases']->map(function ($db) {
                    return [
                        $db->name,
                        class_basename($db),
                        $db->uuid,
                        $db->destination->server->name,
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        if ($resources['services']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.resources.services_title', ['count' => $resources['services']->count()]));
            $this->table(
                [
                    trans('console.admin_delete_user.resources.table_headers.name'),
                    trans('console.admin_delete_user.resources.table_headers.uuid'),
                    trans('console.admin_delete_user.resources.table_headers.server'),
                ],
                $resources['services']->map(function ($service) {
                    return [
                        $service->name,
                        $service->uuid,
                        $service->server->name,
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        $this->error(trans('console.admin_delete_user.resources.irreversible_warning'));
        if (! $this->confirm(trans('console.admin_delete_user.resources.confirm_delete_all'), false)) {
            return false;
        }

        if (! $this->isDryRun) {
            $this->info(trans('console.admin_delete_user.resources.deleting'));
            try {
                $result = $action->execute();
                $this->info(trans('console.admin_delete_user.resources.deleted_summary', [
                    'applications' => $result['applications'],
                    'databases' => $result['databases'],
                    'services' => $result['services'],
                ]));
                $this->logAction("Deleted resources for user {$this->user->email}: {$result['applications']} apps, {$result['databases']} databases, {$result['services']} services");
            } catch (\Exception $e) {
                $this->error(trans('console.admin_delete_user.resources.delete_failed'));
                $this->error(trans('console.admin_delete_user.resources.exception_label').': '.get_class($e));
                $this->error(trans('console.admin_delete_user.resources.message_label').': '.$e->getMessage());
                $this->error(trans('console.admin_delete_user.resources.file_label').': '.$e->getFile().':'.$e->getLine());

                if ($this->output->isVerbose()) {
                    $this->error(trans('console.admin_delete_user.resources.stack_trace_label'));
                    $this->error($e->getTraceAsString());
                }

                throw $e; // Re-throw to trigger rollback
            }
        }

        return true;
    }

    private function deleteServers(): bool
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.servers.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $action = new DeleteUserServers($this->user, $this->isDryRun);
        $servers = $action->getServersPreview();

        if ($servers->isEmpty()) {
            $this->info(trans('console.admin_delete_user.servers.no_servers'));

            return true;
        }

        $this->warn(trans('console.admin_delete_user.servers.summary_title', ['count' => $servers->count()]));
        $this->table(
            [
                trans('console.admin_delete_user.servers.table_headers.id'),
                trans('console.admin_delete_user.servers.table_headers.name'),
                trans('console.admin_delete_user.servers.table_headers.ip'),
                trans('console.admin_delete_user.servers.table_headers.description'),
                trans('console.admin_delete_user.servers.table_headers.resources_count'),
            ],
            $servers->map(function ($server) {
                $resourceCount = $server->definedResources()->count();

                return [
                    $server->id,
                    $server->name,
                    $server->ip,
                    $server->description ?? '-',
                    $resourceCount,
                ];
            })->toArray()
        );
        $this->newLine();

        $this->error(trans('console.admin_delete_user.servers.irreversible_warning'));
        if (! $this->confirm(trans('console.admin_delete_user.servers.confirm_delete_all'), false)) {
            return false;
        }

        if (! $this->isDryRun) {
            $this->info(trans('console.admin_delete_user.servers.deleting'));
            try {
                $result = $action->execute();
                $this->info(trans('console.admin_delete_user.servers.deleted_summary', ['count' => $result['servers']]));
                $this->logAction("Deleted {$result['servers']} servers for user {$this->user->email}");
            } catch (\Exception $e) {
                $this->error(trans('console.admin_delete_user.servers.delete_failed'));
                $this->error(trans('console.admin_delete_user.servers.exception_label').': '.get_class($e));
                $this->error(trans('console.admin_delete_user.servers.message_label').': '.$e->getMessage());
                $this->error(trans('console.admin_delete_user.servers.file_label').': '.$e->getFile().':'.$e->getLine());

                if ($this->output->isVerbose()) {
                    $this->error(trans('console.admin_delete_user.servers.stack_trace_label'));
                    $this->error($e->getTraceAsString());
                }

                throw $e; // Re-throw to trigger rollback
            }
        }

        return true;
    }

    private function handleTeams(): bool
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.teams.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $action = new DeleteUserTeams($this->user, $this->isDryRun);
        $preview = $action->getTeamsPreview();

        // Check for edge cases first - EXIT IMMEDIATELY if found
        if ($preview['edge_cases']->isNotEmpty()) {
            $this->error('═══════════════════════════════════════');
            $this->error(trans('console.admin_delete_user.teams.edge_cases.detected_cannot_proceed'));
            $this->error('═══════════════════════════════════════');
            $this->newLine();

            foreach ($preview['edge_cases'] as $edgeCase) {
                $team = $edgeCase['team'];
                $reason = $edgeCase['reason'];
                $this->error(trans('console.admin_delete_user.teams.edge_cases.team_line', [
                    'team' => $team->name,
                    'id' => $team->id,
                ]));
                $this->error(trans('console.admin_delete_user.teams.edge_cases.issue_line', ['issue' => $reason]));

                // Show team members for context
                $this->info(trans('console.admin_delete_user.teams.edge_cases.current_members'));
                foreach ($team->members as $member) {
                    $role = $member->pivot->role;
                    $this->line(trans('console.admin_delete_user.teams.edge_cases.member_line', [
                        'name' => $member->name,
                        'email' => $member->email,
                        'role' => $role,
                    ]));
                }

                // Check for active resources
                $resourceCount = 0;
                foreach ($team->servers()->get() as $server) {
                    $resources = $server->definedResources();
                    $resourceCount += $resources->count();
                }

                if ($resourceCount > 0) {
                    $this->warn(trans('console.admin_delete_user.teams.edge_cases.active_resources_warning', [
                        'count' => $resourceCount,
                    ]));
                }

                // Show subscription details if relevant
                if ($team->subscription && $team->subscription->stripe_subscription_id) {
                    $this->warn(trans('console.admin_delete_user.teams.edge_cases.active_stripe_subscription_details'));
                    $this->warn("    Subscription ID: {$team->subscription->stripe_subscription_id}");
                    $this->warn("    Customer ID: {$team->subscription->stripe_customer_id}");

                    // Show other owners who could potentially take over
                    $otherOwners = $team->members
                        ->where('id', '!=', $this->user->id)
                        ->filter(function ($member) {
                            return $member->pivot->role === 'owner';
                        });

                    if ($otherOwners->isNotEmpty()) {
                        $this->info(trans('console.admin_delete_user.teams.edge_cases.other_owners_billing'));
                        foreach ($otherOwners as $owner) {
                            $this->line("    - {$owner->name} ({$owner->email})");
                        }
                    }
                }

                $this->newLine();
            }

            $this->error(trans('console.admin_delete_user.teams.edge_cases.manual_resolution_title'));

            // Check if any edge case involves subscription payment issues
            $hasSubscriptionIssue = $preview['edge_cases']->contains(function ($edgeCase) {
                return str_contains($edgeCase['reason'], 'Stripe subscription');
            });

            if ($hasSubscriptionIssue) {
                $this->info(trans('console.admin_delete_user.teams.edge_cases.subscription_payment_issues.title'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.subscription_payment_issues.step_1'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.subscription_payment_issues.step_2'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.subscription_payment_issues.step_3'));
                $this->newLine();
            }

            $hasNoOwnerReplacement = $preview['edge_cases']->contains(function ($edgeCase) {
                return str_contains($edgeCase['reason'], 'No suitable owner replacement');
            });

            if ($hasNoOwnerReplacement) {
                $this->info(trans('console.admin_delete_user.teams.edge_cases.no_owner_replacement.title'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.no_owner_replacement.step_1'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.no_owner_replacement.step_2'));
                $this->info(trans('console.admin_delete_user.teams.edge_cases.no_owner_replacement.step_3'));
                $this->newLine();
            }

            $this->error(trans('console.admin_delete_user.teams.edge_cases.user_deletion_aborted'));
            $this->logAction("User deletion aborted for {$this->user->email}: Edge cases in team handling");

            // Return false to trigger proper cleanup and lock release
            return false;
        }

        if ($preview['to_delete']->isEmpty() &&
            $preview['to_transfer']->isEmpty() &&
            $preview['to_leave']->isEmpty()) {
            $this->info(trans('console.admin_delete_user.teams.no_changes_needed'));

            return true;
        }

        if ($preview['to_delete']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.teams.delete_summary_title'));
            $this->table(
                [
                    trans('console.admin_delete_user.teams.table_headers.id'),
                    trans('console.admin_delete_user.teams.table_headers.name'),
                    trans('console.admin_delete_user.teams.table_headers.resources'),
                    trans('console.admin_delete_user.teams.table_headers.subscription'),
                ],
                $preview['to_delete']->map(function ($team) {
                    $resourceCount = 0;
                    foreach ($team->servers()->get() as $server) {
                        $resourceCount += $server->definedResources()->count();
                    }
                    $hasSubscription = $team->subscription && $team->subscription->stripe_subscription_id
                        ? '⚠️ YES - '.$team->subscription->stripe_subscription_id
                        : 'No';

                    return [
                        $team->id,
                        $team->name,
                        $resourceCount,
                        $hasSubscription,
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        if ($preview['to_transfer']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.teams.transfer_summary_title'));
            $this->table(
                [
                    trans('console.admin_delete_user.teams.transfer_table_headers.team_id'),
                    trans('console.admin_delete_user.teams.transfer_table_headers.team_name'),
                    trans('console.admin_delete_user.teams.transfer_table_headers.new_owner'),
                    trans('console.admin_delete_user.teams.transfer_table_headers.new_owner_email'),
                ],
                $preview['to_transfer']->map(function ($item) {
                    return [
                        $item['team']->id,
                        $item['team']->name,
                        $item['new_owner']->name,
                        $item['new_owner']->email,
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        if ($preview['to_leave']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.teams.leave_summary_title'));
            $userId = $this->user->id;
            $this->table(
                [
                    trans('console.admin_delete_user.teams.leave_table_headers.id'),
                    trans('console.admin_delete_user.teams.leave_table_headers.name'),
                    trans('console.admin_delete_user.teams.leave_table_headers.user_role'),
                    trans('console.admin_delete_user.teams.leave_table_headers.other_members'),
                ],
                $preview['to_leave']->map(function ($team) use ($userId) {
                    $userRole = $team->members->where('id', $userId)->first()->pivot->role;
                    $otherMembers = $team->members->count() - 1;

                    return [
                        $team->id,
                        $team->name,
                        $userRole,
                        $otherMembers,
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        $this->error(trans('console.admin_delete_user.teams.final_warning'));
        if (! $this->confirm(trans('console.admin_delete_user.teams.confirm_proceed'), false)) {
            return false;
        }

        if (! $this->isDryRun) {
            $this->info(trans('console.admin_delete_user.teams.processing'));
            try {
                $result = $action->execute();
                $this->info(trans('console.admin_delete_user.teams.processed_summary', [
                    'deleted' => $result['deleted'],
                    'transferred' => $result['transferred'],
                    'left' => $result['left'],
                ]));
                $this->logAction("Team changes for user {$this->user->email}: deleted {$result['deleted']}, transferred {$result['transferred']}, left {$result['left']}");
            } catch (\Exception $e) {
                $this->error(trans('console.admin_delete_user.teams.process_failed'));
                $this->error(trans('console.admin_delete_user.teams.exception_label').': '.get_class($e));
                $this->error(trans('console.admin_delete_user.teams.message_label').': '.$e->getMessage());
                $this->error(trans('console.admin_delete_user.teams.file_label').': '.$e->getFile().':'.$e->getLine());

                if ($this->output->isVerbose()) {
                    $this->error(trans('console.admin_delete_user.teams.stack_trace_label'));
                    $this->error($e->getTraceAsString());
                }

                throw $e; // Re-throw to trigger rollback
            }
        }

        return true;
    }

    private function cancelStripeSubscriptions(): bool
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.stripe.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $action = new CancelSubscription($this->user, $this->isDryRun);
        $subscriptions = $action->getSubscriptionsPreview();

        if ($subscriptions->isEmpty()) {
            $this->info(trans('console.admin_delete_user.stripe.no_subscriptions'));

            return true;
        }

        // Verify subscriptions in Stripe before showing details
        $this->info(trans('console.admin_delete_user.stripe.verifying'));
        $verification = $action->verifySubscriptionsInStripe();

        if (! empty($verification['errors'])) {
            $this->warn(trans('console.admin_delete_user.stripe.verification_errors_title'));
            foreach ($verification['errors'] as $error) {
                $this->warn("  - {$error}");
            }
            $this->newLine();
        }

        if ($verification['not_found']->isNotEmpty()) {
            $this->warn(trans('console.admin_delete_user.stripe.not_found_or_inactive'));
            foreach ($verification['not_found'] as $item) {
                $subscription = $item['subscription'];
                $reason = $item['reason'];
                $this->line("  - {$subscription->stripe_subscription_id} (Team: {$subscription->team->name}) - {$reason}");
            }
            $this->newLine();
        }

        if ($verification['verified']->isEmpty()) {
            $this->info(trans('console.admin_delete_user.stripe.no_active_subscriptions'));

            return true;
        }

        $this->info(trans('console.admin_delete_user.stripe.active_subscriptions_title'));
        $this->newLine();

        $totalMonthlyValue = 0;
        foreach ($verification['verified'] as $item) {
            $subscription = $item['subscription'];
            $stripeStatus = $item['stripe_status'];
            $team = $subscription->team;
            $planId = $subscription->stripe_plan_id;

            // Try to get the price from config
            $monthlyValue = $this->getSubscriptionMonthlyValue($planId);
            $totalMonthlyValue += $monthlyValue;

            $this->line("  - {$subscription->stripe_subscription_id} (Team: {$team->name})");
            $this->line(trans('console.admin_delete_user.stripe.status', ['status' => $stripeStatus]));
            if ($monthlyValue > 0) {
                $this->line(trans('console.admin_delete_user.stripe.monthly_value', ['amount' => $monthlyValue]));
            }
            if ($subscription->stripe_cancel_at_period_end) {
                $this->line(trans('console.admin_delete_user.stripe.already_cancel_at_period_end'));
            }
        }

        if ($totalMonthlyValue > 0) {
            $this->newLine();
            $this->warn(trans('console.admin_delete_user.stripe.total_monthly_value', ['amount' => $totalMonthlyValue]));
        }
        $this->newLine();

        $this->error(trans('console.admin_delete_user.stripe.immediate_cancellation_warning'));
        $this->warn(trans('console.admin_delete_user.stripe.irreversible_note'));
        if (! $this->confirm(trans('console.admin_delete_user.stripe.confirm_immediate_cancellation'), false)) {
            return false;
        }

        if (! $this->isDryRun) {
            $this->info(trans('console.admin_delete_user.stripe.cancelling'));
            $result = $action->execute();
            $this->info(trans('console.admin_delete_user.stripe.cancelled_summary', [
                'cancelled' => $result['cancelled'],
                'failed' => $result['failed'],
            ]));
            if ($result['failed'] > 0 && ! empty($result['errors'])) {
                $this->error(trans('console.admin_delete_user.stripe.failed_subscriptions_title'));
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }

                return false;
            }
            $this->logAction("Cancelled {$result['cancelled']} Stripe subscriptions for user {$this->user->email}");
        }

        return true;
    }

    private function deleteUserProfile(): bool
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info(trans('console.admin_delete_user.user_profile.phase_title'));
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        $this->warn(trans('console.admin_delete_user.user_profile.final_warning'));
        $this->newLine();

        $this->info(trans('console.admin_delete_user.user_profile.summary_title'));
        $this->table(
            [
                trans('console.admin_delete_user.user_profile.table_headers.property'),
                trans('console.admin_delete_user.user_profile.table_headers.value'),
            ],
            [
                [trans('console.admin_delete_user.user_profile.fields.email'), $this->user->email],
                [trans('console.admin_delete_user.user_profile.fields.name'), $this->user->name],
                [trans('console.admin_delete_user.user_profile.fields.user_id'), $this->user->id],
                [trans('console.admin_delete_user.user_profile.fields.created'), $this->user->created_at->format('Y-m-d H:i:s')],
                [trans('console.admin_delete_user.user_profile.fields.email_verified'), $this->user->email_verified_at ? 'Yes' : 'No'],
                [trans('console.admin_delete_user.user_profile.fields.two_factor_enabled'), $this->user->two_factor_confirmed_at ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();

        $this->warn(trans('console.admin_delete_user.user_profile.confirmation_instruction', [
            'confirmation_text' => "DELETE {$this->user->email}",
        ]));
        $confirmation = $this->ask(trans('console.admin_delete_user.user_profile.confirmation_label'));

        if ($confirmation !== "DELETE {$this->user->email}") {
            $this->error(trans('console.admin_delete_user.user_profile.confirmation_mismatch'));

            return false;
        }

        if (! $this->isDryRun) {
            $this->info(trans('console.admin_delete_user.user_profile.deleting'));

            try {
                $this->user->delete();
                $this->info(trans('console.admin_delete_user.user_profile.deleted_successfully'));
                $this->logAction("User profile deleted: {$this->user->email}");
            } catch (\Exception $e) {
                $this->error(trans('console.admin_delete_user.user_profile.delete_failed'));
                $this->error(trans('console.admin_delete_user.user_profile.exception_label').': '.get_class($e));
                $this->error(trans('console.admin_delete_user.user_profile.message_label').': '.$e->getMessage());
                $this->error(trans('console.admin_delete_user.user_profile.file_label').': '.$e->getFile().':'.$e->getLine());

                if ($this->output->isVerbose()) {
                    $this->error(trans('console.admin_delete_user.user_profile.stack_trace_label'));
                    $this->error($e->getTraceAsString());
                }

                $this->logAction("Failed to delete user profile {$this->user->email}: {$e->getMessage()}");

                throw $e; // Re-throw to trigger rollback
            }
        }

        return true;
    }

    private function getSubscriptionMonthlyValue(string $planId): int
    {
        // Try to get pricing from subscription metadata or config
        // Since we're using dynamic pricing, return 0 for now
        // This could be enhanced by fetching the actual price from Stripe API

        // Check if this is a dynamic pricing plan
        $dynamicMonthlyPlanId = config('subscription.stripe_price_id_dynamic_monthly');
        $dynamicYearlyPlanId = config('subscription.stripe_price_id_dynamic_yearly');

        if ($planId === $dynamicMonthlyPlanId || $planId === $dynamicYearlyPlanId) {
            // For dynamic pricing, we can't determine the exact amount without calling Stripe API
            // Return 0 to indicate dynamic/usage-based pricing
            return 0;
        }

        // For any other plans, return 0 as we don't have hardcoded prices
        return 0;
    }

    private function logAction(string $message): void
    {
        $logMessage = "[CloudDeleteUser] {$message}";

        if ($this->isDryRun) {
            $logMessage = "[DRY RUN] {$logMessage}";
        }

        Log::channel('single')->info($logMessage);

        // Also log to a dedicated user deletion log file
        $logFile = storage_path('logs/user-deletions.log');

        // Ensure the logs directory exists
        $logDir = dirname($logFile);
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$timestamp}] {$logMessage}\n", FILE_APPEND | LOCK_EX);
    }

    private function displayErrorState(string $failedAt): void
    {
        $this->newLine();
        $this->error('═══════════════════════════════════════');
        $this->error(trans('console.admin_delete_user.error_state.title'));
        $this->error('═══════════════════════════════════════');
        $this->error(trans('console.admin_delete_user.error_state.failed_at', ['phase' => $failedAt]));
        $this->newLine();

        $stateTable = [];
        foreach ($this->deletionState as $phase => $completed) {
            $phaseLabel = str_replace('_', ' ', ucwords($phase, '_'));
            $status = $completed
                ? trans('console.admin_delete_user.error_state.status.completed')
                : trans('console.admin_delete_user.error_state.status.not_completed');
            $stateTable[] = [$phaseLabel, $status];
        }

        $this->table([
            trans('console.admin_delete_user.error_state.table_headers.phase'),
            trans('console.admin_delete_user.error_state.table_headers.status'),
        ], $stateTable);
        $this->newLine();

        // Show what was rolled back vs what remains
        if ($this->deletionState['db_committed']) {
            $this->error(trans('console.admin_delete_user.error_state.database_committed'));
        } else {
            $this->info(trans('console.admin_delete_user.error_state.database_rolled_back'));
        }

        $this->newLine();
        $this->error(trans('console.admin_delete_user.error_state.user_email', ['email' => $this->user->email]));
        $this->error(trans('console.admin_delete_user.error_state.user_id', ['id' => $this->user->id]));
        $this->error(trans('console.admin_delete_user.error_state.timestamp', ['timestamp' => now()->format('Y-m-d H:i:s')]));
        $this->newLine();
    }

    private function displayRecoverySteps(): void
    {
        $this->error('═══════════════════════════════════════');
        $this->error(trans('console.admin_delete_user.recovery.title'));
        $this->error('═══════════════════════════════════════');

        if (! $this->deletionState['db_committed']) {
            $this->info(trans('console.admin_delete_user.recovery.database_rolled_back'));
            $this->newLine();

            if ($this->deletionState['phase_2_resources'] || $this->deletionState['phase_3_servers']) {
                $this->warn(trans('console.admin_delete_user.recovery.remote_operations_warning'));
                $this->newLine();

                if ($this->deletionState['phase_2_resources']) {
                    $this->warn(trans('console.admin_delete_user.recovery.phase_2_attempted'));
                    $this->warn(trans('console.admin_delete_user.recovery.check_orphaned_containers'));
                    $this->warn(trans('console.admin_delete_user.recovery.use_command').': docker ps -a | grep coolify');
                    $this->warn(trans('console.admin_delete_user.recovery.remove_command').': docker rm -f <container_id>');
                    $this->newLine();
                }

                if ($this->deletionState['phase_3_servers']) {
                    $this->warn(trans('console.admin_delete_user.recovery.phase_3_attempted'));
                    $this->warn(trans('console.admin_delete_user.recovery.check_server_configurations'));
                    $this->warn(trans('console.admin_delete_user.recovery.verify_ssh_access'));
                    $this->newLine();
                }
            }
        } else {
            $this->error(trans('console.admin_delete_user.recovery.database_committed'));
            $this->newLine();
            $this->error(trans('console.admin_delete_user.recovery.permanently_deleted'));

            if ($this->deletionState['phase_5_user_profile']) {
                $this->error(trans('console.admin_delete_user.recovery.deleted_user_profile', ['email' => $this->user->email]));
            }
            if ($this->deletionState['phase_4_teams']) {
                $this->error(trans('console.admin_delete_user.recovery.deleted_teams'));
            }
            if ($this->deletionState['phase_3_servers']) {
                $this->error(trans('console.admin_delete_user.recovery.deleted_servers'));
            }
            if ($this->deletionState['phase_2_resources']) {
                $this->error(trans('console.admin_delete_user.recovery.deleted_resources'));
            }

            $this->newLine();

            if (! $this->deletionState['phase_6_stripe']) {
                $this->error(trans('console.admin_delete_user.recovery.stripe_not_cancelled'));
                $this->error('1. '.trans('console.admin_delete_user.stripe.manual_dashboard_step').': https://dashboard.stripe.com/');
                $this->error('2. '.trans('console.admin_delete_user.stripe.manual_search_step').': '.$this->user->email);
                $this->error('3. '.trans('console.admin_delete_user.recovery.cancel_subscriptions_manually'));
                $this->newLine();
            }
        }

        $this->error(trans('console.admin_delete_user.recovery.log_file').': storage/logs/user-deletions.log');
        $this->error(trans('console.admin_delete_user.recovery.check_logs'));
        $this->newLine();
    }

    /**
     * Register signal handlers for graceful shutdown on Ctrl+C (SIGINT) and SIGTERM
     */
    private function registerSignalHandlers(): void
    {
        if (! function_exists('pcntl_signal')) {
            // pcntl extension not available, skip signal handling
            return;
        }

        // Handle Ctrl+C (SIGINT)
        pcntl_signal(SIGINT, function () {
            $this->newLine();
            $this->warn('═══════════════════════════════════════');
            $this->warn(trans('console.admin_delete_user.signals.process_interrupted'));
            $this->warn('═══════════════════════════════════════');
            $this->info(trans('console.admin_delete_user.signals.cleanup_and_release_lock'));
            $this->releaseLock();
            $this->info(trans('console.admin_delete_user.signals.lock_released_exit_gracefully'));
            exit(130); // Standard exit code for SIGINT
        });

        // Handle SIGTERM
        pcntl_signal(SIGTERM, function () {
            $this->newLine();
            $this->warn('═══════════════════════════════════════');
            $this->warn(trans('console.admin_delete_user.signals.process_terminated'));
            $this->warn('═══════════════════════════════════════');
            $this->info(trans('console.admin_delete_user.signals.cleanup_and_release_lock'));
            $this->releaseLock();
            $this->info(trans('console.admin_delete_user.signals.lock_released_exit_gracefully'));
            exit(143); // Standard exit code for SIGTERM
        });

        // Enable async signal handling
        pcntl_async_signals(true);
    }

    /**
     * Release the lock if it exists
     */
    private function releaseLock(): void
    {
        if ($this->lock) {
            try {
                $this->lock->release();
            } catch (\Exception $e) {
                // Silently ignore lock release errors
                // Lock will expire after 10 minutes anyway
            }
        }
    }
}
