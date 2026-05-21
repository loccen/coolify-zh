<?php

use App\Livewire\Project\Database\BackupExecutions;

test('instance restore host script exists with rollback and health checks', function () {
    $script = file_get_contents(base_path('scripts/restore-coolify-instance.sh'));

    expect($script)->toContain('rollback_restore');
    expect($script)->toContain('wait_for_coolify_health');
    expect($script)->toContain('verify_encrypted_settings');
    expect($script)->toContain('APP_PREVIOUS_KEYS');
    expect($script)->toContain('restore_database_from_dump');
});

test('instance backup executions component wires local and s3 restore through host script', function () {
    $source = file_get_contents(app_path('Livewire/Project/Database/BackupExecutions.php'));
    $reflection = new ReflectionClass(BackupExecutions::class);

    expect($reflection->hasMethod('restoreLocalBackup'))->toBeTrue();
    expect($reflection->hasMethod('restoreS3Backup'))->toBeTrue();
    expect($reflection->hasMethod('buildDetachedRestoreCommand'))->toBeTrue();
    expect($source)->toContain('/data/coolify/bin/restore-coolify-instance.sh');
    expect($source)->toContain('--s3-env-file');
    expect($source)->toContain('--helper-image');
    expect($source)->toContain('nohup sh -lc');
    expect($source)->toContain('/data/coolify/source/restore-instance-');
    expect($source)->toContain("dispatch('instancerestore')");
});

test('settings backup page exposes instance restore activity monitor', function () {
    $view = file_get_contents(resource_path('views/livewire/settings-backup.blade.php'));

    expect($view)->toContain('@instancerestore.window');
    expect($view)->toContain('instance-restore-activity-monitor');
});
