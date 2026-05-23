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

it('uses explicit translation lookups in follow-up livewire toast dispatches', function () {
    $applicationAdvanced = file_get_contents(app_path('Livewire/Project/Application/Advanced.php'));
    $applicationDeploymentIndex = file_get_contents(app_path('Livewire/Project/Application/Deployment/Index.php'));
    $applicationHeading = file_get_contents(app_path('Livewire/Project/Application/Heading.php'));
    $applicationPreviewForm = file_get_contents(app_path('Livewire/Project/Application/Preview/Form.php'));
    $applicationRollback = file_get_contents(app_path('Livewire/Project/Application/Rollback.php'));
    $applicationSource = file_get_contents(app_path('Livewire/Project/Application/Source.php'));
    $applicationSwarm = file_get_contents(app_path('Livewire/Project/Application/Swarm.php'));
    $backupNow = file_get_contents(app_path('Livewire/Project/Database/BackupNow.php'));
    $backupEdit = file_get_contents(app_path('Livewire/Project/Database/BackupEdit.php'));
    $backupExecutions = file_get_contents(app_path('Livewire/Project/Database/BackupExecutions.php'));
    $scheduledBackups = file_get_contents(app_path('Livewire/Project/Database/ScheduledBackups.php'));
    $databaseImport = file_get_contents(app_path('Livewire/Project/Database/Import.php'));
    $databasePostgresqlGeneral = file_get_contents(app_path('Livewire/Project/Database/Postgresql/General.php'));
    $environmentVariableAdd = file_get_contents(app_path('Livewire/Project/Shared/EnvironmentVariable/Add.php'));
    $environmentVariableAll = file_get_contents(app_path('Livewire/Project/Shared/EnvironmentVariable/All.php'));
    $environmentVariableShow = file_get_contents(app_path('Livewire/Project/Shared/EnvironmentVariable/Show.php'));
    $scheduledTaskAdd = file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Add.php'));
    $scheduledTaskShow = file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Show.php'));
    $executeContainerCommand = file_get_contents(app_path('Livewire/Project/Shared/ExecuteContainerCommand.php'));
    $healthChecks = file_get_contents(app_path('Livewire/Project/Shared/HealthChecks.php'));
    $webhooks = file_get_contents(app_path('Livewire/Project/Shared/Webhooks.php'));
    $projectEdit = file_get_contents(app_path('Livewire/Project/Edit.php'));

    expect($applicationAdvanced)
        ->toContain("__('Log drain is not enabled on this server.')")
        ->toContain("__('You cannot set both GPU count and GPU device IDs.')")
        ->toContain("__('Custom name saved.')")
        ->and($applicationDeploymentIndex)
        ->toContain("__('Invalid Pull Request ID in URL. Filter cleared.')")
        ->toContain("__('Invalid Pull Request ID. Please enter a valid positive number.')")
        ->and($applicationHeading)
        ->toContain("__('Failed to deploy')")
        ->toContain("__('Gracefully stopping application.<br/>It could take a while depending on the application.')")
        ->and($applicationPreviewForm)
        ->toContain("__('Preview url template updated.')")
        ->and($applicationRollback)
        ->toContain("__('Images loaded.')")
        ->and($applicationSource)
        ->toContain("__('Private key updated!')")
        ->toContain("__('Application source updated!')")
        ->toContain("__('Source updated!')")
        ->and($applicationSwarm)
        ->toContain("__('Swarm settings updated.')")
        ->and($backupNow)
        ->toContain("__('Backup queued. It will be available in a few minutes.')")
        ->and($backupEdit)
        ->toContain("__('Backup updated successfully.')")
        ->toContain("__('Instance timezone')")
        ->toContain("__('Failed to delete backup: :message'")
        ->and($backupExecutions)
        ->toContain("__('Failed backups cleaned up.')")
        ->toContain("__('Backup execution not found.')")
        ->toContain("__('Log: :path'")
        ->and($scheduledBackups)
        ->toContain("__('Database type set.')")
        ->toContain("__('Scheduled backup deleted.')")
        ->and($databaseImport)
        ->toContain("__('Please select an S3 storage.')")
        ->toContain("__('Please check the file first by clicking :action.'")
        ->toContain("__('File found in S3. Size: :size'")
        ->toContain("__('Restoring database from S3. Progress will be shown in the activity monitor...')")
        ->and($databasePostgresqlGeneral)
        ->toContain("__('A script with this filename already exists.')")
        ->toContain("__('Init script added.')")
        ->and($environmentVariableAdd)
        ->toContain("environmentVariableKeyMessages('key', 'Key')")
        ->and($environmentVariableAll)
        ->toContain("__('Locked Secret, delete and add again to change')")
        ->toContain("__('Multiline environment variable, edit in normal view')")
        ->toContain("__('Duplicate environment variable key after normalization: :key.'")
        ->toContain("__('Environment variable added.')")
        ->toContain("__('Cannot delete environment variable \\'")
        ->and($environmentVariableShow)
        ->toContain("environmentVariableKeyMessages('key', 'Key')")
        ->toContain("__('Required environment variables cannot be empty.')")
        ->toContain("__('Environment variable deleted successfully.')")
        ->and($executeContainerCommand)
        ->toContain("__('Please select a container.')")
        ->and($healthChecks)
        ->toContain("__('Health check updated.')")
        ->toContain("__('Health check enabled.')")
        ->and($scheduledTaskAdd)
        ->toContain("__('The scheduled task name field is required.')")
        ->toContain("__('The scheduled task timeout must be at least :min seconds.')")
        ->and($scheduledTaskShow)
        ->toContain("__('The scheduled task command field is required.')")
        ->toContain("__('The scheduled task timeout must not be greater than :max seconds.')")
        ->and(file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Executions.php')))
        ->toContain("__('Waiting for task output...')")
        ->and($webhooks)
        ->toContain("__('Secret Saved.')")
        ->and($projectEdit)
        ->toContain("__('Project updated.')");
});

it('resolves follow-up livewire toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Domain generated.'))->toBe('域名已生成。')
        ->and(__('Log drain is not enabled on this server.'))->toBe('此服务器未启用日志转发。')
        ->and(__('You cannot set both GPU count and GPU device IDs.'))->toBe('不能同时设置 GPU 数量和 GPU 设备 ID。')
        ->and(__('Custom name saved.'))->toBe('自定义名称已保存。')
        ->and(__('Stop grace period updated.'))->toBe('停止宽限期已更新。')
        ->and(__('Invalid Pull Request ID in URL. Filter cleared.'))->toBe('URL 中的拉取请求 ID 无效，筛选条件已清除。')
        ->and(__('Invalid Pull Request ID. Please enter a valid positive number.'))->toBe('拉取请求 ID 无效。请输入有效的正整数。')
        ->and(__('Private key updated!'))->toBe('私钥已更新！')
        ->and(__('Application source updated!'))->toBe('应用代码源已更新！')
        ->and(__('Source updated!'))->toBe('代码源已更新！')
        ->and(__('Preview url template updated.'))->toBe('预览 URL 模板已更新。')
        ->and(__('Swarm settings updated.'))->toBe('Swarm 设置已更新。')
        ->and(__('Backup queued. It will be available in a few minutes.'))->toBe('备份已加入队列，几分钟后可用。')
        ->and(__('Instance timezone'))->toBe('实例时区')
        ->and(__('Database type set.'))->toBe('数据库类型已设置。')
        ->and(__('Scheduled backup deleted.'))->toBe('计划备份已删除。')
        ->and(__('Backup updated successfully.'))->toBe('备份已成功更新。')
        ->and(__('Failed backups cleaned up.'))->toBe('失败的备份已清理。')
        ->and(__('Backup execution not found.'))->toBe('未找到备份执行记录。')
        ->and(__('Backup deleted.'))->toBe('备份已删除。')
        ->and(__('Failed to delete backup: :message', ['message' => 'boom']))->toBe('删除备份失败：boom')
        ->and(__('Log: :path', ['path' => '/tmp/restore.log']))->toBe('日志：/tmp/restore.log')
        ->and(__('Please select an S3 storage.'))->toBe('请选择一个 S3 存储。')
        ->and(__('Please provide an S3 path.'))->toBe('请输入 S3 路径。')
        ->and(__('Please check the file first by clicking :action.', ['action' => '“'.__('Check File').'”']))->toBe('请先点击 “检查文件” 检查该文件。')
        ->and(__('File found in S3. Size: :size', ['size' => '42 MB']))->toBe('已在 S3 中找到文件。大小：42 MB')
        ->and(__('Restoring database from S3. Progress will be shown in the activity monitor...'))->toBe('正在从 S3 恢复数据库。进度会显示在活动监视器中...')
        ->and(__('A script with this filename already exists.'))->toBe('已存在使用此文件名的脚本。')
        ->and(__('Init script added.'))->toBe('初始化脚本已添加。')
        ->and(__('Locked Secret, delete and add again to change'))->toBe('已锁定的密钥，如需修改请删除后重新添加')
        ->and(__('Multiline environment variable, edit in normal view'))->toBe('多行环境变量，请在常规视图中编辑')
        ->and(__('Duplicate environment variable key after normalization: :key.', ['key' => 'APP_KEY']))->toBe('环境变量键在规范化后重复：APP_KEY。')
        ->and(__('Key'))->toBe('键')
        ->and(__('The scheduled task name field is required.'))->toBe('必须填写计划任务名称。')
        ->and(__('The scheduled task command field is required.'))->toBe('必须填写计划任务命令。')
        ->and(__('The scheduled task frequency field is required.'))->toBe('必须填写计划任务频率。')
        ->and(__('The scheduled task timeout field is required.'))->toBe('必须填写计划任务超时时间。')
        ->and(__('The scheduled task timeout must be an integer.'))->toBe('计划任务超时时间必须是整数。')
        ->and(__('The scheduled task timeout must be at least :min seconds.', ['min' => 60]))->toBe('计划任务超时时间不能小于 60 秒。')
        ->and(__('The scheduled task timeout must not be greater than :max seconds.', ['max' => 36000]))->toBe('计划任务超时时间不能大于 36000 秒。')
        ->and(__('Waiting for task output...'))->toBe('正在等待任务输出...')
        ->and(__('Please select a container.'))->toBe('请选择一个容器。')
        ->and(__('Environment variable added.'))->toBe('环境变量已添加。')
        ->and(__('Required environment variables cannot be empty.'))->toBe('必填环境变量不能为空。')
        ->and(__('Environment variable updated.'))->toBe('环境变量已更新。')
        ->and(__('Environment variable deleted successfully.'))->toBe('环境变量已成功删除。')
        ->and(__('Health check updated.'))->toBe('健康检查已更新。')
        ->and(__('Health check enabled.'))->toBe('健康检查已启用。')
        ->and(__('Health check disabled.'))->toBe('健康检查已禁用。')
        ->and(__('Secret Saved.'))->toBe('密钥已保存。')
        ->and(__('Project updated.'))->toBe('项目已更新。')
        ->and(__('Invalid docker-compose file.'))->toBe('无效的 docker-compose 文件。')
        ->and(__('The provided password is incorrect.'))->toBe('输入的密码不正确。');
});
