<?php

use App\Console\Commands\HorizonManage;
use Illuminate\Support\Facades\App;

it('wires horizon manage console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/HorizonManage.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.horizon_manage.description'")
        ->toContain("trans('console.horizon_manage.job_status'")
        ->toContain("trans('console.horizon_manage.no_pending_jobs_found'")
        ->toContain("trans('console.horizon_manage.no_failed_jobs_found'")
        ->toContain("trans('console.horizon_manage.no_running_jobs_found'");

    expect($enTranslations['horizon_manage']['description'])->toBe('Manage Horizon')
        ->and($enTranslations['horizon_manage']['job_status'])->toBe('Job Status: :status')
        ->and($enTranslations['horizon_manage']['no_pending_jobs_found'])->toBe('No pending jobs found.')
        ->and($enTranslations['horizon_manage']['no_failed_jobs_found'])->toBe('No failed jobs found.')
        ->and($enTranslations['horizon_manage']['no_running_jobs_found'])->toBe('No running jobs found.');

    expect($zhTranslations['horizon_manage']['description'])->toBe('管理 Horizon')
        ->and($zhTranslations['horizon_manage']['job_status'])->toBe('任务状态：:status')
        ->and($zhTranslations['horizon_manage']['no_pending_jobs_found'])->toBe('未找到待处理作业。')
        ->and($zhTranslations['horizon_manage']['no_failed_jobs_found'])->toBe('未找到失败作业。')
        ->and($zhTranslations['horizon_manage']['no_running_jobs_found'])->toBe('未找到运行中的作业。');
});

it('resolves horizon manage translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.horizon_manage.description'))->toBe('Manage Horizon')
        ->and(trans('console.horizon_manage.job_status', ['status' => 'Running']))->toBe('Job Status: Running')
        ->and(trans('console.horizon_manage.no_pending_jobs_found'))->toBe('No pending jobs found.')
        ->and(trans('console.horizon_manage.no_failed_jobs_found'))->toBe('No failed jobs found.')
        ->and(trans('console.horizon_manage.no_running_jobs_found'))->toBe('No running jobs found.')
        ->and((new HorizonManage)->getDescription())->toBe('Manage Horizon');

    App::setLocale('zh_CN');

    expect(trans('console.horizon_manage.description'))->toBe('管理 Horizon')
        ->and(trans('console.horizon_manage.job_status', ['status' => '运行中']))->toBe('任务状态：运行中')
        ->and(trans('console.horizon_manage.no_pending_jobs_found'))->toBe('未找到待处理作业。')
        ->and(trans('console.horizon_manage.no_failed_jobs_found'))->toBe('未找到失败作业。')
        ->and(trans('console.horizon_manage.no_running_jobs_found'))->toBe('未找到运行中的作业。')
        ->and((new HorizonManage)->getDescription())->toBe('管理 Horizon');
});
