<?php

use Illuminate\Support\Facades\App;

it('wires translation calls in shared ui follow-up files', function () {
    $componentsRoot = __DIR__.'/../../resources/views/components';
    $livewireRoot = __DIR__.'/../../resources/views/livewire';

    $modalConfirmation = file_get_contents($componentsRoot.'/modal-confirmation.blade.php');
    $domainConflictModal = file_get_contents($componentsRoot.'/domain-conflict-modal.blade.php');
    $statusRunning = file_get_contents($componentsRoot.'/status/running.blade.php');
    $storageResources = file_get_contents($livewireRoot.'/storage/resources.blade.php');
    $destinationNewDocker = file_get_contents($livewireRoot.'/destination/new/docker.blade.php');
    $globalSearch = file_get_contents($livewireRoot.'/global-search.blade.php');
    $serverProxy = file_get_contents($livewireRoot.'/server/proxy.blade.php');
    $serverSwarm = file_get_contents($livewireRoot.'/server/swarm.blade.php');
    $cloneMe = file_get_contents($livewireRoot.'/project/clone-me.blade.php');
    $deleteProject = file_get_contents($livewireRoot.'/project/delete-project.blade.php');
    $editDomain = file_get_contents($livewireRoot.'/project/service/edit-domain.blade.php');
    $fileStorage = file_get_contents($livewireRoot.'/project/service/file-storage.blade.php');
    $serviceStorage = file_get_contents($livewireRoot.'/project/service/storage.blade.php');
    $serviceIndex = file_get_contents($livewireRoot.'/project/service/index.blade.php');
    $serviceDatabaseBackups = file_get_contents($livewireRoot.'/project/service/database-backups.blade.php');
    $storagesShow = file_get_contents($livewireRoot.'/project/shared/storages/show.blade.php');
    $scheduledTaskAdd = file_get_contents($livewireRoot.'/project/shared/scheduled-task/add.blade.php');
    $scheduledTaskShow = file_get_contents($livewireRoot.'/project/shared/scheduled-task/show.blade.php');
    $scheduledTaskExecutions = file_get_contents($livewireRoot.'/project/shared/scheduled-task/executions.blade.php');
    $terminal = file_get_contents($livewireRoot.'/project/shared/terminal.blade.php');
    $configurationChecker = file_get_contents($livewireRoot.'/project/shared/configuration-checker.blade.php');

    expect($modalConfirmation)
        ->toContain("__('Password is required.')")
        ->toContain("__('Your Password')")
        ->and($domainConflictModal)
        ->toContain("__('I understand, proceed anyway')")
        ->and($statusRunning)
        ->toContain("__('No health check configured.")
        ->toContain("__('Unhealthy state.")
        ->and($storageResources)
        ->toContain("__('Deleted database')")
        ->toContain("__('Disable S3')")
        ->and($destinationNewDocker)
        ->toContain("__('Destinations are used to segregate resources by network.')")
        ->toContain("__('Select a server')")
        ->and($globalSearch)
        ->toContain("__('New Team')")
        ->toContain("__('New Private Key')")
        ->toContain("__('New GitHub App')")
        ->and($serverProxy)
        ->toContain("__('Custom proxy configurations may be reset to their default settings.')")
        ->toContain("__('Test the upgrade in a non-production environment first.')")
        ->and($serverSwarm)
        ->toContain("__('Read the docs <a class=\\'underline dark:text-white\\' href=\\'https://coolify.io/docs/knowledge-base/docker/swarm\\' target=\\'_blank\\'>here</a>.')")
        ->and($cloneMe)
        ->toContain("__('Quickly clone all resources to a new project or environment.')")
        ->toContain("__('Clone to new Project')")
        ->and($deleteProject)
        ->toContain("__('Confirm Project Deletion?')")
        ->toContain("__('Permanently Delete')")
        ->and($editDomain)
        ->toContain("__('Required Port: :port'")
        ->toContain("__('I understand, remove port anyway')")
        ->and($fileStorage)
        ->toContain("__('Source Path')")
        ->toContain("__('Load from server')")
        ->toContain("__('Content (refreshed after a successful deployment)')")
        ->and($serviceStorage)
        ->toContain("{{ __('Volumes') }}")
        ->toContain("__('No file mounts configured.')")
        ->and($serviceIndex)
        ->toContain("__('Convert to Database')")
        ->toContain("__('Service Application Name')")
        ->toContain("__('Convert to Application')")
        ->and($serviceDatabaseBackups)
        ->toContain("{{ __('Scheduled Backups') }}")
        ->toContain(":title=\"__('New Scheduled Backup')\"")
        ->and($storagesShow)
        ->toContain("__('This volume is mounted as read-only and cannot be modified from the UI.')")
        ->toContain("__('Confirm persistent storage deletion?')")
        ->toContain("__('Storage Name')")
        ->and($scheduledTaskAdd)
        ->toContain("__('Run cron')")
        ->toContain("__('Timeout (seconds)')")
        ->and($scheduledTaskShow)
        ->toContain("__('Task: :name'")
        ->toContain("__('Execute Now')")
        ->toContain("__('Scheduled Task Name')")
        ->and($scheduledTaskExecutions)
        ->toContain("__('Download Logs')")
        ->toContain("__('Task is running...')")
        ->toContain("__('No executions found.')")
        ->and($terminal)
        ->toContain("__('Terminal Not Available')")
        ->toContain("__('No shell (bash/sh) is available in this container. Please ensure either bash or sh is installed to use the terminal.')")
        ->and($configurationChecker)
        ->toContain("__('The latest configuration has not been applied')")
        ->toContain("__('Please redeploy to apply the new configuration.')")
        ->toContain("__('View changes')");
});

