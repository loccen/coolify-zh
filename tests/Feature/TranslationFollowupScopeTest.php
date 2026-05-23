<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in follow-up i18n views', function () {
    $viewsRoot = base_path('resources/views');

    $sentinel = file_get_contents($viewsRoot.'/livewire/server/sentinel.blade.php');
    $caCertificate = file_get_contents($viewsRoot.'/livewire/server/ca-certificate/show.blade.php');
    $dockerCleanup = file_get_contents($viewsRoot.'/livewire/server/docker-cleanup.blade.php');
    $proxy = file_get_contents($viewsRoot.'/livewire/server/proxy.blade.php');
    $proxyDynamicConfigurations = file_get_contents($viewsRoot.'/livewire/server/proxy/dynamic-configurations.blade.php');
    $proxyDynamicConfigurationNavbar = file_get_contents($viewsRoot.'/livewire/server/proxy/dynamic-configuration-navbar.blade.php');
    $proxyNewDynamicConfiguration = file_get_contents($viewsRoot.'/livewire/server/proxy/new-dynamic-configuration.blade.php');
    $executeContainerCommand = file_get_contents($viewsRoot.'/livewire/project/shared/execute-container-command.blade.php');
    $environmentVariableAdd = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/add.blade.php');
    $environmentVariableAll = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/all.blade.php');
    $backupEdit = file_get_contents($viewsRoot.'/livewire/project/database/backup-edit.blade.php');
    $backupNow = file_get_contents($viewsRoot.'/livewire/project/database/backup-now.blade.php');
    $dockerImage = file_get_contents($viewsRoot.'/livewire/project/new/docker-image.blade.php');
    $simpleDockerfile = file_get_contents($viewsRoot.'/livewire/project/new/simple-dockerfile.blade.php');
    $publicGitRepository = file_get_contents($viewsRoot.'/livewire/project/new/public-git-repository.blade.php');
    $githubPrivateRepository = file_get_contents($viewsRoot.'/livewire/project/new/github-private-repository.blade.php');
    $deployKeyRepository = file_get_contents($viewsRoot.'/livewire/project/new/github-private-repository-deploy-key.blade.php');
    $dockerCompose = file_get_contents($viewsRoot.'/livewire/project/new/docker-compose.blade.php');
    $newResourceSelect = file_get_contents($viewsRoot.'/livewire/project/new/select.blade.php');
    $projectAddEmpty = file_get_contents($viewsRoot.'/livewire/project/add-empty.blade.php');
    $deleteEnvironment = file_get_contents($viewsRoot.'/livewire/project/delete-environment.blade.php');
    $applicationAdvanced = file_get_contents($viewsRoot.'/livewire/project/application/advanced.blade.php');
    $environmentVariableShow = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/show.blade.php');
    $environmentVariableShowHardcoded = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/show-hardcoded.blade.php');
    $resourceLimits = file_get_contents($viewsRoot.'/livewire/project/shared/resource-limits.blade.php');
    $resourceOperations = file_get_contents($viewsRoot.'/livewire/project/shared/resource-operations.blade.php');
    $metrics = file_get_contents($viewsRoot.'/livewire/project/shared/metrics.blade.php');
    $danger = file_get_contents($viewsRoot.'/livewire/project/shared/danger.blade.php');
    $healthChecks = file_get_contents($viewsRoot.'/livewire/project/shared/health-checks.blade.php');
    $destination = file_get_contents($viewsRoot.'/livewire/project/shared/destination.blade.php');
    $tags = file_get_contents($viewsRoot.'/livewire/project/shared/tags.blade.php');
    $scheduledTasks = file_get_contents($viewsRoot.'/livewire/project/shared/scheduled-task/all.blade.php');
    $resourceIndex = file_get_contents($viewsRoot.'/livewire/project/resource/index.blade.php');
    $resourceCreate = file_get_contents($viewsRoot.'/livewire/project/resource/create.blade.php');
    $databaseBackupIndex = file_get_contents($viewsRoot.'/livewire/project/database/backup/index.blade.php');
    $databaseBackupExecution = file_get_contents($viewsRoot.'/livewire/project/database/backup/execution.blade.php');
    $databaseCreateScheduledBackup = file_get_contents($viewsRoot.'/livewire/project/database/create-scheduled-backup.blade.php');
    $databaseScheduledBackups = file_get_contents($viewsRoot.'/livewire/project/database/scheduled-backups.blade.php');

    expect($sentinel)
        ->toContain("{{ __('Sentinel') }}")
        ->toContain("__('Enable Sentinel')")
        ->toContain("__('Metrics rate (seconds)')")
        ->and($caCertificate)
        ->toContain("{{ __('CA Certificate') }}")
        ->toContain("__('Recommended Configuration:')")
        ->toContain("__('CERTIFICATE CONTENT')")
        ->and($dockerCleanup)
        ->toContain("{{ __('Docker Cleanup') }}")
        ->toContain("__('Force Docker Cleanup')")
        ->toContain("__('Recent executions')")
        ->and($proxy)
        ->toContain("{{ __('Configuration') }}")
        ->toContain("__('Switch Proxy')")
        ->toContain("__('Configuration file ( :path )'")
        ->and($proxyDynamicConfigurations)
        ->toContain("{{ __('Proxy Dynamic Configuration') }}")
        ->toContain("__('Loading dynamic configurations...')")
        ->and($proxyDynamicConfigurationNavbar)
        ->toContain("{{ __('File:') }}")
        ->toContain("__('Edit')")
        ->and($proxyNewDynamicConfiguration)
        ->toContain("__('Filename')")
        ->and($executeContainerCommand)
        ->toContain("{{ __('Terminal') }}")
        ->toContain("__('Container')")
        ->and($environmentVariableAdd)
        ->toContain("__('Comment')")
        ->toContain("__('Is Multiline?')")
        ->toContain("__('Tip: Type')")
        ->toContain('&#123;&#123;')
        ->and($environmentVariableAll)
        ->toContain("__('Environment Variables')")
        ->toContain("__('Production Environment Variables')")
        ->toContain("__('Save All Environment Variables')")
        ->and($backupEdit)
        ->toContain("{{ __('Scheduled Backup') }}")
        ->toContain("__('Backup Retention Settings')")
        ->toContain("__('S3 Enabled')")
        ->and($backupNow)
        ->toContain("{{ __('Backup Now') }}")
        ->and($dockerImage)
        ->toContain("{{ __('Create a new Application') }}")
        ->toContain("__('Image Name')")
        ->toContain("__('SHA256 Digest (optional)')")
        ->and($simpleDockerfile)
        ->toContain("{{ __('Create a new Application') }}")
        ->toContain("{{ __('Dockerfile') }}")
        ->and($publicGitRepository)
        ->toContain("{{ __('Deploy any public Git repositories.') }}")
        ->toContain("{{ __('Check repository') }}")
        ->and($githubPrivateRepository)
        ->toContain("{{ __('Deploy any public or private Git repositories through a GitHub App.') }}")
        ->toContain("{{ __('Refresh Repository List') }}")
        ->and($deployKeyRepository)
        ->toContain("{{ __('Deploy any public or private Git repositories through a Deploy Key.') }}")
        ->toContain("{{ __('Create a new private key') }}")
        ->and($dockerCompose)
        ->toContain("{{ __('Create a new Service') }}")
        ->toContain("label=\"{{ __('Docker Compose file') }}\"")
        ->and($newResourceSelect)
        ->toContain("{{ __('Select a server') }}")
        ->toContain("{{ __('Select a destination') }}")
        ->and($projectAddEmpty)
        ->toContain("{{ __('Continue') }}")
        ->toContain("__('New project will have a default :environment environment.'")
        ->and($deleteEnvironment)
        ->toContain("title=\"{{ __('Confirm Environment Deletion?') }}\"")
        ->toContain("buttonTitle=\"{{ __('Delete Environment') }}\"")
        ->and($applicationAdvanced)
        ->toContain("{{ __('Advanced') }}")
        ->toContain("__('Disable Build Cache')")
        ->toContain("__('Operations')")
        ->and($environmentVariableShow)
        ->toContain("__('Comment')")
        ->toContain("__('Environment Variable Name')")
        ->toContain("__('Is Literal?')")
        ->and($environmentVariableShowHardcoded)
        ->toContain("__('Hardcoded env')")
        ->toContain("__('Service:')")
        ->toContain("__('Documentation for this environment variable.')")
        ->and($resourceLimits)
        ->toContain("{{ __('Resource Limits') }}")
        ->toContain("__('Limit CPUs')")
        ->toContain("__('Maximum Memory Limit')")
        ->and($resourceOperations)
        ->toContain("{{ __('Resource Operations') }}")
        ->toContain("__('Clone Resource')")
        ->toContain("__('Move Resource')")
        ->and($metrics)
        ->toContain("{{ __('Metrics') }}")
        ->toContain("__('Metrics are only available for servers with Sentinel & Metrics enabled!')")
        ->toContain("__('Server settings')")
        ->and($danger)
        ->toContain("{{ __('Danger Zone') }}")
        ->toContain("__('Confirm Resource Deletion?')")
        ->toContain("__('Resource Name')")
        ->and($healthChecks)
        ->toContain("{{ __('Healthchecks') }}")
        ->toContain("__('Enable healthcheck for this resource.')")
        ->toContain("__('Start Period (s)')")
        ->and($destination)
        ->toContain("{{ __('Primary Server') }}")
        ->toContain("__('Promote to Primary')")
        ->toContain("__('No additional servers available to attach.')")
        ->and($tags)
        ->toContain("{{ __('Assigned Tags') }}")
        ->toContain("__('Create new or assign existing tags')")
        ->toContain("__('Click to add quickly')")
        ->and($scheduledTasks)
        ->toContain("{{ __('Scheduled Tasks') }}")
        ->toContain("__('Last run')")
        ->toContain("__('No runs yet')")
        ->and($resourceIndex)
        ->toContain("{{ __('New') }}")
        ->toContain("title=\"{{ __('running') }}\"")
        ->toContain("{{ __('Server:') }}")
        ->toContain("{{ __('Server is unreachable or misconfigured') }}")
        ->toContain("{{ __('Add tag') }}")
        ->and($resourceCreate)
        ->toContain("{{ __('New') }}")
        ->and($databaseBackupIndex)
        ->toContain("{{ __('Backups') }}")
        ->toContain("{{ __('Scheduled Backups') }}")
        ->toContain(":title=\"__('New Scheduled Backup')\"")
        ->and($databaseBackupExecution)
        ->toContain("{{ __('Backup') }} | Coolify")
        ->toContain("{{ __('Backups') }}")
        ->and($databaseCreateScheduledBackup)
        ->toContain("__('No validated S3 Storages found.')")
        ->toContain("__('Save to S3')")
        ->toContain("__('Select a S3 Storage')")
        ->and($databaseScheduledBackups)
        ->toContain("{{ __('No scheduled backups configured.') }}");
});

