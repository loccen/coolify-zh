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
        ->toContain("trans('console.horizon_manage.no_running_jobs_found'")
        ->toContain("trans('console.horizon_manage.prompts.what_to_do'")
        ->toContain("trans('console.horizon_manage.options.pending'")
        ->toContain("trans('console.horizon_manage.options.running'")
        ->toContain("trans('console.horizon_manage.options.can_i_restart_this_worker'")
        ->toContain("trans('console.horizon_manage.options.job_status'")
        ->toContain("trans('console.horizon_manage.options.workers'")
        ->toContain("trans('console.horizon_manage.options.failed'")
        ->toContain("trans('console.horizon_manage.options.failed_delete'")
        ->toContain("trans('console.horizon_manage.options.purge_queues'")
        ->toContain("trans('console.horizon_manage.prompts.which_job_to_check'")
        ->toContain("trans('console.horizon_manage.prompts.which_job_to_delete'")
        ->toContain("trans('console.horizon_manage.prompts.which_queue_to_purge'");

    expect($enTranslations['horizon_manage']['description'])->toBe('Manage Horizon')
        ->and($enTranslations['horizon_manage']['job_status'])->toBe('Job Status: :status')
        ->and($enTranslations['horizon_manage']['no_pending_jobs_found'])->toBe('No pending jobs found.')
        ->and($enTranslations['horizon_manage']['no_failed_jobs_found'])->toBe('No failed jobs found.')
        ->and($enTranslations['horizon_manage']['no_running_jobs_found'])->toBe('No running jobs found.')
        ->and($enTranslations['horizon_manage']['prompts']['what_to_do'])->toBe('What to do?')
        ->and($enTranslations['horizon_manage']['prompts']['which_job_to_check'])->toBe('Which job to check?')
        ->and($enTranslations['horizon_manage']['prompts']['which_job_to_delete'])->toBe('Which job to delete?')
        ->and($enTranslations['horizon_manage']['prompts']['which_queue_to_purge'])->toBe('Which queue to purge?')
        ->and($enTranslations['horizon_manage']['options']['pending'])->toBe('Pending Jobs')
        ->and($enTranslations['horizon_manage']['options']['running'])->toBe('Running Jobs')
        ->and($enTranslations['horizon_manage']['options']['can_i_restart_this_worker'])->toBe('Can I restart this worker?')
        ->and($enTranslations['horizon_manage']['options']['job_status'])->toBe('Job Status')
        ->and($enTranslations['horizon_manage']['options']['workers'])->toBe('Workers')
        ->and($enTranslations['horizon_manage']['options']['failed'])->toBe('Failed Jobs')
        ->and($enTranslations['horizon_manage']['options']['failed_delete'])->toBe('Failed Jobs - Delete')
        ->and($enTranslations['horizon_manage']['options']['purge_queues'])->toBe('Purge Queues');

    expect($zhTranslations['horizon_manage']['description'])->toBe('管理 Horizon')
        ->and($zhTranslations['horizon_manage']['job_status'])->toBe('任务状态：:status')
        ->and($zhTranslations['horizon_manage']['no_pending_jobs_found'])->toBe('未找到待处理作业。')
        ->and($zhTranslations['horizon_manage']['no_failed_jobs_found'])->toBe('未找到失败作业。')
        ->and($zhTranslations['horizon_manage']['no_running_jobs_found'])->toBe('未找到运行中的作业。')
        ->and($zhTranslations['horizon_manage']['prompts']['what_to_do'])->toBe('要执行什么操作？')
        ->and($zhTranslations['horizon_manage']['prompts']['which_job_to_check'])->toBe('要检查哪个作业？')
        ->and($zhTranslations['horizon_manage']['prompts']['which_job_to_delete'])->toBe('要删除哪个作业？')
        ->and($zhTranslations['horizon_manage']['prompts']['which_queue_to_purge'])->toBe('要清空哪个队列？')
        ->and($zhTranslations['horizon_manage']['options']['pending'])->toBe('待处理作业')
        ->and($zhTranslations['horizon_manage']['options']['running'])->toBe('运行中的作业')
        ->and($zhTranslations['horizon_manage']['options']['can_i_restart_this_worker'])->toBe('现在可以重启这个 worker 吗？')
        ->and($zhTranslations['horizon_manage']['options']['job_status'])->toBe('作业状态')
        ->and($zhTranslations['horizon_manage']['options']['workers'])->toBe('Workers')
        ->and($zhTranslations['horizon_manage']['options']['failed'])->toBe('失败作业')
        ->and($zhTranslations['horizon_manage']['options']['failed_delete'])->toBe('失败作业 - 删除')
        ->and($zhTranslations['horizon_manage']['options']['purge_queues'])->toBe('清空队列');
});

