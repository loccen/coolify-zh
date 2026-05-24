<?php

use App\Console\Commands\CleanupDatabase;
use Illuminate\Support\Facades\App;

it('wires cleanup database console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupDatabase.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.cleanup_database.description'")
        ->toContain("trans('console.cleanup_database.running'")
        ->toContain("trans('console.cleanup_database.running_dry_run'")
        ->toContain("trans('console.cleanup_database.keep_days'")
        ->toContain("trans('console.cleanup_database.delete_failed_jobs'")
        ->toContain("trans('console.cleanup_database.delete_sessions'")
        ->toContain("trans('console.cleanup_database.delete_activity_log'")
        ->toContain("trans('console.cleanup_database.delete_application_deployment_queues'")
        ->toContain("trans('console.cleanup_database.delete_scheduled_task_executions'");

    expect($enTranslations['cleanup_database']['description'])->toBe('Cleanup database.')
        ->and($enTranslations['cleanup_database']['running'])->toBe('Running database cleanup...')
        ->and($enTranslations['cleanup_database']['running_dry_run'])->toBe('Running database cleanup in dry-run mode...')
        ->and($enTranslations['cleanup_database']['keep_days'])->toBe('Keep days: :days')
        ->and($enTranslations['cleanup_database']['delete_failed_jobs'])->toBe('Delete :count entries from failed_jobs.')
        ->and($enTranslations['cleanup_database']['delete_sessions'])->toBe('Delete :count entries from sessions.')
        ->and($enTranslations['cleanup_database']['delete_activity_log'])->toBe('Delete :count entries from activity_log.')
        ->and($enTranslations['cleanup_database']['delete_application_deployment_queues'])->toBe('Delete :count entries from application_deployment_queues.')
        ->and($enTranslations['cleanup_database']['delete_scheduled_task_executions'])->toBe('Delete :count entries from scheduled_task_executions.');

    expect($zhTranslations['cleanup_database']['description'])->toBe('清理数据库。')
        ->and($zhTranslations['cleanup_database']['running'])->toBe('正在清理数据库...')
        ->and($zhTranslations['cleanup_database']['running_dry_run'])->toBe('正在以 dry-run 模式清理数据库...')
        ->and($zhTranslations['cleanup_database']['keep_days'])->toBe('保留天数：:days')
        ->and($zhTranslations['cleanup_database']['delete_failed_jobs'])->toBe('删除 failed_jobs 中的 :count 条记录。')
        ->and($zhTranslations['cleanup_database']['delete_sessions'])->toBe('删除 sessions 中的 :count 条记录。')
        ->and($zhTranslations['cleanup_database']['delete_activity_log'])->toBe('删除 activity_log 中的 :count 条记录。')
        ->and($zhTranslations['cleanup_database']['delete_application_deployment_queues'])->toBe('删除 application_deployment_queues 中的 :count 条记录。')
        ->and($zhTranslations['cleanup_database']['delete_scheduled_task_executions'])->toBe('删除 scheduled_task_executions 中的 :count 条记录。');
});

it('resolves cleanup database description in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_database.running'))->toBe('Running database cleanup...')
        ->and(trans('console.cleanup_database.running_dry_run'))->toBe('Running database cleanup in dry-run mode...')
        ->and(trans('console.cleanup_database.keep_days', ['days' => 60]))->toBe('Keep days: 60')
        ->and(trans('console.cleanup_database.delete_failed_jobs', ['count' => 2]))->toBe('Delete 2 entries from failed_jobs.')
        ->and(trans('console.cleanup_database.delete_sessions', ['count' => 3]))->toBe('Delete 3 entries from sessions.')
        ->and(trans('console.cleanup_database.delete_activity_log', ['count' => 4]))->toBe('Delete 4 entries from activity_log.')
        ->and(trans('console.cleanup_database.delete_application_deployment_queues', ['count' => 5]))->toBe('Delete 5 entries from application_deployment_queues.')
        ->and(trans('console.cleanup_database.delete_scheduled_task_executions', ['count' => 6]))->toBe('Delete 6 entries from scheduled_task_executions.')
        ->and((new CleanupDatabase)->getDescription())->toBe('Cleanup database.');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_database.running'))->toBe('正在清理数据库...')
        ->and(trans('console.cleanup_database.running_dry_run'))->toBe('正在以 dry-run 模式清理数据库...')
        ->and(trans('console.cleanup_database.keep_days', ['days' => 60]))->toBe('保留天数：60')
        ->and(trans('console.cleanup_database.delete_failed_jobs', ['count' => 2]))->toBe('删除 failed_jobs 中的 2 条记录。')
        ->and(trans('console.cleanup_database.delete_sessions', ['count' => 3]))->toBe('删除 sessions 中的 3 条记录。')
        ->and(trans('console.cleanup_database.delete_activity_log', ['count' => 4]))->toBe('删除 activity_log 中的 4 条记录。')
        ->and(trans('console.cleanup_database.delete_application_deployment_queues', ['count' => 5]))->toBe('删除 application_deployment_queues 中的 5 条记录。')
        ->and(trans('console.cleanup_database.delete_scheduled_task_executions', ['count' => 6]))->toBe('删除 scheduled_task_executions 中的 6 条记录。')
        ->and((new CleanupDatabase)->getDescription())->toBe('清理数据库。');
});
