<?php

use App\Console\Commands\Init;
use Illuminate\Support\Facades\App;

it('wires init command phase one strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/Init.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.init.description', locale: app()->getLocale())")
        ->toContain("trans('console.init.info.changelog_fetch_initiated', locale: app()->getLocale())")
        ->toContain("trans('console.init.info.enabling_auto_update', locale: \$locale)")
        ->toContain("trans('console.init.info.disabling_auto_update', locale: \$locale)")
        ->toContain("trans('console.init.info.continuing_with_initialization', locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_pull_templates_from_cdn', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_changelogs_from_github', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.error_in_pull_helper_image_command', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.error_in_cleanup_redis_command', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.error_in_cleanup_names_command', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.error_in_cleanup_stucked_resources_command', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_cleanup_inprogress_deployments', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_cleanup_stuck_scheduled_task_executions', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_cleanup_stuck_database_backup_executions', ['error' => \$e->getMessage()], locale: \$locale)")
        ->toContain("trans('console.init.error.could_not_setup_dynamic_configuration', ['error' => \$e->getMessage()], locale: \$locale)");

    expect($enTranslations['init']['description'])->toBe('Cleanup instance related stuffs')
        ->and($enTranslations['init']['info']['changelog_fetch_initiated'])->toBe('Changelog fetch initiated')
        ->and($enTranslations['init']['info']['enabling_auto_update'])->toBe('Enabling auto-update')
        ->and($enTranslations['init']['info']['disabling_auto_update'])->toBe('Disabling auto-update')
        ->and($enTranslations['init']['info']['continuing_with_initialization'])->toBe('Continuing with initialization - cleanup errors will not prevent Coolify from starting')
        ->and($enTranslations['init']['error']['could_not_pull_templates_from_cdn'])->toBe('Could not pull templates from CDN: :error')
        ->and($enTranslations['init']['error']['could_not_changelogs_from_github'])->toBe('Could not changelogs from github: :error')
        ->and($enTranslations['init']['error']['error_in_pull_helper_image_command'])->toBe('Error in pullHelperImage command: :error')
        ->and($enTranslations['init']['error']['error_in_cleanup_redis_command'])->toBe('Error in cleanup:redis command: :error')
        ->and($enTranslations['init']['error']['error_in_cleanup_names_command'])->toBe('Error in cleanup:names command: :error')
        ->and($enTranslations['init']['error']['error_in_cleanup_stucked_resources_command'])->toBe('Error in cleanup:stucked-resources command: :error')
        ->and($enTranslations['init']['error']['could_not_cleanup_inprogress_deployments'])->toBe('Could not cleanup inprogress deployments: :error')
        ->and($enTranslations['init']['error']['could_not_cleanup_stuck_scheduled_task_executions'])->toBe('Could not cleanup stuck scheduled task executions: :error')
        ->and($enTranslations['init']['error']['could_not_cleanup_stuck_database_backup_executions'])->toBe('Could not cleanup stuck database backup executions: :error')
        ->and($enTranslations['init']['error']['could_not_setup_dynamic_configuration'])->toBe('Could not setup dynamic configuration: :error');

    expect($zhTranslations['init']['description'])->toBe('清理实例相关内容')
        ->and($zhTranslations['init']['info']['changelog_fetch_initiated'])->toBe('已开始拉取更新日志')
        ->and($zhTranslations['init']['info']['enabling_auto_update'])->toBe('正在启用自动更新')
        ->and($zhTranslations['init']['info']['disabling_auto_update'])->toBe('正在禁用自动更新')
        ->and($zhTranslations['init']['info']['continuing_with_initialization'])->toBe('继续初始化，清理错误不会阻止 Coolify 启动')
        ->and($zhTranslations['init']['error']['could_not_pull_templates_from_cdn'])->toBe('无法从 CDN 拉取模板：:error')
        ->and($zhTranslations['init']['error']['could_not_changelogs_from_github'])->toBe('无法从 GitHub 拉取更新日志：:error')
        ->and($zhTranslations['init']['error']['error_in_pull_helper_image_command'])->toBe('执行 pullHelperImage 命令时出错：:error')
        ->and($zhTranslations['init']['error']['error_in_cleanup_redis_command'])->toBe('执行 cleanup:redis 命令时出错：:error')
        ->and($zhTranslations['init']['error']['error_in_cleanup_names_command'])->toBe('执行 cleanup:names 命令时出错：:error')
        ->and($zhTranslations['init']['error']['error_in_cleanup_stucked_resources_command'])->toBe('执行 cleanup:stucked-resources 命令时出错：:error')
        ->and($zhTranslations['init']['error']['could_not_cleanup_inprogress_deployments'])->toBe('无法清理进行中的部署：:error')
        ->and($zhTranslations['init']['error']['could_not_cleanup_stuck_scheduled_task_executions'])->toBe('无法清理卡住的计划任务执行记录：:error')
        ->and($zhTranslations['init']['error']['could_not_cleanup_stuck_database_backup_executions'])->toBe('无法清理卡住的数据库备份执行记录：:error')
        ->and($zhTranslations['init']['error']['could_not_setup_dynamic_configuration'])->toBe('无法设置动态配置：:error');
});

