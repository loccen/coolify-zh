<?php

namespace App\Console\Commands\Cloud;

use App\Jobs\SyncStripeSubscriptionsJob;
use Illuminate\Console\Command;

class SyncStripeSubscriptions extends Command
{
    protected $signature = 'cloud:sync-stripe-subscriptions {--fix : Actually fix discrepancies (default is check only)}';

    protected $description = 'Sync subscription status with Stripe. By default only checks, use --fix to apply changes.';

    public function getDescription(): string
    {
        return trans('console.sync_stripe_subscriptions.description');
    }

    public function handle(): int
    {
        if (! isCloud()) {
            $this->error(trans('console.sync_stripe_subscriptions.error.cloud_only'));

            return 1;
        }

        if (! isStripe()) {
            $this->error(trans('console.sync_stripe_subscriptions.error.stripe_not_configured'));

            return 1;
        }

        $fix = $this->option('fix');

        if ($fix) {
            $this->warn(trans('console.sync_stripe_subscriptions.warn.running_with_fix'));
        } else {
            $this->info(trans('console.sync_stripe_subscriptions.info.running_in_check_mode'));
        }

        $this->newLine();

        $job = new SyncStripeSubscriptionsJob($fix);
        $fetched = 0;
        $result = $job->handle(function (int $count) use (&$fetched): void {
            $fetched = $count;
            $this->output->write("\r  ".trans('console.sync_stripe_subscriptions.info.fetching_subscriptions', ['count' => $fetched]));
        });
        if ($fetched > 0) {
            $this->output->write("\r".str_repeat(' ', 60)."\r");
        }

        if (isset($result['error'])) {
            $this->error($result['error']);

            return 1;
        }

        $this->info(trans('console.sync_stripe_subscriptions.info.total_subscriptions_checked', ['count' => $result['total_checked']]));
        $this->newLine();

        if (count($result['discrepancies']) > 0) {
            $this->warn(trans('console.sync_stripe_subscriptions.warn.discrepancies_found', ['count' => count($result['discrepancies'])]));
            $this->newLine();

            foreach ($result['discrepancies'] as $discrepancy) {
                $this->line('  - '.trans('console.sync_stripe_subscriptions.labels.subscription_id', ['value' => $discrepancy['subscription_id']]));
                $this->line('    '.trans('console.sync_stripe_subscriptions.labels.team_id', ['value' => $discrepancy['team_id']]));
                $this->line('    '.trans('console.sync_stripe_subscriptions.labels.stripe_id', ['value' => $discrepancy['stripe_subscription_id']]));
                $this->line('    '.trans('console.sync_stripe_subscriptions.labels.stripe_status', ['value' => $discrepancy['stripe_status']]));
                $this->newLine();
            }

            if ($fix) {
                $this->info(trans('console.sync_stripe_subscriptions.info.all_discrepancies_fixed'));
            } else {
                $this->comment(trans('console.sync_stripe_subscriptions.info.run_with_fix'));
            }
        } else {
            $this->info(trans('console.sync_stripe_subscriptions.info.no_discrepancies_found'));
        }

        if (count($result['resubscribed']) > 0) {
            $this->newLine();
            $this->warn(trans('console.sync_stripe_subscriptions.warn.resubscribed_users', ['count' => count($result['resubscribed'])]));
            $this->newLine();

            foreach ($result['resubscribed'] as $resub) {
                $this->line('  - '.trans('console.sync_stripe_subscriptions.labels.team_id_with_email', ['team_id' => $resub['team_id'], 'email' => $resub['email']]));
                $this->line('    '.trans('console.sync_stripe_subscriptions.labels.old', ['subscription_id' => $resub['old_stripe_subscription_id'], 'customer_id' => $resub['old_stripe_customer_id']]));
                $this->line('    '.trans('console.sync_stripe_subscriptions.labels.new', ['subscription_id' => $resub['new_stripe_subscription_id'], 'customer_id' => $resub['new_stripe_customer_id'], 'status' => $resub['new_status']]));
                $this->newLine();
            }
        }

        if (count($result['errors']) > 0) {
            $this->newLine();
            $this->error(trans('console.sync_stripe_subscriptions.error.errors_encountered', ['count' => count($result['errors'])]));
            foreach ($result['errors'] as $error) {
                $this->line('  - '.trans('console.sync_stripe_subscriptions.labels.subscription_error', ['subscription_id' => $error['subscription_id'], 'error' => $error['error']]));
            }
        }

        return 0;
    }
}