it('resolves horizon manage translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.horizon_manage.description'))->toBe('Manage Horizon')
        ->and(trans('console.horizon_manage.job_status', ['status' => 'Running']))->toBe('Job Status: Running')
        ->and(trans('console.horizon_manage.no_pending_jobs_found'))->toBe('No pending jobs found.')
        ->and(trans('console.horizon_manage.no_failed_jobs_found'))->toBe('No failed jobs found.')
        ->and(trans('console.horizon_manage.no_running_jobs_found'))->toBe('No running jobs found.')
        ->and(trans('console.horizon_manage.prompts.what_to_do'))->toBe('What to do?')
        ->and(trans('console.horizon_manage.prompts.which_job_to_check'))->toBe('Which job to check?')
        ->and(trans('console.horizon_manage.prompts.which_job_to_delete'))->toBe('Which job to delete?')
        ->and(trans('console.horizon_manage.prompts.which_queue_to_purge'))->toBe('Which queue to purge?')
        ->and(trans('console.horizon_manage.options.pending'))->toBe('Pending Jobs')
        ->and(trans('console.horizon_manage.options.running'))->toBe('Running Jobs')
        ->and(trans('console.horizon_manage.options.can_i_restart_this_worker'))->toBe('Can I restart this worker?')
        ->and(trans('console.horizon_manage.options.job_status'))->toBe('Job Status')
        ->and(trans('console.horizon_manage.options.workers'))->toBe('Workers')
        ->and(trans('console.horizon_manage.options.failed'))->toBe('Failed Jobs')
        ->and(trans('console.horizon_manage.options.failed_delete'))->toBe('Failed Jobs - Delete')
        ->and(trans('console.horizon_manage.options.purge_queues'))->toBe('Purge Queues')
        ->and((new HorizonManage)->getDescription())->toBe('Manage Horizon');

    App::setLocale('zh_CN');

    expect(trans('console.horizon_manage.description'))->toBe('管理 Horizon')
        ->and(trans('console.horizon_manage.job_status', ['status' => '运行中']))->toBe('任务状态：运行中')
        ->and(trans('console.horizon_manage.no_pending_jobs_found'))->toBe('未找到待处理作业。')
        ->and(trans('console.horizon_manage.no_failed_jobs_found'))->toBe('未找到失败作业。')
        ->and(trans('console.horizon_manage.no_running_jobs_found'))->toBe('未找到运行中的作业。')
        ->and(trans('console.horizon_manage.prompts.what_to_do'))->toBe('要执行什么操作？')
        ->and(trans('console.horizon_manage.prompts.which_job_to_check'))->toBe('要检查哪个作业？')
        ->and(trans('console.horizon_manage.prompts.which_job_to_delete'))->toBe('要删除哪个作业？')
        ->and(trans('console.horizon_manage.prompts.which_queue_to_purge'))->toBe('要清空哪个队列？')
        ->and(trans('console.horizon_manage.options.pending'))->toBe('待处理作业')
        ->and(trans('console.horizon_manage.options.running'))->toBe('运行中的作业')
        ->and(trans('console.horizon_manage.options.can_i_restart_this_worker'))->toBe('现在可以重启这个 worker 吗？')
        ->and(trans('console.horizon_manage.options.job_status'))->toBe('作业状态')
        ->and(trans('console.horizon_manage.options.workers'))->toBe('Workers')
        ->and(trans('console.horizon_manage.options.failed'))->toBe('失败作业')
        ->and(trans('console.horizon_manage.options.failed_delete'))->toBe('失败作业 - 删除')
        ->and(trans('console.horizon_manage.options.purge_queues'))->toBe('清空队列')
        ->and((new HorizonManage)->getDescription())->toBe('管理 Horizon');
});