it('resolves init command phase one translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.init.description'))->toBe('Cleanup instance related stuffs')
        ->and(trans('console.init.info.changelog_fetch_initiated'))->toBe('Changelog fetch initiated')
        ->and(trans('console.init.info.enabling_auto_update'))->toBe('Enabling auto-update')
        ->and(trans('console.init.info.disabling_auto_update'))->toBe('Disabling auto-update')
        ->and(trans('console.init.info.continuing_with_initialization'))->toBe('Continuing with initialization - cleanup errors will not prevent Coolify from starting')
        ->and(trans('console.init.error.could_not_pull_templates_from_cdn', ['error' => 'boom']))->toBe('Could not pull templates from CDN: boom')
        ->and(trans('console.init.error.could_not_changelogs_from_github', ['error' => 'boom']))->toBe('Could not changelogs from github: boom')
        ->and(trans('console.init.error.error_in_pull_helper_image_command', ['error' => 'boom']))->toBe('Error in pullHelperImage command: boom')
        ->and(trans('console.init.error.error_in_cleanup_redis_command', ['error' => 'boom']))->toBe('Error in cleanup:redis command: boom')
        ->and(trans('console.init.error.error_in_cleanup_names_command', ['error' => 'boom']))->toBe('Error in cleanup:names command: boom')
        ->and(trans('console.init.error.error_in_cleanup_stucked_resources_command', ['error' => 'boom']))->toBe('Error in cleanup:stucked-resources command: boom')
        ->and(trans('console.init.error.could_not_cleanup_inprogress_deployments', ['error' => 'boom']))->toBe('Could not cleanup inprogress deployments: boom')
        ->and(trans('console.init.error.could_not_cleanup_stuck_scheduled_task_executions', ['error' => 'boom']))->toBe('Could not cleanup stuck scheduled task executions: boom')
        ->and(trans('console.init.error.could_not_cleanup_stuck_database_backup_executions', ['error' => 'boom']))->toBe('Could not cleanup stuck database backup executions: boom')
        ->and(trans('console.init.error.could_not_setup_dynamic_configuration', ['error' => 'boom']))->toBe('Could not setup dynamic configuration: boom')
        ->and((new Init)->getDescription())->toBe('Cleanup instance related stuffs');

    App::setLocale('zh_CN');

    expect(trans('console.init.description'))->toBe('清理实例相关内容')
        ->and(trans('console.init.info.changelog_fetch_initiated'))->toBe('已开始拉取更新日志')
        ->and(trans('console.init.info.enabling_auto_update'))->toBe('正在启用自动更新')
        ->and(trans('console.init.info.disabling_auto_update'))->toBe('正在禁用自动更新')
        ->and(trans('console.init.info.continuing_with_initialization'))->toBe('继续初始化，清理错误不会阻止 Coolify 启动')
        ->and(trans('console.init.error.could_not_pull_templates_from_cdn', ['error' => 'boom']))->toBe('无法从 CDN 拉取模板：boom')
        ->and(trans('console.init.error.could_not_changelogs_from_github', ['error' => 'boom']))->toBe('无法从 GitHub 拉取更新日志：boom')
        ->and(trans('console.init.error.error_in_pull_helper_image_command', ['error' => 'boom']))->toBe('执行 pullHelperImage 命令时出错：boom')
        ->and(trans('console.init.error.error_in_cleanup_redis_command', ['error' => 'boom']))->toBe('执行 cleanup:redis 命令时出错：boom')
        ->and(trans('console.init.error.error_in_cleanup_names_command', ['error' => 'boom']))->toBe('执行 cleanup:names 命令时出错：boom')
        ->and(trans('console.init.error.error_in_cleanup_stucked_resources_command', ['error' => 'boom']))->toBe('执行 cleanup:stucked-resources 命令时出错：boom')
        ->and(trans('console.init.error.could_not_cleanup_inprogress_deployments', ['error' => 'boom']))->toBe('无法清理进行中的部署：boom')
        ->and(trans('console.init.error.could_not_cleanup_stuck_scheduled_task_executions', ['error' => 'boom']))->toBe('无法清理卡住的计划任务执行记录：boom')
        ->and(trans('console.init.error.could_not_cleanup_stuck_database_backup_executions', ['error' => 'boom']))->toBe('无法清理卡住的数据库备份执行记录：boom')
        ->and(trans('console.init.error.could_not_setup_dynamic_configuration', ['error' => 'boom']))->toBe('无法设置动态配置：boom')
        ->and((new Init)->getDescription())->toBe('清理实例相关内容');
});
