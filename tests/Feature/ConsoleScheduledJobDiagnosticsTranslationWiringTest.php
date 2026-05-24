<?php

use App\Console\Commands\ScheduledJobDiagnostics;
use Illuminate\Support\Facades\App;

it('wires scheduled job diagnostics console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/ScheduledJobDiagnostics.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.scheduled_job_diagnostics.description'")
        ->toContain("\$this->translation('heartbeat'")
        ->toContain("\$this->translation('heartbeat_missing'")
        ->toContain("\$this->translation('sections.docker_cleanup')")
        ->toContain("\$this->translation('sections.scheduled_backups')")
        ->toContain("\$this->translation('sections.scheduled_tasks')")
        ->toContain("\$this->translation('sections.server_manager_jobs')")
        ->toContain("\$this->translation('headers.docker_cleanup.id')")
        ->toContain("\$this->translation('headers.docker_cleanup.last_execution')")
        ->toContain("\$this->translation('headers.scheduled_backups.backup_id')")
        ->toContain("\$this->translation('headers.scheduled_backups.database_type')")
        ->toContain("\$this->translation('headers.scheduled_tasks.task_id')")
        ->toContain("\$this->translation('headers.scheduled_tasks.name')")
        ->toContain("\$this->translation('headers.server_manager_jobs.server_id')")
        ->toContain("\$this->translation('values.missing')")
        ->toContain("\$this->translation('values.yes')")
        ->toContain("\$this->translation('values.no')")
        ->toContain("\$this->translation('values.never')")
        ->toContain("\$this->translation('values.unknown')")
        ->toContain("\$this->translation('values.not_applicable')");

    expect($enTranslations['scheduled_job_diagnostics']['description'])->toBe('Inspect dedup cache state and scheduling decisions for all scheduled jobs')
        ->and($enTranslations['scheduled_job_diagnostics']['heartbeat'])->toBe('Scheduler heartbeat: :heartbeat (:age)')
        ->and($enTranslations['scheduled_job_diagnostics']['heartbeat_missing'])->toBe('Scheduler heartbeat: MISSING — ScheduledJobManager may not be running')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['docker_cleanup'])->toBe('=== Docker Cleanup Jobs ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['scheduled_backups'])->toBe('=== Scheduled Backups ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['scheduled_tasks'])->toBe('=== Scheduled Tasks ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['server_manager_jobs'])->toBe('=== Server Manager Jobs ===')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['docker_cleanup']['id'])->toBe('ID')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['docker_cleanup']['last_execution'])->toBe('Last Execution')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['scheduled_backups']['backup_id'])->toBe('Backup ID')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['scheduled_backups']['database_type'])->toBe('DB Type')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['scheduled_tasks']['task_id'])->toBe('Task ID')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['scheduled_tasks']['name'])->toBe('Name')
        ->and($enTranslations['scheduled_job_diagnostics']['headers']['server_manager_jobs']['server_id'])->toBe('Server ID')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['missing'])->toBe('<missing>')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['yes'])->toBe('YES')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['no'])->toBe('no')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['never'])->toBe('never')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['unknown'])->toBe('unknown')
        ->and($enTranslations['scheduled_job_diagnostics']['values']['not_applicable'])->toBe('N/A');

    expect($zhTranslations['scheduled_job_diagnostics']['description'])->toBe('检查所有计划任务的去重缓存状态和调度决策')
        ->and($zhTranslations['scheduled_job_diagnostics']['heartbeat'])->toBe('调度器心跳：:heartbeat（:age）')
        ->and($zhTranslations['scheduled_job_diagnostics']['heartbeat_missing'])->toBe('调度器心跳：缺失。ScheduledJobManager 可能未运行。')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['docker_cleanup'])->toBe('=== Docker 清理任务 ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['scheduled_backups'])->toBe('=== 计划备份任务 ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['scheduled_tasks'])->toBe('=== 计划任务 ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['server_manager_jobs'])->toBe('=== 服务器管理任务 ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['docker_cleanup']['id'])->toBe('ID')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['docker_cleanup']['last_execution'])->toBe('上次执行')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['scheduled_backups']['backup_id'])->toBe('备份 ID')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['scheduled_backups']['database_type'])->toBe('数据库类型')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['scheduled_tasks']['task_id'])->toBe('任务 ID')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['scheduled_tasks']['name'])->toBe('名称')
        ->and($zhTranslations['scheduled_job_diagnostics']['headers']['server_manager_jobs']['server_id'])->toBe('服务器 ID')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['missing'])->toBe('<缺失>')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['yes'])->toBe('是')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['no'])->toBe('否')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['never'])->toBe('从未')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['unknown'])->toBe('未知')
        ->and($zhTranslations['scheduled_job_diagnostics']['values']['not_applicable'])->toBe('不适用');
});

