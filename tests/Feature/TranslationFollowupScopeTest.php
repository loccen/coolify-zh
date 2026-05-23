<?php

use App\Support\ValidationPatterns;
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
    $applicationDeploymentShow = file_get_contents($viewsRoot.'/livewire/project/application/deployment/show.blade.php');
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
    $databaseKeydbGeneral = file_get_contents($viewsRoot.'/livewire/project/database/keydb/general.blade.php');
    $databaseScheduledBackups = file_get_contents($viewsRoot.'/livewire/project/database/scheduled-backups.blade.php');
    $applicationPreviewsCompose = file_get_contents($viewsRoot.'/livewire/project/application/previews-compose.blade.php');
    $serviceStackForm = file_get_contents($viewsRoot.'/livewire/project/service/stack-form.blade.php');

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
        ->toContain("{{ __('Static') }}")
        ->and($deployKeyRepository)
        ->toContain("{{ __('Deploy any public or private Git repositories through a Deploy Key.') }}")
        ->toContain("{{ __('Create a new private key') }}")
        ->toContain("{{ __('Static') }}")
        ->and($dockerCompose)
        ->toContain("{{ __('Create a new Service') }}")
        ->toContain("label=\"{{ __('Docker Compose file') }}\"")
        ->and($publicGitRepository)
        ->toContain("{{ __('Deploy any public Git repositories.') }}")
        ->toContain("{{ __('Static') }}")
        ->and($newResourceSelect)
        ->toContain("{{ __('Select a server') }}")
        ->toContain("{{ __('Select a destination') }}")
        ->toContain("x-text=\"selectedCategory === '' ? `{{ __('Filter by category') }}` : selectedCategory\"")
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
        ->and($applicationDeploymentShow)
        ->toContain("{{ __('Deployment') }}")
        ->toContain("__('Deployment is')")
        ->toContain("__('No logs yet.')")
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
        ->toContain("x-text=\"availableEnvironments.length === 0 && isCurrentProjectSelected ? noOtherEnvironmentsLabel : `{{ __('Choose an environment...') }}`\"")
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
        ->toContain("__('HTTP')")
        ->toContain("__('CMD')")
        ->and($databaseKeydbGeneral)
        ->toContain("__('View the <a target=\\'_blank\\' class=\\'underline dark:text-white\\' href=\\'https://raw.githubusercontent.com/Snapchat/KeyDB/unstable/keydb.conf\\'>KeyDB default configuration</a>.')")
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

    expect($applicationPreviewsCompose)
        ->toContain("__('One domain per preview.')")
        ->toContain("__('Domains for :serviceName'")
        ->toContain("{{ __('Save') }}")
        ->toContain("{{ __('Generate Domain') }}");

    expect($serviceStackForm)
        ->toContain("__('My super WordPress site')");
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
        ->and(__('Private Repository (with Deploy Key)'))->toBe('私有仓库（使用部署密钥）')
        ->and(__('Docker Compose Empty'))->toBe('空白 Docker Compose')
        ->and(__('Check repository'))->toBe('检查仓库')
        ->and(__('Refresh Repository List'))->toBe('刷新仓库列表')
        ->and(__('Deployment'))->toBe('部署')
        ->and(__('Deployment is'))->toBe('部署状态：')
        ->and(__('Delete Environment'))->toBe('删除环境')
        ->and(__('Permanently Delete'))->toBe('永久删除')
        ->and(__('One domain per preview.'))->toBe('每个预览只能使用一个域名。')
        ->and(__('My super WordPress site'))->toBe('我的 WordPress 站点')
        ->and(__('Is Multiline?'))->toBe('多行值？')
        ->and(__('Tip: Type'))->toBe('提示：输入')
        ->and(__('to reference a shared environment variable'))->toBe('可引用共享环境变量')
        ->and(__('Commands'))->toBe('命令')
        ->and(__('HTTP'))->toBe('HTTP')
        ->and(__('CMD'))->toBe('命令')
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
        ->and(__('View the <a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://raw.githubusercontent.com/Snapchat/KeyDB/unstable/keydb.conf\'>KeyDB default configuration</a>.'))->toBe('查看<a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://raw.githubusercontent.com/Snapchat/KeyDB/unstable/keydb.conf\'>KeyDB 默认配置</a>。')
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
        ->and(__('You can change the Redis Username in the input field below or by editing the value of the REDIS_USERNAME environment variable.<br><br>If you change the Redis Username in the database, please sync it here, otherwise automations (like backups) won\'t work.<br><br>Note: If the environment variable REDIS_USERNAME is set as a shared variable (environment, project, or team-based), this input field will become read-only.'))->toBe('你可以在下方输入框中修改 Redis 用户名，或直接编辑 `REDIS_USERNAME` 环境变量的值。<br><br>如果你在数据库中修改了 Redis 用户名，请在这里同步，否则自动化功能（如备份）将无法工作。<br><br>注意：如果 `REDIS_USERNAME` 被设置为共享变量（环境、项目或团队级），这个输入框会变成只读。')
        ->and(__('You can change the Redis Password in the input field below or by editing the value of the REDIS_PASSWORD environment variable.<br><br>If you change the Redis Password in the database, please sync it here, otherwise automations (like backups) won\'t work.<br><br>Note: If the environment variable REDIS_PASSWORD is set as a shared variable (environment, project, or team-based), this input field will become read-only.'))->toBe('你可以在下方输入框中修改 Redis 密码，或直接编辑 `REDIS_PASSWORD` 环境变量的值。<br><br>如果你在数据库中修改了 Redis 密码，请在这里同步，否则自动化功能（如备份）将无法工作。<br><br>注意：如果 `REDIS_PASSWORD` 被设置为共享变量（环境、项目或团队级），这个输入框会变成只读。')
        ->and(__('settings.instance_updated'))->toBe('设置已更新。')
        ->and(trans('settings.instance_updated'))->toBe('设置已更新。');
});

