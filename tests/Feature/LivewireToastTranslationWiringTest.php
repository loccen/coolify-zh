<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative livewire toast dispatches', function () {
    $databaseRedisGeneral = file_get_contents(app_path('Livewire/Project/Database/Redis/General.php'));
    $databaseHeading = file_get_contents(app_path('Livewire/Project/Database/Heading.php'));
    $serviceIndex = file_get_contents(app_path('Livewire/Project/Service/Index.php'));
    $serviceConfiguration = file_get_contents(app_path('Livewire/Project/Service/Configuration.php'));
    $serviceStorage = file_get_contents(app_path('Livewire/Project/Service/Storage.php'));
    $serviceFileStorage = file_get_contents(app_path('Livewire/Project/Service/FileStorage.php'));
    $serviceStackForm = file_get_contents(app_path('Livewire/Project/Service/StackForm.php'));
    $serviceEditCompose = file_get_contents(app_path('Livewire/Project/Service/EditCompose.php'));
    $applicationPreviews = file_get_contents(app_path('Livewire/Project/Application/Previews.php'));
    $applicationGeneral = file_get_contents(app_path('Livewire/Project/Application/General.php'));
    $sharedTags = file_get_contents(app_path('Livewire/Project/Shared/Tags.php'));
    $scheduledTaskShow = file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Show.php'));
    $scheduledTaskAdd = file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Add.php'));
    $databaseImport = file_get_contents(app_path('Livewire/Project/Database/Import.php'));

    expect($databaseRedisGeneral)
        ->toContain("__('Database updated.')")
        ->toContain("__('Database must be started to be publicly accessible.')")
        ->toContain("__('SSL configuration updated.')")
        ->and($databaseHeading)
        ->toContain("__('Gracefully stopping database.')")
        ->and($serviceIndex)
        ->toContain("__('Database deleted.')")
        ->toContain("__('Application deleted.')")
        ->toContain("__('Database saved.')")
        ->and($serviceConfiguration)
        ->toContain("__('Service application restarted successfully.')")
        ->toContain("__('Service database restarted successfully.')")
        ->and($serviceStorage)
        ->toContain("__('Volume added successfully')")
        ->toContain("__('Directory mount added successfully')")
        ->and($serviceFileStorage)
        ->toContain("__('File storage loaded from server.')")
        ->toContain("__('File updated.')")
        ->and($serviceStackForm)
        ->toContain("__('Service settings saved.')")
        ->toContain("__('Service saved.')")
        ->and($serviceEditCompose)
        ->toContain("__('Docker compose is valid.')")
        ->toContain("__('Saving new docker compose...')")
        ->toContain("__('Service updated successfully')")
        ->and($applicationPreviews)
        ->toContain("__('Validating DNS failed.')")
        ->toContain("__('Preview saved.<br><br>Do not forget to redeploy the preview to apply the changes.')")
        ->toContain("__('Preview deletion started. It may take a few moments to complete.')")
        ->and($applicationGeneral)
        ->toContain("__('Failed to parse your docker-compose file. Please check the syntax and try again.')")
        ->toContain("__('Loading docker compose file.')")
        ->toContain("__('Application settings updated!')")
        ->and($sharedTags)
        ->toContain("__('Invalid tag.')")
        ->toContain("__('Tag added.')")
        ->and($scheduledTaskShow)
        ->toContain("__('Scheduled task enabled.')")
        ->toContain("__('Scheduled task executed.')")
        ->and($scheduledTaskAdd)
        ->toContain("__('Invalid Cron / Human expression.')")
        ->toContain("__('Scheduled task added.')")
        ->and($databaseImport)
        ->toContain("__('Server not found. Please refresh the page.')")
        ->toContain("__('Please select a file to import.')")
        ->toContain("__('Invalid container name.')");
});

it('resolves representative livewire toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Database updated.'))->toBe('数据库已更新。')
        ->and(__('You need to restart the service for the changes to take effect.'))->toBe('需要重启服务后，更改才会生效。')
        ->and(__('Database must be started to be publicly accessible.'))->toBe('数据库必须先启动，才能公开访问。')
        ->and(__('SSL configuration updated.'))->toBe('SSL 配置已更新。')
        ->and(__('Database deleted.'))->toBe('数据库已删除。')
        ->and(__('Application deleted.'))->toBe('应用已删除。')
        ->and(__('Service settings saved.'))->toBe('服务设置已保存。')
        ->and(__('Service saved.'))->toBe('服务已保存。')
        ->and(__('Docker compose is valid.'))->toBe('Docker Compose 配置有效。')
        ->and(__('Saving new docker compose...'))->toBe('正在保存新的 Docker Compose...')
        ->and(__('Service updated successfully'))->toBe('服务已成功更新。')
        ->and(__('Gracefully stopping database.'))->toBe('正在优雅停止数据库。')
        ->and(__('Validating DNS failed.'))->toBe('DNS 校验失败。')
        ->and(__('Preview not found.'))->toBe('未找到预览。')
        ->and(__('Preview added.'))->toBe('预览已添加。')
        ->and(__('Deployment queue full'))->toBe('部署队列已满')
        ->and(__('Deployment skipped'))->toBe('已跳过部署')
        ->and(__('Manual Docker Image previews are only available for Docker Image applications.'))->toBe('手动 Docker 镜像预览只适用于 Docker 镜像应用。')
        ->and(__('Both pull request id and docker tag are required.'))->toBe('拉取请求 ID 和 Docker 标签都必须填写。')
        ->and(__('Failed to parse your docker-compose file. Please check the syntax and try again.'))->toBe('无法解析你的 docker-compose 文件。请检查语法后重试。')
        ->and(__('Loading docker compose file.'))->toBe('正在加载 Docker Compose 文件。')
        ->and(__('Docker compose file loaded.'))->toBe('Docker Compose 文件已加载。')
        ->and(__('Wildcard domain generated.'))->toBe('通配域名已生成。')
        ->and(__('Nginx configuration generated.'))->toBe('Nginx 配置已生成。')
        ->and(__('Redirect updated.'))->toBe('重定向已更新。')
        ->and(__('Application settings updated!'))->toBe('应用设置已更新！')
        ->and(__('Invalid tag.'))->toBe('无效标签。')
        ->and(__('Tag added.'))->toBe('标签已添加。')
        ->and(__('Scheduled task enabled.'))->toBe('计划任务已启用。')
        ->and(__('Scheduled task executed.'))->toBe('计划任务已执行。')
        ->and(__('Invalid Cron / Human expression.'))->toBe('无效的 Cron / 人类可读表达式。')
        ->and(__('Scheduled task added.'))->toBe('计划任务已添加。')
        ->and(__('Server not found. Please refresh the page.'))->toBe('未找到服务器，请刷新页面。')
        ->and(__('Please select a file to import.'))->toBe('请选择要导入的文件。')
        ->and(__('Invalid container name.'))->toBe('容器名称无效。');
});