it('resolves scheduled job diagnostics strings in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.scheduled_job_diagnostics.description'))->toBe('Inspect dedup cache state and scheduling decisions for all scheduled jobs')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat', ['heartbeat' => '2026-05-24 10:00:00', 'age' => '5 minutes ago']))->toBe('Scheduler heartbeat: 2026-05-24 10:00:00 (5 minutes ago)')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat_missing'))->toBe('Scheduler heartbeat: MISSING — ScheduledJobManager may not be running')
        ->and(trans('console.scheduled_job_diagnostics.sections.docker_cleanup'))->toBe('=== Docker Cleanup Jobs ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_backups'))->toBe('=== Scheduled Backups ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_tasks'))->toBe('=== Scheduled Tasks ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.server_manager_jobs'))->toBe('=== Server Manager Jobs ===')
        ->and(trans('console.scheduled_job_diagnostics.headers.docker_cleanup.id'))->toBe('ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.docker_cleanup.last_execution'))->toBe('Last Execution')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_backups.backup_id'))->toBe('Backup ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_backups.database_type'))->toBe('DB Type')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_tasks.task_id'))->toBe('Task ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_tasks.name'))->toBe('Name')
        ->and(trans('console.scheduled_job_diagnostics.headers.server_manager_jobs.server_id'))->toBe('Server ID')
        ->and(trans('console.scheduled_job_diagnostics.values.missing'))->toBe('<missing>')
        ->and(trans('console.scheduled_job_diagnostics.values.yes'))->toBe('YES')
        ->and(trans('console.scheduled_job_diagnostics.values.no'))->toBe('no')
        ->and(trans('console.scheduled_job_diagnostics.values.never'))->toBe('never')
        ->and(trans('console.scheduled_job_diagnostics.values.unknown'))->toBe('unknown')
        ->and(trans('console.scheduled_job_diagnostics.values.not_applicable'))->toBe('N/A')
        ->and((new ScheduledJobDiagnostics)->getDescription())->toBe('Inspect dedup cache state and scheduling decisions for all scheduled jobs');

    App::setLocale('zh_CN');

    expect(trans('console.scheduled_job_diagnostics.description'))->toBe('检查所有计划任务的去重缓存状态和调度决策')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat', ['heartbeat' => '2026-05-24 10:00:00', 'age' => '5 分钟前']))->toBe('调度器心跳：2026-05-24 10:00:00（5 分钟前）')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat_missing'))->toBe('调度器心跳：缺失。ScheduledJobManager 可能未运行。')
        ->and(trans('console.scheduled_job_diagnostics.sections.docker_cleanup'))->toBe('=== Docker 清理任务 ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_backups'))->toBe('=== 计划备份任务 ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_tasks'))->toBe('=== 计划任务 ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.server_manager_jobs'))->toBe('=== 服务器管理任务 ===')
        ->and(trans('console.scheduled_job_diagnostics.headers.docker_cleanup.id'))->toBe('ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.docker_cleanup.last_execution'))->toBe('上次执行')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_backups.backup_id'))->toBe('备份 ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_backups.database_type'))->toBe('数据库类型')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_tasks.task_id'))->toBe('任务 ID')
        ->and(trans('console.scheduled_job_diagnostics.headers.scheduled_tasks.name'))->toBe('名称')
        ->and(trans('console.scheduled_job_diagnostics.headers.server_manager_jobs.server_id'))->toBe('服务器 ID')
        ->and(trans('console.scheduled_job_diagnostics.values.missing'))->toBe('<缺失>')
        ->and(trans('console.scheduled_job_diagnostics.values.yes'))->toBe('是')
        ->and(trans('console.scheduled_job_diagnostics.values.no'))->toBe('否')
        ->and(trans('console.scheduled_job_diagnostics.values.never'))->toBe('从未')
        ->and(trans('console.scheduled_job_diagnostics.values.unknown'))->toBe('未知')
        ->and(trans('console.scheduled_job_diagnostics.values.not_applicable'))->toBe('不适用')
        ->and((new ScheduledJobDiagnostics)->getDescription())->toBe('检查所有计划任务的去重缓存状态和调度决策');
});