it('uses explicit translation lookups in additional follow-up views', function () {
    $viewsRoot = base_path('resources/views');

    $applicationAdvanced = file_get_contents($viewsRoot.'/livewire/project/application/advanced.blade.php');
    $applicationGeneral = file_get_contents($viewsRoot.'/livewire/project/application/general.blade.php');
    $applicationHeading = file_get_contents($viewsRoot.'/livewire/project/application/heading.blade.php');
    $applicationSource = file_get_contents($viewsRoot.'/livewire/project/application/source.blade.php');
    $projectEdit = file_get_contents($viewsRoot.'/livewire/project/edit.blade.php');
    $environmentEdit = file_get_contents($viewsRoot.'/livewire/project/environment-edit.blade.php');
    $databaseHeading = file_get_contents($viewsRoot.'/livewire/project/database/heading.blade.php');
    $resourceIndex = file_get_contents($viewsRoot.'/livewire/project/resource/index.blade.php');
    $databaseScheduledBackups = file_get_contents($viewsRoot.'/livewire/project/database/scheduled-backups.blade.php');
    $environmentVariableShow = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/show.blade.php');
    $newResourceSelect = file_get_contents($viewsRoot.'/livewire/project/new/select.blade.php');

    expect($applicationAdvanced)
        ->toContain('By default, you do not reach the Coolify defined networks.')
        ->toContain('Enable GPU usage for this application. More info')
        ->toContain('WARNING: Advanced use cases only.')
        ->toContain('You can add a custom name for your container.')
        ->and($applicationGeneral)
        ->toContain("__('Application URL')")
        ->toContain("__('All traffic will be redirected to the selected direction.')")
        ->toContain("__('This will overwrite your current custom Nginx configuration.')")
        ->and($applicationHeading)
        ->toContain("__('Container has restarted')")
        ->toContain("__('This application will be stopped.')")
        ->toContain("__('Update Service')")
        ->and($applicationSource)
        ->toContain("__('current')")
        ->and($projectEdit)
        ->toContain("{{ __('Edit') }} | Coolify")
        ->toContain("{{ __('Save') }}")
        ->toContain("{{ __('Edit project details here.') }}")
        ->toContain(":label=\"__('Name')\"")
        ->toContain(":label=\"__('Description')\"")
        ->and($environmentEdit)
        ->toContain("{{ __('Edit') }} | Coolify")
        ->toContain("{{ __('Environment') }}:")
        ->toContain("{{ __('Save') }}")
        ->toContain("{{ __('Edit') }}")
        ->toContain(":label=\"__('Name')\"")
        ->toContain(":label=\"__('Description')\"")
        ->and($databaseHeading)
        ->toContain("__('If the database is currently in use data could be lost.')")
        ->toContain("__('Restarting database.')")
        ->toContain("__('This database will be stopped.')")
        ->and($resourceIndex)
        ->toContain("__('Create / Edit')")
        ->toContain("__('Try adjusting your search criteria.')")
        ->toContain("__('Contact your team administrator to add resources.')")
        ->and($databaseScheduledBackups)
        ->toContain("__('No executions yet')")
        ->and($environmentVariableShow)
        ->toContain("__('Please confirm the execution of the actions by entering the Environment Variable Name below')")
        ->and($newResourceSelect)
        ->toContain("__('AMD only')")
        ->toContain("__('ARM only')");
});

