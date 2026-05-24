<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDatabase extends Command
{
    protected $signature = 'cleanup:database {--yes} {--keep-days=}';

    protected $description = 'Cleanup database.';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.cleanup_database.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        if ($this->option('yes')) {
            $this->info(trans('console.cleanup_database.running'));
        } else {
            $this->info(trans('console.cleanup_database.running_dry_run'));
        }
        $keep_days = $this->option('keep-days') ?? 60;

        $this->info(trans('console.cleanup_database.keep_days', ['days' => $keep_days], locale: app()->getLocale()));
        // Cleanup failed jobs table
        $failed_jobs = DB::table('failed_jobs')->where('failed_at', '<', now()->subDays(1));
        $count = $failed_jobs->count();
        $this->info(trans('console.cleanup_database.delete_failed_jobs', ['count' => $count], locale: app()->getLocale()));
        if ($this->option('yes')) {
            $failed_jobs->delete();
        }

        // Cleanup sessions table
        $sessions = DB::table('sessions')->where('last_activity', '<', now()->subDays($keep_days)->timestamp);
        $count = $sessions->count();
        $this->info(trans('console.cleanup_database.delete_sessions', ['count' => $count], locale: app()->getLocale()));
        if ($this->option('yes')) {
            $sessions->delete();
        }

        // Cleanup activity_log table
        $activity_log = DB::table('activity_log')->where('created_at', '<', now()->subDays($keep_days))->orderBy('created_at', 'desc')->skip(10);
        $count = $activity_log->count();
        $this->info(trans('console.cleanup_database.delete_activity_log', ['count' => $count], locale: app()->getLocale()));
        if ($this->option('yes')) {
            $activity_log->delete();
        }

        // Cleanup application_deployment_queues table
        $application_deployment_queues = DB::table('application_deployment_queues')->where('created_at', '<', now()->subDays($keep_days))->orderBy('created_at', 'desc')->skip(10);
        $count = $application_deployment_queues->count();
        $this->info(trans('console.cleanup_database.delete_application_deployment_queues', ['count' => $count], locale: app()->getLocale()));
        if ($this->option('yes')) {
            $application_deployment_queues->delete();
        }

        // Cleanup scheduled_task_executions table
        $scheduled_task_executions = DB::table('scheduled_task_executions')->where('created_at', '<', now()->subDays($keep_days))->orderBy('created_at', 'desc');
        $count = $scheduled_task_executions->count();
        $this->info(trans('console.cleanup_database.delete_scheduled_task_executions', ['count' => $count], locale: app()->getLocale()));
        if ($this->option('yes')) {
            $scheduled_task_executions->delete();
        }
    }
}