it('uses explicit translation lookups in database general proxy sections', function () {
    $generalViews = glob(base_path('resources/views/livewire/project/database/*/general.blade.php'));

    expect($generalViews)->not->toBeEmpty();

    foreach ($generalViews as $viewPath) {
        $contents = file_get_contents($viewPath);

        expect($contents)
            ->toContain("__('Proxy')")
            ->toContain("__('Proxy Logs')")
            ->toContain("__('Logs')")
            ->toContain("__('Make it publicly available')")
            ->toContain("__('Public Port')")
            ->toContain("__('Proxy Timeout (seconds)')")
            ->toContain("__('Advanced')")
            ->toContain("__('Drain Logs')");
    }
});

it('uses explicit translation lookups in database general settings sections', function () {
    $generalViews = glob(base_path('resources/views/livewire/project/database/*/general.blade.php'));

    expect($generalViews)->not->toBeEmpty();

    foreach ($generalViews as $viewPath) {
        $contents = file_get_contents($viewPath);

        expect($contents)
            ->toContain("__('General')")
            ->toContain("__('Name')")
            ->toContain("__('Description')")
            ->toContain("__('Image')")
            ->toContain("__('Custom Docker Options')")
            ->toContain("__('Network')")
            ->toContain("__('Ports Mappings')");

        if (str_contains($contents, "__('Enable SSL')") || str_contains($contents, 'Regenerate SSL Certificates')) {
            expect($contents)->toContain("__('SSL Configuration')");
        }
    }
});