it('resolves additional follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Please confirm the execution of the actions by entering the Environment Variable Name below'))->toBe('请输入下方的环境变量名称以确认执行这些操作')
        ->and(__('By default, you do not reach the Coolify defined networks.<br>Starting a docker compose based resource will have an internal network. <br>If you connect to a Coolify defined network, you maybe need to use different internal DNS names to connect to a resource.<br><br>For more information, check <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://coolify.io/docs/knowledge-base/docker/compose#connect-to-predefined-networks\'>this</a>.'))->toBe('默认情况下，你无法访问 Coolify 定义的网络。启动基于 docker compose 的资源时，会创建一个内部网络。<br>如果你连接到 Coolify 定义的网络，可能需要使用不同的内部 DNS 名称来连接资源。<br><br>更多信息请查看<a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://coolify.io/docs/knowledge-base/docker/compose#connect-to-predefined-networks\'>这里</a>。')
        ->and(__('Comma separated list of device ids. More info <a href=\'https://docs.docker.com/compose/gpu-support/#access-specific-devices\' class=\'underline dark:text-white\' target=\'_blank\'>here</a>.'))->toBe('用逗号分隔的设备 ID 列表。更多信息请查看<a href=\'https://docs.docker.com/compose/gpu-support/#access-specific-devices\' class=\'underline dark:text-white\' target=\'_blank\'>这里</a>。')
        ->and(__('Enable GPU usage for this application. More info <a href=\'https://docs.docker.com/compose/gpu-support/\' class=\'underline dark:text-white\' target=\'_blank\'>here</a>.'))->toBe('为此应用启用 GPU。更多信息请查看<a href=\'https://docs.docker.com/compose/gpu-support/\' class=\'underline dark:text-white\' target=\'_blank\'>这里</a>。')
        ->and(__('WARNING: Advanced use cases only. Your docker compose file will be deployed as-is. Nothing is modified by Coolify. You need to configure the proxy parts. More info in the <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/compose#raw-docker-compose-deployment\'>documentation.</a>'))->toBe('警告：仅适用于高级场景。你的 docker compose 文件会按原样部署，Coolify 不会做任何修改。你需要自行配置代理相关部分。更多信息请查看<a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/compose#raw-docker-compose-deployment\'>文档</a>。')
        ->and(__('You can add a custom name for your container.<br><br>The name will be converted to slug format when you save it. <span class=\'font-bold dark:text-warning\'>You will lose the rolling update feature!</span>'))->toBe('你可以为容器设置自定义名称。<br><br>保存时会自动转换为 slug 格式。<span class=\'font-bold dark:text-warning\'>你将失去滚动更新功能！</span>')
        ->and(__('Close'))->toBe('关闭')
        ->and(__('AMD only'))->toBe('仅支持 AMD')
        ->and(__('ARM only'))->toBe('仅支持 ARM')
        ->and(__('This service only supports AMD64/x86_64 architecture. It will not work on ARM-based servers (e.g., Apple Silicon, Raspberry Pi, AWS Graviton).'))->toBe('此服务仅支持 AMD64/x86_64 架构，无法在基于 ARM 的服务器上运行（例如 Apple Silicon、Raspberry Pi、AWS Graviton）。')
        ->and(__('This service only supports ARM64/aarch64 architecture. It will not work on AMD64/x86_64-based servers.'))->toBe('此服务仅支持 ARM64/aarch64 架构，无法在基于 AMD64/x86_64 的服务器上运行。')
        ->and(__('Create / Edit'))->toBe('创建 / 编辑')
        ->and(__('Try adjusting your search criteria.'))->toBe('试着调整搜索条件。')
        ->and(__('Contact your team administrator to add resources.'))->toBe('请联系团队管理员添加资源。')
        ->and(__('No executions yet'))->toBe('还没有执行记录')
        ->and(__('Edit project details here.'))->toBe('在这里编辑项目详情。')
        ->and(__('Environment'))->toBe('环境')
        ->and(__('A powerful Markdown workspace designed for speed, clarity, and creativity.'))->toBe('为速度、清晰度和创造力而设计的强大文档工作区。')
        ->and(__('AnythingLLM is the easiest to use, all-in-one AI application that can do RAG, AI Agents, and much more with no code or infrastructure headaches.'))->toBe('AnythingLLM 是一款易用的一体化智能应用，可实现检索增强生成、智能代理等功能，无需编写代码，也不用折腾基础设施。')
        ->and(__('AppFlowy is the AI collaborative workspace where you achieve more without losing control of your data.'))->toBe('AppFlowy 是一个智能协作工作空间，让你在不失去数据控制权的前提下完成更多工作。')
        ->and(__('A backend-as-a-service platform that simplifies the web & mobile app development.'))->toBe('简化网页和移动应用开发的后端即服务平台。')
        ->and(__('Process PDFs entirely in your browser. No uploads. No servers. Complete privacy.'))->toBe('完全在浏览器中处理文档。无需上传，无需服务器，隐私完全可控。');
});

