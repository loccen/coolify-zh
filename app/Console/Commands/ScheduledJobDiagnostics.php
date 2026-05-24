<?php

namespace App\Console\Commands;

use App\Models\DockerCleanupExecution;
use App\Models\ScheduledDatabaseBackup;
use App\Models\ScheduledTask;
use App\Models\Server;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ScheduledJobDiagnostics extends Command
{
    protected $signature = 'scheduled:diagnostics
        {--type=all : Type to inspect: docker-cleanup, backups, tasks, server-jobs, all}
        {--server= : Filter by server ID}';

    protected $description = 'Inspect dedup cache state and scheduling decisions for all scheduled jobs';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.scheduled_job_diagnostics.description', locale: app()->getLocale()));
    }

    public function handle(): int
    {
        $type = $this->option('type');
        $serverFilter = $this->option('server');

        $this->outputHeartbeat();

        if (in_array($type, ['all', 'docker-cleanup'])) {
            $this->inspectDockerCleanups($serverFilter);
        }

        if (in_array($type, ['all', 'backups'])) {
            $this->inspectBackups();
        }

        if (in_array($type, ['all', 'tasks'])) {
            $this->inspectTasks();
        }

        if (in_array($type, ['all', 'server-jobs'])) {
            $this->inspectServerJobs($serverFilter);
        }

        return self::SUCCESS;
    }

    private function translation(string $key, array $replace = []): string
    {
        return trans('console.scheduled_job_diagnostics.'.$key, $replace, locale: app()->getLocale());
    }

    private function outputHeartbeat(): void
    {
        $heartbeat = Cache::get('scheduled-job-manager:heartbeat');
        if ($heartbeat) {
            $age = Carbon::parse($heartbeat)->diffForHumans();
            $this->info($this->translation('heartbeat', ['heartbeat' => $heartbeat, 'age' => $age]));
        } else {
            $this->error($this->translation('heartbeat_missing'));
        }
        $this->newLine();
    }

    private function inspectDockerCleanups(?string $serverFilter): void
    {
        $this->info($this->translation('sections.docker_cleanup'));

        $servers = $this->getServers($serverFilter);

        $rows = [];
        foreach ($servers as $server) {
            $frequency = data_get($server->settings, 'docker_cleanup_frequency', '0 * * * *');
            if (isset(VALID_CRON_STRINGS[$frequency])) {
                $frequency = VALID_CRON_STRINGS[$frequency];
            }

            $dedupKey = "docker-cleanup:{$server->id}";
            $cacheValue = Cache::get($dedupKey);
            $timezone = data_get($server->settings, 'server_timezone', config('app.timezone'));

            if (validate_timezone($timezone) === false) {
                $timezone = config('app.timezone');
            }

            $wouldFire = shouldRunCronNow($frequency, $timezone, $dedupKey);

            $lastExecution = DockerCleanupExecution::where('server_id', $server->id)
                ->latest()
                ->first();

            $rows[] = [
                $server->id,
                $server->name,
                $timezone,
                $frequency,
                $dedupKey,
                $cacheValue ?? $this->translation('values.missing'),
                $wouldFire ? $this->translation('values.yes') : $this->translation('values.no'),
                $lastExecution ? $lastExecution->status.' @ '.$lastExecution->created_at : $this->translation('values.never'),
            ];
        }

        $this->table(
            [
                $this->translation('headers.docker_cleanup.id'),
                $this->translation('headers.docker_cleanup.server'),
                $this->translation('headers.docker_cleanup.timezone'),
                $this->translation('headers.docker_cleanup.frequency'),
                $this->translation('headers.docker_cleanup.dedup_key'),
                $this->translation('headers.docker_cleanup.cache_value'),
                $this->translation('headers.docker_cleanup.would_fire'),
                $this->translation('headers.docker_cleanup.last_execution'),
            ],
            $rows
        );
        $this->newLine();
    }

    private function inspectBackups(): void
    {
        $this->info($this->translation('sections.scheduled_backups'));

        $backups = ScheduledDatabaseBackup::with(['database'])
            ->where('enabled', true)
            ->get();

        $rows = [];
        foreach ($backups as $backup) {
            $server = $backup->server();
            $frequency = $backup->frequency;
            if (isset(VALID_CRON_STRINGS[$frequency])) {
                $frequency = VALID_CRON_STRINGS[$frequency];
            }

            $dedupKey = "scheduled-backup:{$backup->id}";
            $cacheValue = Cache::get($dedupKey);
            $timezone = $server ? data_get($server->settings, 'server_timezone', config('app.timezone')) : config('app.timezone');

            if (validate_timezone($timezone) === false) {
                $timezone = config('app.timezone');
            }

            $wouldFire = shouldRunCronNow($frequency, $timezone, $dedupKey);

            $rows[] = [
                $backup->id,
                $backup->database_type ?? $this->translation('values.unknown'),
                $server?->name ?? $this->translation('values.not_applicable'),
                $frequency,
                $cacheValue ?? $this->translation('values.missing'),
                $wouldFire ? $this->translation('values.yes') : $this->translation('values.no'),
            ];
        }

        $this->table(
            [
                $this->translation('headers.scheduled_backups.backup_id'),
                $this->translation('headers.scheduled_backups.database_type'),
                $this->translation('headers.scheduled_backups.server'),
                $this->translation('headers.scheduled_backups.frequency'),
                $this->translation('headers.scheduled_backups.cache_value'),
                $this->translation('headers.scheduled_backups.would_fire'),
            ],
            $rows
        );
        $this->newLine();
    }

    private function inspectTasks(): void
    {
        $this->info($this->translation('sections.scheduled_tasks'));

        $tasks = ScheduledTask::with(['service', 'application'])
            ->where('enabled', true)
            ->get();

        $rows = [];
        foreach ($tasks as $task) {
            $server = $task->server();
            $frequency = $task->frequency;
            if (isset(VALID_CRON_STRINGS[$frequency])) {
                $frequency = VALID_CRON_STRINGS[$frequency];
            }

            $dedupKey = "scheduled-task:{$task->id}";
            $cacheValue = Cache::get($dedupKey);
            $timezone = $server ? data_get($server->settings, 'server_timezone', config('app.timezone')) : config('app.timezone');

            if (validate_timezone($timezone) === false) {
                $timezone = config('app.timezone');
            }

            $wouldFire = shouldRunCronNow($frequency, $timezone, $dedupKey);

            $rows[] = [
                $task->id,
                $task->name,
                $server?->name ?? $this->translation('values.not_applicable'),
                $frequency,
                $cacheValue ?? $this->translation('values.missing'),
                $wouldFire ? $this->translation('values.yes') : $this->translation('values.no'),
            ];
        }

        $this->table(
            [
                $this->translation('headers.scheduled_tasks.task_id'),
                $this->translation('headers.scheduled_tasks.name'),
                $this->translation('headers.scheduled_tasks.server'),
                $this->translation('headers.scheduled_tasks.frequency'),
                $this->translation('headers.scheduled_tasks.cache_value'),
                $this->translation('headers.scheduled_tasks.would_fire'),
            ],
            $rows
        );
        $this->newLine();
    }

    private function inspectServerJobs(?string $serverFilter): void
    {
        $this->info($this->translation('sections.server_manager_jobs'));

        $servers = $this->getServers($serverFilter);

        $rows = [];
        foreach ($servers as $server) {
            $timezone = data_get($server->settings, 'server_timezone', config('app.timezone'));
            if (validate_timezone($timezone) === false) {
                $timezone = config('app.timezone');
            }

            $dedupKeys = [
                "sentinel-restart:{$server->id}" => '0 0 * * *',
                "server-patch-check:{$server->id}" => '0 0 * * 0',
                "server-check:{$server->id}" => isCloud() ? '*/5 * * * *' : '* * * * *',
                "server-storage-check:{$server->id}" => data_get($server->settings, 'server_disk_usage_check_frequency', '0 23 * * *'),
            ];

            foreach ($dedupKeys as $dedupKey => $frequency) {
                if (isset(VALID_CRON_STRINGS[$frequency])) {
                    $frequency = VALID_CRON_STRINGS[$frequency];
                }

                $cacheValue = Cache::get($dedupKey);
                $wouldFire = shouldRunCronNow($frequency, $timezone, $dedupKey);

                $rows[] = [
                    $server->id,
                    $server->name,
                    $dedupKey,
                    $frequency,
                    $cacheValue ?? $this->translation('values.missing'),
                    $wouldFire ? $this->translation('values.yes') : $this->translation('values.no'),
                ];
            }
        }

        $this->table(
            [
                $this->translation('headers.server_manager_jobs.server_id'),
                $this->translation('headers.server_manager_jobs.server'),
                $this->translation('headers.server_manager_jobs.dedup_key'),
                $this->translation('headers.server_manager_jobs.frequency'),
                $this->translation('headers.server_manager_jobs.cache_value'),
                $this->translation('headers.server_manager_jobs.would_fire'),
            ],
            $rows
        );
        $this->newLine();
    }

    private function getServers(?string $serverFilter): Collection
    {
        $query = Server::with('settings')->where('ip', '!=', '1.2.3.4');

        if ($serverFilter) {
            $query->where('id', $serverFilter);
        }

        if (isCloud()) {
            $servers = $query->whereRelation('team.subscription', 'stripe_invoice_paid', true)->get();
            $own = Team::find(0)?->servers()->with('settings')->get() ?? collect();

            return $servers->merge($own);
        }

        return $query->get();
    }
}
