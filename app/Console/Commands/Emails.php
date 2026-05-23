<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\ApplicationPreview;
use App\Models\ScheduledDatabaseBackup;
use App\Models\Server;
use App\Models\StandalonePostgresql;
use App\Models\Team;
use App\Notifications\Application\DeploymentFailed;
use App\Notifications\Application\DeploymentSuccess;
use App\Notifications\Application\StatusChanged;
use App\Notifications\Database\BackupFailed;
use App\Notifications\Database\BackupSuccess;
use App\Notifications\Test;
use App\Support\UserVisibleLocale;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Messages\MailMessage;
use Mail;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class Emails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    private ?MailMessage $mail = null;

    private ?string $email = null;

    private string $locale = 'en';

    public function __construct()
    {
        parent::__construct();

        $this->setCommandLocale(app()->getLocale());
    }

    public function handle()
    {
        $this->setCommandLocale(app()->getLocale());

        $type = select(
            trans('console.emails.select_prompt', locale: $this->locale),
            options: [
                'updates' => trans('console.emails.types.updates', locale: $this->locale),
                'emails-test' => trans('console.emails.types.emails_test', locale: $this->locale),
                'database-backup-statuses-daily' => trans('console.emails.types.database_backup_statuses_daily', locale: $this->locale),
                'application-deployment-success-daily' => trans('console.emails.types.application_deployment_success_daily', locale: $this->locale),
                'application-deployment-success' => trans('console.emails.types.application_deployment_success', locale: $this->locale),
                'application-deployment-failed' => trans('console.emails.types.application_deployment_failed', locale: $this->locale),
                'application-status-changed' => trans('console.emails.types.application_status_changed', locale: $this->locale),
                'backup-success' => trans('console.emails.types.backup_success', locale: $this->locale),
                'backup-failed' => trans('console.emails.types.backup_failed', locale: $this->locale),
                // 'invitation-link' => 'Invitation Link',
                'realusers-before-trial' => trans('console.emails.types.realusers_before_trial', locale: $this->locale),
                'realusers-server-lost-connection' => trans('console.emails.types.realusers_server_lost_connection', locale: $this->locale),
            ],
        );
        $emailsGathered = ['realusers-before-trial', 'realusers-server-lost-connection'];
        if (isDev()) {
            $this->email = 'test@example.com';
        } else {
            if (! in_array($type, $emailsGathered)) {
                $this->email = text(trans('console.emails.email_prompt', locale: $this->locale));
            }
        }
        set_transanctional_email_settings();

        $this->mail = new MailMessage;
        $this->mail->subject(trans('console.emails.test_subject', locale: $this->locale));
        switch ($type) {
            case 'updates':
                $teams = Team::all();
                if (! $teams || $teams->isEmpty()) {
                    $this->line(trans('console.emails.no_teams', locale: $this->locale));

                    return;
                }
                $emails = [];
                foreach ($teams as $team) {
                    foreach ($team->members as $member) {
                        if ($member->email && $member->marketing_emails) {
                            $emails[] = $member->email;
                        }
                    }
                }
                $emails = array_unique($emails);
                $this->info(trans('console.emails.sending_emails', ['count' => count($emails)], $this->locale));
                foreach ($emails as $email) {
                    $this->info($email);
                }
                $confirmed = confirm(trans('console.emails.confirm', locale: $this->locale));
                if ($confirmed) {
                    foreach ($emails as $email) {
                        $this->mail = new MailMessage;
                        $this->mail->subject(trans('mail.updates.subject', locale: $this->locale));
                        $unsubscribeUrl = route('unsubscribe.marketing.emails', [
                            'token' => encrypt($email),
                        ]);
                        $this->mail->view('emails.updates', ['unsubscribeUrl' => $unsubscribeUrl]);
                        $this->sendEmail($email);
                    }
                }
                break;
            case 'emails-test':
                $this->mail = (new Test)->toMail();
                $this->sendEmail();
                break;
            case 'application-deployment-success-daily':
                $applications = Application::all();
                foreach ($applications as $application) {
                    $deployments = $application->get_last_days_deployments();
                    if ($deployments->isEmpty()) {
                        continue;
                    }
                    $this->mail = (new DeploymentSuccess($application, 'test'))->toMail();
                    $this->sendEmail();
                }
                break;
            case 'application-deployment-success':
                $application = Application::all()->first();
                $this->mail = (new DeploymentSuccess($application, 'test'))->toMail();
                $this->sendEmail();
                break;
            case 'application-deployment-failed':
                $application = Application::all()->first();
                $preview = ApplicationPreview::all()->first();
                if (! $preview) {
                    $preview = ApplicationPreview::create([
                        'application_id' => $application->id,
                        'pull_request_id' => 1,
                        'pull_request_html_url' => 'http://example.com',
                        'fqdn' => $application->fqdn,
                    ]);
                }
                $this->mail = (new DeploymentFailed($application, 'test'))->toMail();
                $this->sendEmail();
                $this->mail = (new DeploymentFailed($application, 'test', $preview))->toMail();
                $this->sendEmail();
                break;
            case 'application-status-changed':
                $application = Application::all()->first();
                $this->mail = (new StatusChanged($application))->toMail();
                $this->sendEmail();
                break;
            case 'backup-failed':
                $backup = ScheduledDatabaseBackup::all()->first();
                $db = StandalonePostgresql::all()->first();
                if (! $backup) {
                    $backup = ScheduledDatabaseBackup::create([
                        'enabled' => true,
                        'frequency' => 'daily',
                        'save_s3' => false,
                        'database_id' => $db->id,
                        'database_type' => $db->getMorphClass(),
                        'team_id' => 0,
                    ]);
                }
                $output = 'Because of an error, the backup of the database '.$db->name.' failed.';
                $this->mail = (new BackupFailed($backup, $db, $output, $backup->database_name ?? 'unknown'))->toMail();
                $this->sendEmail();
                break;
            case 'backup-success':
                $backup = ScheduledDatabaseBackup::all()->first();
                $db = StandalonePostgresql::all()->first();
                if (! $backup) {
                    $backup = ScheduledDatabaseBackup::create([
                        'enabled' => true,
                        'frequency' => 'daily',
                        'save_s3' => false,
                        'database_id' => $db->id,
                        'database_type' => $db->getMorphClass(),
                        'team_id' => 0,
                    ]);
                }
                // $this->mail = (new BackupSuccess($backup->frequency, $db->name))->toMail();
                $this->sendEmail();
                break;
                // case 'invitation-link':
                //     $user = User::all()->first();
                //     $invitation = TeamInvitation::whereEmail($user->email)->first();
                //     if (!$invitation) {
                //         $invitation = TeamInvitation::create([
                //             'uuid' => Str::uuid(),
                //             'email' => $user->email,
                //             'team_id' => 1,
                //             'link' => 'http://example.com',
                //         ]);
                //     }
                //     $this->mail = (new InvitationLink($user))->toMail();
                //     $this->sendEmail();
                //     break;
            case 'realusers-before-trial':
                $this->mail = new MailMessage;
                $this->mail->view('emails.before-trial-conversion');
                $this->mail->subject(trans('mail.before_trial_conversion.subject', locale: $this->locale));
                $teams = Team::doesntHave('subscription')->where('id', '!=', 0)->get();
                if (! $teams || $teams->isEmpty()) {
                    $this->line(trans('console.emails.no_teams', locale: $this->locale));

                    return;
                }
                $emails = [];
                foreach ($teams as $team) {
                    foreach ($team->members as $member) {
                        if ($member->email) {
                            $emails[] = $member->email;
                        }
                    }
                }
                $emails = array_unique($emails);
                $this->info(trans('console.emails.sending_emails', ['count' => count($emails)], $this->locale));
                foreach ($emails as $email) {
                    $this->info($email);
                }
                $confirmed = confirm(trans('console.emails.confirm', locale: $this->locale));
                if ($confirmed) {
                    foreach ($emails as $email) {
                        $this->sendEmail($email);
                    }
                }
                break;
            case 'realusers-server-lost-connection':
                $serverId = text(trans('console.emails.server_id', locale: $this->locale));
                $server = Server::find($serverId);
                if (! $server) {
                    throw new Exception(trans('console.emails.server_not_found', locale: $this->locale));
                }
                $admins = [];
                $members = $server->team->members;
                foreach ($members as $member) {
                    if ($member->isAdmin()) {
                        $admins[] = $member->email;
                    }
                }
                $this->info(trans('console.emails.sending_admins', ['count' => count($admins)], $this->locale));
                foreach ($admins as $admin) {
                    $this->info($admin);
                }
                $this->mail = new MailMessage;
                $this->mail->view('emails.server-lost-connection', [
                    'name' => $server->name,
                ]);
                $this->mail->subject(trans('mail.server_lost_connection.subject', ['name' => $server->name], $this->locale));
                foreach ($admins as $email) {
                    $this->sendEmail($email);
                }
                break;
        }
    }

    private function sendEmail(?string $email = null)
    {
        if ($email) {
            $this->email = $email;
        }
        UserVisibleLocale::withLocale($this->locale, function () {
            Mail::send(
                [],
                [],
                fn (Message $message) => $message
                    ->to($this->email)
                    ->subject($this->mail->subject)
                    ->html((string) $this->mail->render())
            );
        });
        $this->info(trans('console.emails.sent_successfully', ['email' => $this->email], $this->locale));
    }

    private function setCommandLocale(?string $locale = null): void
    {
        $this->locale = UserVisibleLocale::resolve($locale);
        $description = trans('console.emails.description', locale: $this->locale);

        $this->description = $description;
        $this->setDescription($description);
    }
}