it('uses explicit translation lookups in project validation message sources', function () {
    $validationPatterns = file_get_contents(app_path('Support/ValidationPatterns.php'));
    $applicationGeneral = file_get_contents(app_path('Livewire/Project/Application/General.php'));
    $cloneMe = file_get_contents(app_path('Livewire/Project/CloneMe.php'));
    $serviceStackForm = file_get_contents(app_path('Livewire/Project/Service/StackForm.php'));
    $resourceLimits = file_get_contents(app_path('Livewire/Project/Shared/ResourceLimits.php'));
    $storagesShow = file_get_contents(app_path('Livewire/Project/Shared/Storages/Show.php'));
    $postgresqlGeneral = file_get_contents(app_path('Livewire/Project/Database/Postgresql/General.php'));
    $redisGeneral = file_get_contents(app_path('Livewire/Project/Database/Redis/General.php'));

    expect($validationPatterns)
        ->toContain("__('The :label may only contain letters, digits, and underscores, and must start with a letter or underscore.'")
        ->toContain("__('The :label may not contain shell-unsafe characters (backtick, $, ;, |, &, <, >, \\\\, quotes, spaces, or control characters).'")
        ->toContain("__('Port mappings must be a comma-separated list of port pairs or ranges with optional IP and protocol (e.g. 3000:3000, 8080:80/udp, 127.0.0.1:8080:80, [::1]::80).')")
        ->and($applicationGeneral)
        ->toContain("__('The Git Repository field is required.')")
        ->toContain("__('The Docker Compose start command contains invalid characters.")
        ->toContain("__('The Build Server setting is required.')")
        ->and($cloneMe)
        ->toContain("__('Please select a server.')")
        ->toContain("__('Please enter a name for the new project or environment.')")
        ->and($serviceStackForm)
        ->toContain("__('The Docker Compose Raw field is required.')")
        ->and($resourceLimits)
        ->toContain("'limitsMemory.regex' => '最大内存限制必须是带单位的数字（b、k、m、g）。例如 256m、1g。填 0 表示不限制。'")
        ->and($storagesShow)
        ->toContain("__('Mount path must start with / and only contain safe path characters.')")
        ->and($postgresqlGeneral)
        ->toContain("__('The SSL Mode must be one of: allow, prefer, require, verify-ca, verify-full.')")
        ->and($redisGeneral)
        ->toContain("__('The Docker Image field is required.')");
});

