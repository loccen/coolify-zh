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
        ->toContain("trans('console.scheduled_job_diagnostics.heartbeat'")
        ->toContain("trans('console.scheduled_job_diagnostics.heartbeat_missing'")
        ->toContain("trans('console.scheduled_job_diagnostics.sections.docker_cleanup'")
        ->toContain("trans('console.scheduled_job_diagnostics.sections.scheduled_backups'")
        ->toContain("trans('console.scheduled_job_diagnostics.sections.scheduled_tasks'")
        ->toContain("trans('console.scheduled_job_diagnostics.sections.server_manager_jobs'");

    expect($enTranslations['scheduled_job_diagnostics']['description'])->toBe('Inspect dedup cache state and scheduling decisions for all scheduled jobs')
        ->and($enTranslations['scheduled_job_diagnostics']['heartbeat'])->toBe('Scheduler heartbeat: :heartbeat (:age)')
        ->and($enTranslations['scheduled_job_diagnostics']['heartbeat_missing'])->toBe('Scheduler heartbeat: MISSING — ScheduledJobManager may not be running')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['docker_cleanup'])->toBe('=== Docker Cleanup Jobs ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['scheduled_backups'])->toBe('=== Scheduled Backups ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['scheduled_tasks'])->toBe('=== Scheduled Tasks ===')
        ->and($enTranslations['scheduled_job_diagnostics']['sections']['server_manager_jobs'])->toBe('=== Server Manager Jobs ===');

    expect($zhTranslations['scheduled_job_diagnostics']['description'])->toBe('检查所有计划任务的去重缓存状态和调度决策')
        ->and($zhTranslations['scheduled_job_diagnostics']['heartbeat'])->toBe('Scheduler heartbeat: :heartbeat (:age)')
        ->and($zhTranslations['scheduled_job_diagnostics']['heartbeat_missing'])->toBe('Scheduler heartbeat: MISSING — ScheduledJobManager may not be running')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['docker_cleanup'])->toBe('=== Docker Cleanup Jobs ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['scheduled_backups'])->toBe('=== Scheduled Backups ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['scheduled_tasks'])->toBe('=== Scheduled Tasks ===')
        ->and($zhTranslations['scheduled_job_diagnostics']['sections']['server_manager_jobs'])->toBe('=== Server Manager Jobs ===');
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
        ->and((new ScheduledJobDiagnostics)->getDescription())->toBe('Inspect dedup cache state and scheduling decisions for all scheduled jobs');

    App::setLocale('zh_CN');

    expect(trans('console.scheduled_job_diagnostics.description'))->toBe('检查所有计划任务的去重缓存状态和调度决策')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat', ['heartbeat' => '2026-05-24 10:00:00', 'age' => '5 minutes ago']))->toBe('Scheduler heartbeat: 2026-05-24 10:00:00 (5 minutes ago)')
        ->and(trans('console.scheduled_job_diagnostics.heartbeat_missing'))->toBe('Scheduler heartbeat: MISSING — ScheduledJobManager may not be running')
        ->and(trans('console.scheduled_job_diagnostics.sections.docker_cleanup'))->toBe('=== Docker Cleanup Jobs ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_backups'))->toBe('=== Scheduled Backups ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.scheduled_tasks'))->toBe('=== Scheduled Tasks ===')
        ->and(trans('console.scheduled_job_diagnostics.sections.server_manager_jobs'))->toBe('=== Server Manager Jobs ===')
        ->and((new ScheduledJobDiagnostics)->getDescription())->toBe('检查所有计划任务的去重缓存状态和调度决策');
});
