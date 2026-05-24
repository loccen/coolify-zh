<?php

use App\Console\Commands\RunScheduledJobsManually;
use Illuminate\Support\Facades\App;

it('wires run scheduled jobs manually console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/RunScheduledJobsManually.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.run_scheduled_jobs_manually.description'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.starting'")
        ->toContain("trans('console.run_scheduled_jobs_manually.warn.dry_run_mode'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.completed'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.processing_scheduled_database_backups'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_backups_found'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.processing_scheduled_tasks'")
        ->toContain("trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_tasks_found'");

    expect($enTranslations['run_scheduled_jobs_manually']['description'])->toBe('Manually run scheduled database backups and tasks when cron fails')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['starting'])->toBe('Starting manual execution of scheduled jobs...:suffix')
        ->and($enTranslations['run_scheduled_jobs_manually']['warn']['dry_run_mode'])->toBe('DRY RUN MODE: No jobs will actually be dispatched')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['completed'])->toBe('Completed manual execution of scheduled jobs.:suffix')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['processing_scheduled_database_backups'])->toBe('Processing scheduled database backups...')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['no_enabled_scheduled_backups_found'])->toBe('No enabled scheduled backups found:frequency.')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['processing_scheduled_tasks'])->toBe('Processing scheduled tasks...')
        ->and($enTranslations['run_scheduled_jobs_manually']['info']['no_enabled_scheduled_tasks_found'])->toBe('No enabled scheduled tasks found:frequency.')
        ->and($enTranslations['run_scheduled_jobs_manually']['values']['dry_run_suffix'])->toBe(' (DRY RUN)')
        ->and($enTranslations['run_scheduled_jobs_manually']['values']['with_frequency'])->toBe(" with frequency ':frequency'");

    expect($zhTranslations['run_scheduled_jobs_manually']['description'])->toBe('在 cron 失效时手动运行计划备份和计划任务')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['starting'])->toBe('正在开始手动执行计划任务...:suffix')
        ->and($zhTranslations['run_scheduled_jobs_manually']['warn']['dry_run_mode'])->toBe('DRY RUN 模式：不会实际分发任何任务')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['completed'])->toBe('已完成手动执行计划任务。:suffix')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['processing_scheduled_database_backups'])->toBe('正在处理计划数据库备份...')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['no_enabled_scheduled_backups_found'])->toBe('未找到已启用的计划数据库备份:frequency。')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['processing_scheduled_tasks'])->toBe('正在处理计划任务...')
        ->and($zhTranslations['run_scheduled_jobs_manually']['info']['no_enabled_scheduled_tasks_found'])->toBe('未找到已启用的计划任务:frequency。')
        ->and($zhTranslations['run_scheduled_jobs_manually']['values']['dry_run_suffix'])->toBe('（DRY RUN）')
        ->and($zhTranslations['run_scheduled_jobs_manually']['values']['with_frequency'])->toBe('，频率为“:frequency”');
});

it('resolves run scheduled jobs manually translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.run_scheduled_jobs_manually.description'))->toBe('Manually run scheduled database backups and tasks when cron fails')
        ->and(trans('console.run_scheduled_jobs_manually.info.starting', ['suffix' => trans('console.run_scheduled_jobs_manually.values.dry_run_suffix')]))->toBe('Starting manual execution of scheduled jobs... (DRY RUN)')
        ->and(trans('console.run_scheduled_jobs_manually.warn.dry_run_mode'))->toBe('DRY RUN MODE: No jobs will actually be dispatched')
        ->and(trans('console.run_scheduled_jobs_manually.info.completed', ['suffix' => trans('console.run_scheduled_jobs_manually.values.dry_run_suffix')]))->toBe('Completed manual execution of scheduled jobs. (DRY RUN)')
        ->and(trans('console.run_scheduled_jobs_manually.info.processing_scheduled_database_backups'))->toBe('Processing scheduled database backups...')
        ->and(trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_backups_found', ['frequency' => trans('console.run_scheduled_jobs_manually.values.with_frequency', ['frequency' => 'daily'])]))->toBe("No enabled scheduled backups found with frequency 'daily'.")
        ->and(trans('console.run_scheduled_jobs_manually.info.processing_scheduled_tasks'))->toBe('Processing scheduled tasks...')
        ->and(trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_tasks_found', ['frequency' => trans('console.run_scheduled_jobs_manually.values.with_frequency', ['frequency' => '0 0 * * *'])]))->toBe("No enabled scheduled tasks found with frequency '0 0 * * *'.")
        ->and((new RunScheduledJobsManually)->getDescription())->toBe('Manually run scheduled database backups and tasks when cron fails');

    App::setLocale('zh_CN');

    expect(trans('console.run_scheduled_jobs_manually.description'))->toBe('在 cron 失效时手动运行计划备份和计划任务')
        ->and(trans('console.run_scheduled_jobs_manually.info.starting', ['suffix' => trans('console.run_scheduled_jobs_manually.values.dry_run_suffix')]))->toBe('正在开始手动执行计划任务...（DRY RUN）')
        ->and(trans('console.run_scheduled_jobs_manually.warn.dry_run_mode'))->toBe('DRY RUN 模式：不会实际分发任何任务')
        ->and(trans('console.run_scheduled_jobs_manually.info.completed', ['suffix' => trans('console.run_scheduled_jobs_manually.values.dry_run_suffix')]))->toBe('已完成手动执行计划任务。（DRY RUN）')
        ->and(trans('console.run_scheduled_jobs_manually.info.processing_scheduled_database_backups'))->toBe('正在处理计划数据库备份...')
        ->and(trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_backups_found', ['frequency' => trans('console.run_scheduled_jobs_manually.values.with_frequency', ['frequency' => 'daily'])]))->toBe('未找到已启用的计划数据库备份，频率为“daily”。')
        ->and(trans('console.run_scheduled_jobs_manually.info.processing_scheduled_tasks'))->toBe('正在处理计划任务...')
        ->and(trans('console.run_scheduled_jobs_manually.info.no_enabled_scheduled_tasks_found', ['frequency' => trans('console.run_scheduled_jobs_manually.values.with_frequency', ['frequency' => '0 0 * * *'])]))->toBe('未找到已启用的计划任务，频率为“0 0 * * *”。')
        ->and((new RunScheduledJobsManually)->getDescription())->toBe('在 cron 失效时手动运行计划备份和计划任务');
});
