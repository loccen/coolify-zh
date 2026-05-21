<?php

namespace App\Livewire\Project\Database;

use App\Models\ScheduledDatabaseBackup;
use App\Models\ScheduledDatabaseBackupExecution;
use App\Models\ServiceDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BackupExecutions extends Component
{
    public ?ScheduledDatabaseBackup $backup = null;

    public $database;

    public ?Collection $executions;

    public int $executions_count = 0;

    public int $skip = 0;

    public int $defaultTake = 10;

    public bool $showNext = false;

    public bool $showPrev = false;

    public int $currentPage = 1;

    public $setDeletableBackup;

    public $delete_backup_s3 = false;

    public $delete_backup_sftp = false;

    public function getListeners()
    {
        $userId = Auth::id();

        return [
            "echo-private:team.{$userId},BackupCreated" => 'refreshBackupExecutions',
        ];
    }

    public function cleanupFailed()
    {
        if ($this->backup) {
            $this->backup->executions()->where('status', 'failed')->delete();
            $this->refreshBackupExecutions();
            $this->dispatch('success', 'Failed backups cleaned up.');
        }
    }

    public function cleanupDeleted()
    {
        if ($this->backup) {
            $deletedCount = $this->backup->executions()->where('local_storage_deleted', true)->count();
            if ($deletedCount > 0) {
                $this->backup->executions()->where('local_storage_deleted', true)->delete();
                $this->refreshBackupExecutions();
                $this->dispatch('success', "Cleaned up {$deletedCount} backup entries deleted from local storage.");
            } else {
                $this->dispatch('info', 'No backup entries found that are deleted from local storage.');
            }
        }
    }

    public function deleteBackup($executionId, $password, $selectedActions = [])
    {
        if (! verifyPasswordConfirmation($password, $this)) {
            return 'The provided password is incorrect.';
        }

        $execution = $this->backup->executions()->where('id', $executionId)->first();
        if (is_null($execution)) {
            $this->dispatch('error', 'Backup execution not found.');

            return;
        }

        $server = $execution->scheduledDatabaseBackup->database->getMorphClass() === ServiceDatabase::class
            ? $execution->scheduledDatabaseBackup->database->service->destination->server
            : $execution->scheduledDatabaseBackup->database->destination->server;

        try {
            if ($execution->filename) {
                deleteBackupsLocally($execution->filename, $server);

                if ($this->delete_backup_s3 && $execution->scheduledDatabaseBackup->s3) {
                    deleteBackupsS3($execution->filename, $execution->scheduledDatabaseBackup->s3);
                }
            }

            $execution->delete();
            $this->dispatch('success', 'Backup deleted.');
            $this->refreshBackupExecutions();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to delete backup: '.$e->getMessage());

            return true;
        }

        return true;
    }

    public function restoreLocalBackup($executionId, $password)
    {
        if (! verifyPasswordConfirmation($password, $this)) {
            return 'The provided password is incorrect.';
        }

        try {
            $execution = $this->resolveInstanceRestoreExecution($executionId);
            if (data_get($execution, 'local_storage_deleted', false) || blank($execution->filename)) {
                $this->dispatch('error', __('settings.backup_page.restore_not_available'));

                return true;
            }

            $server = $this->server();
            if (! $server) {
                $this->dispatch('error', __('settings.backup_page.restore_server_missing'));

                return true;
            }

            $this->ensureRestoreScriptExists($server);
            $scriptPath = escapeshellarg($this->restoreScriptPath());
            $packagePath = escapeshellarg($execution->filename);
            $activity = remote_process(["bash {$scriptPath} --package {$packagePath}"], $server, ignore_errors: true);

            $this->dispatch('activityMonitor', $activity->id);
            $this->dispatch('instancerestore');
            $this->dispatch('info', __('settings.backup_page.restore_started_local'));
        } catch (\Throwable $e) {
            $this->dispatch('error', $e->getMessage());
        }

        return true;
    }

    public function restoreS3Backup($executionId, $password)
    {
        if (! verifyPasswordConfirmation($password, $this)) {
            return 'The provided password is incorrect.';
        }

        try {
            $execution = $this->resolveInstanceRestoreExecution($executionId);
            if (data_get($execution, 's3_uploaded') !== true || data_get($execution, 's3_storage_deleted', false)) {
                $this->dispatch('error', __('settings.backup_page.restore_s3_not_available'));

                return true;
            }

            $server = $this->server();
            if (! $server) {
                $this->dispatch('error', __('settings.backup_page.restore_server_missing'));

                return true;
            }

            $storage = $execution->scheduledDatabaseBackup->s3;
            if (! $storage) {
                $this->dispatch('error', __('settings.backup_page.restore_s3_storage_missing'));

                return true;
            }

            $this->ensureRestoreScriptExists($server);
            $envFile = $this->writeS3RestoreEnvFile($server, $storage, $execution);
            $objectKey = escapeshellarg($this->restoreObjectKey($execution));
            $helperImage = escapeshellarg(config('constants.coolify.helper_image').':'.getHelperVersion());
            $scriptPath = escapeshellarg($this->restoreScriptPath());
            $envFileArg = escapeshellarg($envFile);

            $activity = remote_process([
                "bash {$scriptPath} --s3-env-file {$envFileArg} --s3-object-key {$objectKey} --helper-image {$helperImage}",
            ], $server, ignore_errors: true);

            $this->dispatch('activityMonitor', $activity->id);
            $this->dispatch('instancerestore');
            $this->dispatch('info', __('settings.backup_page.restore_started_s3'));
        } catch (\Throwable $e) {
            $this->dispatch('error', $e->getMessage());
        }

        return true;
    }

    public function download_file($exeuctionId)
    {
        return redirect()->route('download.backup', $exeuctionId);
    }

    public function refreshBackupExecutions(): void
    {
        $this->loadExecutions();
    }

    public function reloadExecutions()
    {
        $this->loadExecutions();
    }

    public function previousPage(?int $take = null)
    {
        if ($take) {
            $this->skip = $this->skip - $take;
        }
        $this->skip = $this->skip - $this->defaultTake;
        if ($this->skip < 0) {
            $this->showPrev = false;
            $this->skip = 0;
        }
        $this->updateCurrentPage();
        $this->loadExecutions();
    }

    public function nextPage(?int $take = null)
    {
        if ($take) {
            $this->skip = $this->skip + $take;
        }
        $this->showPrev = true;
        $this->updateCurrentPage();
        $this->loadExecutions();
    }

    private function loadExecutions()
    {
        if ($this->backup && $this->backup->exists) {
            ['executions' => $executions, 'count' => $count] = $this->backup->executionsPaginated($this->skip, $this->defaultTake);
            $this->executions = $executions;
            $this->executions_count = $count;
        } else {
            $this->executions = collect([]);
            $this->executions_count = 0;
        }
        $this->showMore();
    }

    private function showMore()
    {
        if ($this->executions->count() !== 0) {
            $this->showNext = true;
            if ($this->executions->count() < $this->defaultTake) {
                $this->showNext = false;
            }

            return;
        }
    }

    private function updateCurrentPage()
    {
        $this->currentPage = intval($this->skip / $this->defaultTake) + 1;
    }

    public function mount(ScheduledDatabaseBackup $backup)
    {
        $this->backup = $backup;
        $this->database = $backup->database;
        $this->updateCurrentPage();
        $this->loadExecutions();
    }

    public function server()
    {
        if ($this->database) {
            $server = null;

            if ($this->database instanceof ServiceDatabase) {
                $server = $this->database->service->destination->server;
            } elseif ($this->database->destination && $this->database->destination->server) {
                $server = $this->database->destination->server;
            }
            if ($server) {
                return $server;
            }
        }

        return null;
    }

    private function resolveInstanceRestoreExecution(int $executionId): ScheduledDatabaseBackupExecution
    {
        if (! isInstanceAdmin()) {
            throw new \RuntimeException(__('settings.backup_page.restore_requires_instance_admin'));
        }

        if (! $this->backup || $this->backup->database_id !== 0) {
            throw new \RuntimeException(__('settings.backup_page.restore_not_supported'));
        }

        $execution = $this->backup->executions()->where('id', $executionId)->first();
        if (! $execution) {
            throw new \RuntimeException(__('settings.backup_page.restore_execution_missing'));
        }

        if (! $execution->is_instance_restore_package) {
            throw new \RuntimeException(__('settings.backup_page.restore_not_supported'));
        }

        if ($execution->status !== 'success') {
            throw new \RuntimeException(__('settings.backup_page.restore_not_available'));
        }

        return $execution;
    }

    private function restoreScriptPath(): string
    {
        return '/data/coolify/bin/restore-coolify-instance.sh';
    }

    private function ensureRestoreScriptExists($server): void
    {
        $script = escapeshellarg($this->restoreScriptPath());
        $exists = instant_remote_process(["test -x {$script} && echo OK || echo NOK"], $server, throwError: false);

        if (trim((string) $exists) !== 'OK') {
            throw new \RuntimeException(__('settings.backup_page.restore_script_missing'));
        }
    }

    private function restoreObjectKey(ScheduledDatabaseBackupExecution $execution): string
    {
        $filename = ltrim((string) $execution->filename, '/');
        if ($filename === '' || preg_match('/^[a-zA-Z0-9._\\/-]+$/', $filename) !== 1) {
            throw new \RuntimeException(__('settings.backup_page.restore_s3_key_invalid'));
        }

        return $filename;
    }

    private function writeS3RestoreEnvFile($server, $storage, ScheduledDatabaseBackupExecution $execution): string
    {
        $envPath = "/tmp/coolify-instance-restore-{$execution->id}.env";
        $envPayload = implode("\n", [
            'S3_ENDPOINT='.$storage->endpoint,
            'S3_ACCESS_KEY='.$storage->key,
            'S3_SECRET_KEY='.$storage->secret,
            'S3_BUCKET='.$storage->bucket,
            '',
        ]);
        $encodedPayload = base64_encode($envPayload);
        $escapedPath = escapeshellarg($envPath);
        $escapedPayload = escapeshellarg($encodedPayload);

        instant_remote_process([
            "echo {$escapedPayload} | base64 -d > {$escapedPath}",
            "chmod 600 {$escapedPath}",
        ], $server);

        return $envPath;
    }

    public function render()
    {
        return view('livewire.project.database.backup-executions');
    }
}