it('resolves project validation messages in zh_CN at runtime', function () {
    App::setLocale('zh_CN');

    $identifierMessages = ValidationPatterns::databaseIdentifierMessages('mysqlUser', 'MySQL User');
    $passwordMessages = ValidationPatterns::databasePasswordMessages('mysqlPassword', 'MySQL Password');
    $filePathMessages = ValidationPatterns::filePathMessages('dockerComposeLocation', 'Docker Compose');
    $nameMessages = ValidationPatterns::nameMessages();
    $descriptionMessages = ValidationPatterns::descriptionMessages();
    $portMappingMessages = ValidationPatterns::portMappingMessages();

    expect($identifierMessages['mysqlUser.regex'])->toBe('MySQL 用户只能包含字母、数字和下划线，并且必须以字母或下划线开头。')
        ->and($passwordMessages['mysqlPassword.regex'])->toBe('MySQL 密码不能包含对 shell 不安全的字符（反引号、$、;、|、&、<、>、\\、引号、空格或控制字符）。')
        ->and($filePathMessages['dockerComposeLocation.regex'])->toBe('Docker Compose路径必须是以 / 开头的有效路径，并且只能包含字母数字、点、短横线、下划线、斜杠、@、~ 和 +。')
        ->and($nameMessages['name.regex'])->toBe('名称只能包含字母（含 Unicode）、数字、空格，以及这些字符：- _ . / @ & ( ) # , : +')
        ->and($descriptionMessages['description.max'])->toBe('描述长度不能超过 :max 个字符。')
        ->and($portMappingMessages['portsMappings.regex'])->toBe('端口映射必须是用逗号分隔的端口对或端口范围列表，可选带 IP 和协议（例如 3000:3000、8080:80/udp、127.0.0.1:8080:80、[::1]::80）。')
        ->and(__('Please select a server.'))->toBe('请选择一个服务器。')
        ->and(__('Please select a server & destination.'))->toBe('请选择服务器和目标环境。')
        ->and(__('Please enter a name for the new project or environment.'))->toBe('请输入新项目或环境的名称。')
        ->and(__('The Docker Compose Raw field is required.'))->toBe('必须填写 Docker Compose 原文。')
        ->and(__('The Docker Compose field is required.'))->toBe('必须填写 Docker Compose。')
        ->and(__('The Git Repository field is required.'))->toBe('必须填写 Git 仓库。')
        ->and(__('The Build Pack field is required.'))->toBe('必须填写构建包。')
        ->and(__('The Base Directory field is required.'))->toBe('必须填写基础目录。')
        ->and(__('The Docker Image field is required.'))->toBe('必须填写 Docker 镜像。')
        ->and(__('The Public Port must be an integer.'))->toBe('公共端口必须是整数。')
        ->and(__('The Public Port must not exceed 65535.'))->toBe('公共端口不能超过 65535。')
        ->and(__('The SSL Mode must be one of: allow, prefer, require, verify-ca, verify-full.'))->toBe('SSL 模式必须是以下之一：allow、prefer、require、verify-ca、verify-full。')
        ->and(__('The Docker Compose start command contains invalid characters. Allowed: alphanumerics, && / || chaining, balanced quotes, globs (*, ?), !, and safe path/arg chars. Blocked: bare &, bare |, ;, $, backtick, (, ), <, >, \\, newlines.'))->toBe('Docker Compose 启动命令包含无效字符。允许：字母数字、&& / || 串联、成对引号、通配符（*、?）、! 以及安全的路径 / 参数字符。禁止：裸露的 &、裸露的 |、;、$、反引号、(、)、<、>、\\ 和换行。')
        ->and(__('Maximum Memory Limit must be a number followed by a unit (b, k, m, g). Example: 256m, 1g. Use 0 for unlimited.'))->toBe('最大内存限制必须是带单位的数字（b、k、m、g）。例如 256m、1g。填 0 表示不限制。')
        ->and(__('Mount path must start with / and only contain safe path characters.'))->toBe('挂载路径必须以 / 开头，并且只能包含安全路径字符。')
        ->and(__('Resource not found.'))->toBe('未找到资源。');
});