it('resolves representative follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Comment'))->toBe('备注')
        ->and(__('Image Name'))->toBe('镜像名称')
        ->and(__('Public Repository'))->toBe('公开仓库')
        ->and(__('Private Repository (with GitHub App)'))->toBe('私有仓库（使用 GitHub App）')
        ->and(__('Private Repository (with Deploy Key)'))->toBe('私有仓库（使用 Deploy Key）')
        ->and(__('Docker Compose Empty'))->toBe('空白 Docker Compose')
        ->and(__('Check repository'))->toBe('检查仓库')
        ->and(__('Refresh Repository List'))->toBe('刷新仓库列表')
        ->and(__('Delete Environment'))->toBe('删除环境')
        ->and(__('Permanently Delete'))->toBe('永久删除')
        ->and(__('Is Multiline?'))->toBe('多行值？')
        ->and(__('Tip: Type'))->toBe('提示：输入')
        ->and(__('to reference a shared environment variable'))->toBe('可引用共享环境变量')
        ->and(__('Commands'))->toBe('命令')
        ->and(__('Backup Now'))->toBe('立即备份')
        ->and(__('Proxy Dynamic Configuration'))->toBe('代理动态配置')
        ->and(__('File:'))->toBe('文件：')
        ->and(__('In sync'))->toBe('已同步')
        ->and(__('Out of sync'))->toBe('未同步')
        ->and(__('Build'))->toBe('构建')
        ->and(__('Operations'))->toBe('操作')
        ->and(__('Environment Variable Name'))->toBe('环境变量名称')
        ->and(__('Environment Variables'))->toBe('环境变量')
        ->and(__('Preview Deployments Environment Variables'))->toBe('Preview Deployments 环境变量')
        ->and(__('Production Environment Variables'))->toBe('生产环境变量')
        ->and(__('Save All Environment Variables'))->toBe('保存所有环境变量')
        ->and(__('Hardcoded env'))->toBe('硬编码环境变量')
        ->and(__('Service:'))->toBe('服务：')
        ->and(__('(inherited from host)'))->toBe('（继承自主机）')
        ->and(__('Documentation for this environment variable.'))->toBe('这个环境变量的说明。')
        ->and(__('Resource Operations'))->toBe('资源操作')
        ->and(__('Assigned Tags'))->toBe('已分配标签')
        ->and(__('Add tag'))->toBe('添加标签')
        ->and(__('Choose a destination...'))->toBe('选择目标位置...')
        ->and(__('Last run'))->toBe('上次运行')
        ->and(__('Promote to Primary'))->toBe('设为主服务器')
        ->and(__('Resource Name'))->toBe('资源名称')
        ->and(__('New'))->toBe('新建')
        ->and(__('Backups'))->toBe('备份')
        ->and(__('Scheduled Backups'))->toBe('计划备份')
        ->and(__('No scheduled backups configured.'))->toBe('未配置计划备份。')
        ->and(__('No validated S3 Storages found.'))->toBe('未找到经过验证的 S3 存储。')
        ->and(__('Save to S3'))->toBe('保存到S3')
        ->and(__('Select a S3 Storage'))->toBe('选择 S3 存储')
        ->and(__('Server:'))->toBe('服务器：')
        ->and(__('Server is unreachable or misconfigured'))->toBe('服务器无法访问或配置有误')
        ->and(__('running'))->toBe('运行中')
        ->and(__('exited'))->toBe('已退出')
        ->and(__('starting'))->toBe('启动中')
        ->and(__('restarting'))->toBe('重启中')
        ->and(__('degraded'))->toBe('已降级')
        ->and(__('Proxy Logs'))->toBe('代理日志')
        ->and(__('Logs'))->toBe('日志')
        ->and(__('Public Port'))->toBe('公共端口')
        ->and(__('Proxy Timeout (seconds)'))->toBe('代理超时（秒）')
        ->and(__('Enable SSL'))->toBe('启用 SSL')
        ->and(__('Please verify these values. You can only modify them before the initial start. After that, you need to modify it in the database.'))->toBe('请确认这些值。它们只能在首次启动前修改，启动之后需要直接在数据库中变更。')
        ->and(__('Starting the database will generate this.'))->toBe('启动数据库后会自动生成此值。')
        ->and(__('If you change the values in the database, please sync it here, otherwise automations won\'t work.'))->toBe('如果你在数据库中修改了这些值，请在这里同步，否则自动化功能将无法工作。')
        ->and(__('Changing them here will not change the values in the database.'))->toBe('在这里修改不会改变数据库中的实际值。')
        ->and(__('You can only change the username and password in the database after initial start.'))->toBe('首次启动后，你只能直接在数据库中修改用户名和密码。')
        ->and(__('The SSL certificate of this database will be regenerated.'))->toBe('这个数据库的 SSL 证书将被重新生成。')
        ->and(__('You must restart the database after regenerating the certificate to start using the new certificate.'))->toBe('重新生成证书后，你必须重启数据库才能使用新的证书。')
        ->and(__('Custom ClickHouse Configuration'))->toBe('自定义 ClickHouse 配置')
        ->and(__('Custom Dragonfly Configuration'))->toBe('自定义 Dragonfly 配置')
        ->and(__('settings.instance_updated'))->toBe('设置已更新。')
        ->and(trans('settings.instance_updated'))->toBe('设置已更新。');
});
