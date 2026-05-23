<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewScheduledLogs extends Command
{
    protected $signature = 'logs:scheduled 
                            {--lines=50 : Number of lines to display}
                            {--follow : Follow the log file (tail -f)}
                            {--date= : Specific date (Y-m-d format, defaults to today)}
                            {--task-name= : Filter by task name (partial match)}
                            {--task-id= : Filter by task ID}
                            {--backup-name= : Filter by backup name (partial match)}
                            {--backup-id= : Filter by backup ID}
                            {--errors : View error logs only}
                            {--all : View both normal and error logs}
                            {--hourly : Filter hourly jobs}
                            {--daily : Filter daily jobs}
                            {--weekly : Filter weekly jobs}
                            {--monthly : Filter monthly jobs}
                            {--frequency= : Filter by specific cron expression}';

    protected $description = '';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.scheduled_logs.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        $locale = app()->getLocale();
        $date = $this->option('date') ?: now()->format('Y-m-d');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error(trans('console.scheduled_logs.error.invalid_date_format', locale: $locale));

            return self::INVALID;
        }
        $logPaths = $this->getLogPaths($date);

        if (empty($logPaths)) {
            $this->showAvailableLogFiles($date);

            return;
        }

        $lines = $this->option('lines');
        $follow = $this->option('follow');

        // Build grep filters
        $filters = $this->buildFilters();
        $filterDescription = $this->getFilterDescription();
        $logTypeDescription = $this->getLogTypeDescription();

        if ($follow) {
            $this->info(trans('console.scheduled_logs.info.following_logs', [
                'type' => $logTypeDescription,
                'date' => $date,
                'filter' => $filterDescription,
            ], locale: $locale));
            $this->line('');

            if (count($logPaths) === 1) {
                $logPath = escapeshellarg($logPaths[0]);
                if ($filters) {
                    $escapedFilters = escapeshellarg($filters);
                    passthru("tail -f {$logPath} | grep -E {$escapedFilters}");
                } else {
                    passthru("tail -f {$logPath}");
                }
            } else {
                // Multiple files - use multitail or tail with process substitution
                $logPathsStr = implode(' ', array_map('escapeshellarg', $logPaths));
                if ($filters) {
                    $escapedFilters = escapeshellarg($filters);
                    passthru("tail -f {$logPathsStr} | grep -E {$escapedFilters}");
                } else {
                    passthru("tail -f {$logPathsStr}");
                }
            }
        } else {
            $this->info(trans('console.scheduled_logs.info.showing_last_lines', [
                'lines' => $lines,
                'type' => $logTypeDescription,
                'date' => $date,
                'filter' => $filterDescription,
            ], locale: $locale));
            $this->line('');

            $escapedLines = escapeshellarg((string) $lines);
            if (count($logPaths) === 1) {
                $logPath = escapeshellarg($logPaths[0]);
                if ($filters) {
                    $escapedFilters = escapeshellarg($filters);
                    passthru("tail -n {$escapedLines} {$logPath} | grep -E {$escapedFilters}");
                } else {
                    passthru("tail -n {$escapedLines} {$logPath}");
                }
            } else {
                // Multiple files - concatenate and sort by timestamp
                $logPathsStr = implode(' ', array_map('escapeshellarg', $logPaths));
                if ($filters) {
                    $escapedFilters = escapeshellarg($filters);
                    passthru("tail -n {$escapedLines} {$logPathsStr} | sort | grep -E {$escapedFilters}");
                } else {
                    passthru("tail -n {$escapedLines} {$logPathsStr} | sort");
                }
            }
        }
    }

    private function getLogPaths(string $date): array
    {
        $paths = [];

        if ($this->option('errors')) {
            // Error logs only
            $errorPath = storage_path("logs/scheduled-errors-{$date}.log");
            if (File::exists($errorPath)) {
                $paths[] = $errorPath;
            }
        } elseif ($this->option('all')) {
            // Both normal and error logs
            $normalPath = storage_path("logs/scheduled-{$date}.log");
            $errorPath = storage_path("logs/scheduled-errors-{$date}.log");

            if (File::exists($normalPath)) {
                $paths[] = $normalPath;
            }
            if (File::exists($errorPath)) {
                $paths[] = $errorPath;
            }
        } else {
            // Normal logs only (default)
            $normalPath = storage_path("logs/scheduled-{$date}.log");
            if (File::exists($normalPath)) {
                $paths[] = $normalPath;
            }
        }

        return $paths;
    }

    private function showAvailableLogFiles(string $date): void
    {
        $locale = app()->getLocale();
        $logType = $this->getLogTypeDescription();
        $this->warn(trans('console.scheduled_logs.warn.no_logs_found', [
            'type' => $logType,
            'date' => $date,
        ], locale: $locale));

        // Show available log files
        $normalFiles = File::glob(storage_path('logs/scheduled-*.log'));
        $errorFiles = File::glob(storage_path('logs/scheduled-errors-*.log'));

        if (! empty($normalFiles) || ! empty($errorFiles)) {
            $this->info(trans('console.scheduled_logs.info.available_log_files', locale: $locale));

            if (! empty($normalFiles)) {
                $this->line(trans('console.scheduled_logs.info.normal_logs', locale: $locale));
                foreach ($normalFiles as $file) {
                    $basename = basename($file);
                    $this->line("    - {$basename}");
                }
            }

            if (! empty($errorFiles)) {
                $this->line(trans('console.scheduled_logs.info.error_logs', locale: $locale));
                foreach ($errorFiles as $file) {
                    $basename = basename($file);
                    $this->line("    - {$basename}");
                }
            }
        }
    }

    private function getLogTypeDescription(): string
    {
        if ($this->option('errors')) {
            return trans('console.scheduled_logs.types.error', locale: app()->getLocale());
        } elseif ($this->option('all')) {
            return trans('console.scheduled_logs.types.all', locale: app()->getLocale());
        } else {
            return trans('console.scheduled_logs.types.normal', locale: app()->getLocale());
        }
    }

    private function buildFilters(): ?string
    {
        $filters = [];

        if ($taskName = $this->option('task-name')) {
            $filters[] = '"task_name":"[^"]*'.preg_quote($taskName, '/').'[^"]*"';
        }

        if ($taskId = $this->option('task-id')) {
            $filters[] = '"task_id":'.preg_quote($taskId, '/');
        }

        if ($backupName = $this->option('backup-name')) {
            $filters[] = '"backup_name":"[^"]*'.preg_quote($backupName, '/').'[^"]*"';
        }

        if ($backupId = $this->option('backup-id')) {
            $filters[] = '"backup_id":'.preg_quote($backupId, '/');
        }

        // Frequency filters
        if ($this->option('hourly')) {
            $filters[] = $this->getFrequencyPattern('hourly');
        }

        if ($this->option('daily')) {
            $filters[] = $this->getFrequencyPattern('daily');
        }

        if ($this->option('weekly')) {
            $filters[] = $this->getFrequencyPattern('weekly');
        }

        if ($this->option('monthly')) {
            $filters[] = $this->getFrequencyPattern('monthly');
        }

        if ($frequency = $this->option('frequency')) {
            $filters[] = '"frequency":"'.preg_quote($frequency, '/').'"';
        }

        return empty($filters) ? null : implode('|', $filters);
    }

    private function getFrequencyPattern(string $type): string
    {
        $patterns = [
            'hourly' => [
                '0 \* \* \* \*',     // 0 * * * *
                '@hourly',           // @hourly
            ],
            'daily' => [
                '0 0 \* \* \*',      // 0 0 * * *
                '@daily',            // @daily
                '@midnight',         // @midnight
            ],
            'weekly' => [
                '0 0 \* \* [0-6]',   // 0 0 * * 0-6 (any day of week)
                '@weekly',           // @weekly
            ],
            'monthly' => [
                '0 0 1 \* \*',       // 0 0 1 * * (first of month)
                '@monthly',          // @monthly
            ],
        ];

        $typePatterns = $patterns[$type] ?? [];

        // For grep, we need to match the frequency field in JSON
        return '"frequency":"('.implode('|', $typePatterns).')"';
    }

    private function getFilterDescription(): string
    {
        $descriptions = [];

        if ($taskName = $this->option('task-name')) {
            $descriptions[] = "task name: {$taskName}";
        }

        if ($taskId = $this->option('task-id')) {
            $descriptions[] = "task ID: {$taskId}";
        }

        if ($backupName = $this->option('backup-name')) {
            $descriptions[] = "backup name: {$backupName}";
        }

        if ($backupId = $this->option('backup-id')) {
            $descriptions[] = "backup ID: {$backupId}";
        }

        // Frequency filters
        if ($this->option('hourly')) {
            $descriptions[] = 'hourly jobs';
        }

        if ($this->option('daily')) {
            $descriptions[] = 'daily jobs';
        }

        if ($this->option('weekly')) {
            $descriptions[] = 'weekly jobs';
        }

        if ($this->option('monthly')) {
            $descriptions[] = 'monthly jobs';
        }

        if ($frequency = $this->option('frequency')) {
            $descriptions[] = "frequency: {$frequency}";
        }

        return empty($descriptions) ? '' : ' (filtered by '.implode(', ', $descriptions).')';
    }
}
