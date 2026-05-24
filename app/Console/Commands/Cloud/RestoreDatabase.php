<?php

namespace App\Console\Commands\Cloud;

use Illuminate\Console\Command;

class RestoreDatabase extends Command
{
    protected $signature = 'cloud:restore-database {file : Path to the database dump file} {--debug : Show detailed debug output}';

    protected $description = 'Restore a PostgreSQL database from a dump file (development mode only)';

    private bool $debug = false;

    public function getDescription(): string
    {
        return trans('console.restore_database.description');
    }

    public function handle(): int
    {
        $this->debug = $this->option('debug');

        if (! $this->isDevelopment()) {
            $this->error(trans('console.restore_database.error.development_only'));

            return 1;
        }

        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error(trans('console.restore_database.error.file_not_found', ['file' => $filePath]));

            return 1;
        }

        if (! is_readable($filePath)) {
            $this->error(trans('console.restore_database.error.file_not_readable', ['file' => $filePath]));

            return 1;
        }

        try {
            $this->info(trans('console.restore_database.info.starting'));

            $database = config('database.connections.pgsql.database');
            $host = config('database.connections.pgsql.host');
            $port = config('database.connections.pgsql.port');
            $username = config('database.connections.pgsql.username');
            $password = config('database.connections.pgsql.password');

            if (! $database || ! $username) {
                $this->error(trans('console.restore_database.error.database_configuration_incomplete'));

                return 1;
            }

            $this->info(trans('console.restore_database.info.restoring_to_database', ['database' => $database]));

            // Drop all tables
            if (! $this->dropAllTables($database, $host, $port, $username, $password)) {
                return 1;
            }

            // Restore the database dump
            if (! $this->restoreDatabaseDump($filePath, $database, $host, $port, $username, $password)) {
                return 1;
            }

            $this->info(trans('console.restore_database.info.completed_successfully'));

            return 0;
        } catch (\Exception $e) {
            $this->error(trans('console.restore_database.error.an_error_occurred', ['message' => $e->getMessage()]));

            return 1;
        }
    }

    private function dropAllTables(string $database, string $host, string $port, string $username, string $password): bool
    {
        $this->info(trans('console.restore_database.info.dropping_all_tables'));

        // SQL to drop all tables
        $dropTablesSQL = <<<'SQL'
            DO $$ DECLARE
                r RECORD;
            BEGIN
                FOR r IN (SELECT tablename FROM pg_tables WHERE schemaname = 'public') LOOP
                    EXECUTE 'DROP TABLE IF EXISTS ' || quote_ident(r.tablename) || ' CASCADE';
                END LOOP;
            END $$;
            SQL;

        // Build the psql command to drop all tables
        $command = sprintf(
            'PGPASSWORD=%s psql -h %s -p %s -U %s -d %s -c %s',
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($dropTablesSQL)
        );

        if ($this->debug) {
            $this->line('<comment>'.trans('console.restore_database.debug.executing_drop_command').'</comment>');
            $this->line($command);
        }

        $output = shell_exec($command.' 2>&1');

        if ($this->debug) {
            $this->line('<comment>'.trans('console.restore_database.debug.output')."</comment> {$output}");
        }

        $this->info(trans('console.restore_database.info.all_tables_dropped_successfully'));

        return true;
    }

    private function restoreDatabaseDump(string $filePath, string $database, string $host, string $port, string $username, string $password): bool
    {
        $this->info(trans('console.restore_database.info.restoring_database_from_dump_file'));

        // Handle gzipped files by decompressing first
        $actualFile = $filePath;
        if (str_ends_with($filePath, '.gz')) {
            $actualFile = rtrim($filePath, '.gz');
            $this->info(trans('console.restore_database.info.decompressing_gzipped_dump_file'));

            $decompressCommand = sprintf(
                'gunzip -c %s > %s',
                escapeshellarg($filePath),
                escapeshellarg($actualFile)
            );

            if ($this->debug) {
                $this->line('<comment>'.trans('console.restore_database.debug.executing_decompress_command').'</comment>');
                $this->line($decompressCommand);
            }

            $decompressOutput = shell_exec($decompressCommand.' 2>&1');
            if ($this->debug && $decompressOutput) {
                $this->line('<comment>'.trans('console.restore_database.debug.decompress_output')."</comment> {$decompressOutput}");
            }
        }

        // Use pg_restore for custom format dumps
        $command = sprintf(
            'PGPASSWORD=%s pg_restore -h %s -p %s -U %s -d %s -v %s',
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($actualFile)
        );

        if ($this->debug) {
            $this->line('<comment>'.trans('console.restore_database.debug.executing_restore_command').'</comment>');
            $this->line($command);
        }

        // Execute the restore command
        $process = proc_open(
            $command,
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (! is_resource($process)) {
            $this->error(trans('console.restore_database.error.failed_to_start_restoration_process'));

            return false;
        }

        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        $exitCode = proc_close($process);

        // Clean up decompressed file if we created one
        if ($actualFile !== $filePath && file_exists($actualFile)) {
            unlink($actualFile);
        }

        if ($this->debug) {
            if ($output) {
                $this->line('<comment>'.trans('console.restore_database.debug.output').'</comment>');
                $this->line($output);
            }
            if ($error) {
                $this->line('<comment>'.trans('console.restore_database.debug.error_output').'</comment>');
                $this->line($error);
            }
            $this->line('<comment>'.trans('console.restore_database.debug.exit_code')."</comment> {$exitCode}");
        }

        if ($exitCode !== 0) {
            $this->error(trans('console.restore_database.error.restoration_failed_with_exit_code', ['exit_code' => $exitCode]));
            if ($error) {
                $this->error(trans('console.restore_database.error.error_details'));
                $this->error($error);
            }

            return false;
        }

        if ($output && ! $this->debug) {
            $this->line($output);
        }

        return true;
    }

    private function isDevelopment(): bool
    {
        return app()->environment(['local', 'development', 'dev']);
    }
}