it('uses explicit translation lookups in project runtime error sources', function () {
    $cloneMe = file_get_contents(app_path('Livewire/Project/CloneMe.php'));
    $dockerCompose = file_get_contents(app_path('Livewire/Project/New/DockerCompose.php'));
    $dockerImage = file_get_contents(app_path('Livewire/Project/New/DockerImage.php'));
    $simpleDockerfile = file_get_contents(app_path('Livewire/Project/New/SimpleDockerfile.php'));
    $githubPrivateRepository = file_get_contents(app_path('Livewire/Project/New/GithubPrivateRepository.php'));
    $githubPrivateRepositoryDeployKey = file_get_contents(app_path('Livewire/Project/New/GithubPrivateRepositoryDeployKey.php'));
    $publicGitRepository = file_get_contents(app_path('Livewire/Project/New/PublicGitRepository.php'));
    $serviceIndex = file_get_contents(app_path('Livewire/Project/Service/Index.php'));
    $serviceStorage = file_get_contents(app_path('Livewire/Project/Service/Storage.php'));
    $executeContainerCommand = file_get_contents(app_path('Livewire/Project/Shared/ExecuteContainerCommand.php'));
    $danger = file_get_contents(app_path('Livewire/Project/Shared/Danger.php'));
    $scheduledTaskAdd = file_get_contents(app_path('Livewire/Project/Shared/ScheduledTask/Add.php'));
    $terminal = file_get_contents(app_path('Livewire/Project/Shared/Terminal.php'));
    $fileStorage = file_get_contents(app_path('Livewire/Project/Service/FileStorage.php'));

    expect($cloneMe)
        ->toContain("__('Project with the same name already exists.')")
        ->toContain("__('Environment with the same name already exists.')")
        ->and($dockerCompose)
        ->toContain("__('Destination not found.')")
        ->and($dockerImage)
        ->toContain("__('Provide either a tag or SHA256 digest, not both.')")
        ->toContain("__('Destination not found.')")
        ->and($simpleDockerfile)
        ->toContain("__('Destination not found.')")
        ->and($githubPrivateRepository)
        ->toContain("__('Invalid repository data: :message'")
        ->toContain("__('Destination not found.')")
        ->and($githubPrivateRepositoryDeployKey)
        ->toContain("__('Invalid repository URL: :message'")
        ->toContain("__('Destination not found.')")
        ->and($publicGitRepository)
        ->toContain("__('Invalid repository URL: :message'")
        ->toContain("__('Invalid branch: :message'")
        ->and($serviceIndex)
        ->toContain("__('An application with this name already exists.')")
        ->toContain("__('A database with this name already exists.')")
        ->and($serviceStorage)
        ->toContain("__('No valid resource type for file mount storage type!')")
        ->and($executeContainerCommand)
        ->toContain("__('Server is disabled.')")
        ->toContain("__('Invalid container name format')")
        ->toContain("__('Server ownership verification failed.')")
        ->and($danger)
        ->toContain("__('Unknown Resource')")
        ->toContain("__('Service Application')")
        ->toContain("__('Service Database')")
        ->and($scheduledTaskAdd)
        ->toContain("__('Invalid resource type.')")
        ->and($terminal)
        ->toContain("__('Terminal access is disabled on this server.')")
        ->toContain("__('Invalid container identifier format')")
        ->and($fileStorage)
        ->toContain("__('The selected directory and all its contents will be permanently deleted from the server.')")
        ->toContain("__('The selected file will be permanently deleted from the server.')");
});

it('resolves project runtime error translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Project with the same name already exists.'))->toBe('已存在同名项目。')
        ->and(__('Environment with the same name already exists.'))->toBe('已存在同名环境。')
        ->and(__('Provide either a tag or SHA256 digest, not both.'))->toBe('请填写标签或 SHA256 摘要其中之一，不要同时填写。')
        ->and(__('Destination not found.'))->toBe('未找到目标环境。')
        ->and(__('Invalid repository URL: :message', ['message' => 'bad url']))->toBe('仓库 URL 无效：bad url')
        ->and(__('Invalid branch: :message', ['message' => 'bad branch']))->toBe('分支无效：bad branch')
        ->and(__('Invalid repository data: :message', ['message' => 'bad repo']))->toBe('仓库数据无效：bad repo')
        ->and(__('An application with this name already exists.'))->toBe('已存在同名应用。')
        ->and(__('A database with this name already exists.'))->toBe('已存在同名数据库。')
        ->and(__('No valid resource type for file mount storage type!'))->toBe('文件挂载存储类型没有有效的资源类型！')
        ->and(__('Server is disabled.'))->toBe('服务器已禁用。')
        ->and(__('Invalid container name format'))->toBe('容器名称格式无效')
        ->and(__('Container not found.'))->toBe('未找到容器。')
        ->and(__('Invalid server configuration.'))->toBe('服务器配置无效。')
        ->and(__('Invalid resource type.'))->toBe('资源类型无效。')
        ->and(__('Server ownership verification failed.'))->toBe('服务器归属校验失败。')
        ->and(__('Unknown Resource'))->toBe('未知资源')
        ->and(__('Service Application'))->toBe('服务应用')
        ->and(__('Service Database'))->toBe('服务数据库')
        ->and(__('Terminal access is disabled on this server.'))->toBe('此服务器已禁用终端访问。')
        ->and(__('Invalid container identifier format'))->toBe('容器标识符格式无效')
        ->and(__('The selected directory and all its contents will be permanently deleted from the server.'))->toBe('所选目录及其全部内容都会从服务器中永久删除。')
        ->and(__('The selected file will be permanently deleted from the server.'))->toBe('所选文件会从服务器中永久删除。');
});

