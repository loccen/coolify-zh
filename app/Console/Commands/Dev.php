<?php

namespace App\Console\Commands;

use App\Jobs\CheckHelperImageJob;
use App\Models\InstanceSettings;
use App\Models\ScheduledDatabaseBackupExecution;
use App\Models\ScheduledTaskExecution;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Dev extends Command
{
    protected $signature = 'dev {--init}';

    protected $description = '';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.dev.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        if ($this->option('init')) {
            $this->init();

            return;
        }
    }

    public function init()
    {
        $locale = app()->getLocale();

        // Generate APP_KEY if not exists

        if (empty(config('app.key'))) {
            echo '   INFO  '.trans('console.dev.info.generating_app_key', locale: $locale)."\n";
            Artisan::call('key:generate');
        }

        // Generate STORAGE link if not exists
        if (! file_exists(public_path('storage'))) {
            echo '   INFO  '.trans('console.dev.info.generating_storage_link', locale: $locale)."\n";
            Artisan::call('storage:link');
        }

        // Seed database if it's empty
        $settings = InstanceSettings::find(0);
        if (! $settings) {
            echo '   INFO  '.trans('console.dev.info.initializing_instance', locale: $locale)."\n";
            Artisan::call('migrate --seed');
        } else {
            echo '   INFO  '.trans('console.dev.info.instance_already_initialized', locale: $locale)."\n";
        }

        // Clean up stuck jobs and stale locks on development startup
        try {
            echo '   INFO  '.trans('console.dev.info.cleaning_up_redis', locale: $locale)."\n";
            Artisan::call('cleanup:redis', ['--restart' => true, '--clear-locks' => true]);
            echo '   INFO  '.trans('console.dev.info.redis_cleanup_completed', locale: $locale)."\n";
        } catch (\Throwable $e) {
            echo "   ERROR  Redis cleanup failed: {$e->getMessage()}\n";
        }

        try {
            $updatedTaskCount = ScheduledTaskExecution::where('status', 'running')->update([
                'status' => 'failed',
                'message' => 'Marked as failed during Coolify startup - job was interrupted',
                'finished_at' => Carbon::now(),
            ]);

            if ($updatedTaskCount > 0) {
                echo "   INFO  Marked {$updatedTaskCount} stuck scheduled task executions as failed.\n";
            }
        } catch (\Throwable $e) {
            echo "   ERROR  Could not clean up stuck scheduled task executions: {$e->getMessage()}\n";
        }

        try {
            $updatedBackupCount = ScheduledDatabaseBackupExecution::where('status', 'running')->update([
                'status' => 'failed',
                'message' => 'Marked as failed during Coolify startup - job was interrupted',
                'finished_at' => Carbon::now(),
            ]);

            if ($updatedBackupCount > 0) {
                echo "   INFO  Marked {$updatedBackupCount} stuck database backup executions as failed.\n";
            }
        } catch (\Throwable $e) {
            echo "   ERROR  Could not clean up stuck database backup executions: {$e->getMessage()}\n";
        }

        CheckHelperImageJob::dispatch();
    }
}