it('resolves representative shared ui follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Deleted database'))->toBe('已删除的数据库')
        ->and(__('Disable S3'))->toBe('禁用 S3')
        ->and(__('Password is required.'))->toBe('必须输入密码。')
        ->and(__('Your Password'))->toBe('你的密码')
        ->and(__('Select a server'))->toBe('选择服务器')
        ->and(__('No deployments running.'))->toBe('没有正在运行的部署。')
        ->and(__('configuration changes'))->toBe('项配置变更')
        ->and(__('Rebuild'))->toBe('重新构建')
        ->and(__('Custom (None)'))->toBe('自定义（无）')
        ->and(__('Read the docs <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/swarm\' target=\'_blank\'>here</a>.'))->toBe('请在<a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/swarm\' target=\'_blank\'>这里</a>查看文档。')
        ->and(__('Quickly clone all resources to a new project or environment.'))->toBe('快速将所有资源克隆到新项目或环境。')
        ->and(__('Clone to new Project'))->toBe('克隆到新项目')
        ->and(__('Confirm Project Deletion?'))->toBe('确认删除项目？')
        ->and(__('Permanently Delete'))->toBe('永久删除')
        ->and(__('Source Path'))->toBe('源路径')
        ->and(__('Load from server'))->toBe('从服务器加载')
        ->and(__('Volumes'))->toBe('卷')
        ->and(__('Files'))->toBe('文件')
        ->and(__('Directories'))->toBe('目录')
        ->and(__('No storage found.'))->toBe('未找到存储。')
        ->and(__('No file mounts configured.'))->toBe('未配置文件挂载。')
        ->and(__('Confirm persistent storage deletion?'))->toBe('确认删除持久存储？')
        ->and(__('Storage Name'))->toBe('存储名称')
        ->and(__('Task: :name', ['name' => '清理缓存']))->toBe('任务：清理缓存')
        ->and(__('Execute Now'))->toBe('立即执行')
        ->and(__('Scheduled Task Name'))->toBe('计划任务名称')
        ->and(__('Download Logs'))->toBe('下载日志')
        ->and(__('Task is running...'))->toBe('任务正在运行...')
        ->and(__('Convert to Database'))->toBe('转换为数据库')
        ->and(__('Service Application Name'))->toBe('服务应用名称')
        ->and(__('Convert to Application'))->toBe('转换为应用')
        ->and(__('Service Database Name'))->toBe('服务数据库名称')
        ->and(__('The latest configuration has not been applied'))->toBe('最新配置尚未应用')
        ->and(__('Please redeploy to apply the new configuration.'))->toBe('请重新部署以应用新配置。')
        ->and(__('View changes'))->toBe('查看变更')
        ->and(__('No shell (bash/sh) is available in this container. Please ensure either bash or sh is installed to use the terminal.'))->toBe('这个容器中没有可用的 shell（bash/sh）。请先确保已安装 bash 或 sh，才能使用终端。');
});
