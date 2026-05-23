<?php

use Illuminate\Support\Facades\App;

it('wires translation lookups in database backup and restore views', function () {
    $viewsRoot = __DIR__.'/../../resources/views/livewire/project/database';

    $import = file_get_contents($viewsRoot.'/import.blade.php');
    $executions = file_get_contents($viewsRoot.'/backup-executions.blade.php');
    $initScript = file_get_contents($viewsRoot.'/init-script.blade.php');
    $scheduledBackups = file_get_contents($viewsRoot.'/scheduled-backups.blade.php');

    expect($import)
        ->toContain("__('Import Backup')")
        ->toContain("__('Restore Database from File')")
        ->toContain("__('Restore Database from S3')")
        ->and($executions)
        ->toContain("__('Executions')")
        ->toContain("__('Cleanup Failed Backups')")
        ->toContain("__('Backup Availability:')")
        ->and($initScript)
        ->toContain("__('Filename')")
        ->toContain("__('Confirm init-script deletion?')")
        ->and($scheduledBackups)
        ->toContain("__('Select the type of database to enable automated backups.')")
        ->toContain("__('Success Rate:')")
        ->toContain("__('No executions yet')");
});

it('resolves representative database backup and restore translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Import Backup'))->toBe('导入备份')
        ->and(__('Restore Database from File'))->toBe('从文件恢复数据库')
        ->and(__('Restore Database from S3'))->toBe('从 S3 恢复数据库')
        ->and(__('Cleanup Failed Backups'))->toBe('清理失败的备份')
        ->and(__('Backup Availability:'))->toBe('备份可用性：')
        ->and(__('No executions yet'))->toBe('还没有执行记录')
        ->and(__('Success Rate:'))->toBe('成功率：')
        ->and(__('Init-script Name'))->toBe('初始化脚本名称');
});