it('resolves targeted project helper translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('0 means use all CPUs. Floating point number, like 0.002 or 1.5. More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/engine/reference/run/#cpu-share-constraint\'>here</a>.'))
        ->toBe('0 表示使用全部 CPU。支持小数，例如 0.002 或 1.5。更多信息请查看<a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/engine/reference/run/#cpu-share-constraint\'>这里</a>。')
        ->and(__('If the health check fails, your application will become inaccessible. Please review the <a href=\'https://coolify.io/docs/knowledge-base/health-checks\' target=\'_blank\' class=\'underline text-white\'>Health Checks</a> guide before proceeding!'))
        ->toBe('如果健康检查失败，你的应用将无法访问。继续之前，请先阅读<a href=\'https://coolify.io/docs/knowledge-base/health-checks\' target=\'_blank\' class=\'underline text-white\'>健康检查</a>指南！')
        ->and(__('For more details goto our <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\' target=\'_blank\'>docs</a>.'))
        ->toBe('更多详情请查看我们的<a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\' target=\'_blank\'>文档</a>。')
        ->and(__('See details in our <a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\'>documentation</a>.'))
        ->toBe('详情请查看我们的<a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\'>文档</a>。')
        ->and(__('You can specify one domain with path or more with comma. You can specify a port to bind the domain to.<br><br><span class=\'text-helper\'>Example</span><br>- https://app.coolify.io,https://cloud.coolify.io/dashboard<br>- https://app.coolify.io/api/v3<br>- https://app.coolify.io:3000 -> app.coolify.io will point to port 3000 inside the container.<br>- https://app.coolify.io:8080/api -> app.coolify.io/api will point to port 8080 inside the container.'))
        ->toBe('你可以填写一个带路径的域名，或用逗号分隔多个域名。也可以指定端口，将域名绑定到对应端口。<br><br><span class=\'text-helper\'>示例</span><br>- https://app.coolify.io,https://cloud.coolify.io/dashboard<br>- https://app.coolify.io/api/v3<br>- https://app.coolify.io:3000 -> app.coolify.io 会指向容器内的 3000 端口。<br>- https://app.coolify.io:8080/api -> app.coolify.io/api 会指向容器内的 8080 端口。')
        ->and(__('Variable name: :name', ['name' => 'APP_KEY']))->toBe('变量名：APP_KEY')
        ->and(__('If you change the values in the database, please sync it here, otherwise automations won\'t work.'))
        ->toBe('如果你在数据库中修改了这些值，请在这里同步，否则自动化功能将无法工作。')
        ->and(__('If you change this in the database, please sync it here, otherwise automations (like backups) won\'t work.'))
        ->toBe('如果你在数据库中修改了这个值，请在这里同步，否则自动化功能（如备份）将无法工作。')
        ->and(__('You only need to provide the Redis directives you want to override — Redis will use default values for everything else.<br><br><strong>Important:</strong> Coolify automatically applies the requirepass directive using the password shown in the Password field above. If you override requirepass in your custom configuration, make sure it matches the password field to avoid authentication issues.<br><br><strong>Tip:</strong> <a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://raw.githubusercontent.com/redis/redis/7.2/redis.conf\'>View the full Redis default configuration</a> to see what options are available.'))
        ->toBe('你只需要填写想覆盖的 Redis 指令，其余配置会继续使用 Redis 默认值。<br><br><strong>重要：</strong>Coolify 会自动使用上方密码字段中的值应用 `requirepass` 指令。如果你在自定义配置里覆盖了 `requirepass`，请确保它与密码字段一致，以免出现认证问题。<br><br><strong>提示：</strong>可以查看<a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://raw.githubusercontent.com/redis/redis/7.2/redis.conf\'>完整的 Redis 默认配置</a>，了解可用选项。');
});
