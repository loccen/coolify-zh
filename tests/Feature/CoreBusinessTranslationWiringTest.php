<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative core business views', function () {
    $viewsRoot = __DIR__.'/../../resources/views/livewire';

    $serverIndex = file_get_contents($viewsRoot.'/server/index.blade.php');
    $applicationHeading = file_get_contents($viewsRoot.'/project/application/heading.blade.php');
    $applicationConfiguration = file_get_contents($viewsRoot.'/project/application/configuration.blade.php');
    $applicationGeneral = file_get_contents($viewsRoot.'/project/application/general.blade.php');
    $applicationSource = file_get_contents($viewsRoot.'/project/application/source.blade.php');
    $applicationDestination = file_get_contents($viewsRoot.'/project/application/destination.blade.php');
    $applicationPreviews = file_get_contents($viewsRoot.'/project/application/previews.blade.php');
    $applicationPreviewForm = file_get_contents($viewsRoot.'/project/application/preview/form.blade.php');
    $applicationRollback = file_get_contents($viewsRoot.'/project/application/rollback.blade.php');
    $applicationDeploymentIndex = file_get_contents($viewsRoot.'/project/application/deployment/index.blade.php');
    $applicationDeploymentNavbar = file_get_contents($viewsRoot.'/project/application/deployment-navbar.blade.php');
    $serviceHeading = file_get_contents($viewsRoot.'/project/service/heading.blade.php');
    $serviceConfiguration = file_get_contents($viewsRoot.'/project/service/configuration.blade.php');
    $databaseHeading = file_get_contents($viewsRoot.'/project/database/heading.blade.php');
    $databaseConfiguration = file_get_contents($viewsRoot.'/project/database/configuration.blade.php');
    $tagsShow = file_get_contents($viewsRoot.'/tags/show.blade.php');
    $resourceSelect = file_get_contents(app_path('Livewire/Project/New/Select.php'));
    $globalSearch = file_get_contents($viewsRoot.'/global-search.blade.php');
    $boarding = file_get_contents($viewsRoot.'/boarding/index.blade.php');

    expect($serverIndex)
        ->toContain("{{ __('Servers') }}")
        ->and($applicationHeading)
        ->toContain("{{ __('Deployments') }}")
        ->and($applicationConfiguration)
        ->toContain("{{ __('General') }}")
        ->toContain("{{ __('Preview Deployments') }}")
        ->toContain("{{ __('Resource Limits') }}")
        ->and($applicationGeneral)
        ->toContain("{{ __('Docker Registry') }}")
        ->toContain("{{ __('Build') }}")
        ->toContain("{{ __('HTTP Basic Authentication') }}")
        ->toContain("{{ __('Pre/Post Deployment Commands') }}")
        ->toContain("{{ __('will detect the required configuration automatically.') }}")
        ->and($applicationSource)
        ->toContain("{{ __('Open Repository') }}")
        ->toContain("__('Change git source to :name'")
        ->toContain("__('Confirmation Text')")
        ->and($applicationDestination)
        ->toContain("{{ __('Destination') }}")
        ->toContain("__('Destination Network: :name'")
        ->and($applicationPreviews)
        ->toContain("{{ __('Pull Requests on Git') }}")
        ->toContain("__('Force deploy (without cache)')")
        ->toContain("__('Preview Deployment Name')")
        ->toContain("__('The preview deployment domain is already in use by other resources. Using the same domain for multiple resources can cause routing conflicts and unpredictable behavior.')")
        ->and($applicationPreviewForm)
        ->toContain("{{ __('Preview Deployments') }}")
        ->toContain("__('Preview URL Template')")
        ->toContain("__('Domain Preview: :preview'")
        ->and($applicationRollback)
        ->toContain("{{ __('Rollback') }}")
        ->toContain("__('This image is currently running.')")
        ->toContain("__('Loading available docker images...')")
        ->and($applicationDeploymentIndex)
        ->toContain("{{ __('Deployments') }}")
        ->toContain("__('Pull Request Id')")
        ->toContain("__('No deployments found')")
        ->and($applicationDeploymentNavbar)
        ->toContain("{{ __('Deployment Log') }}")
        ->toContain("{{ __('Force Start') }}")
        ->and($serviceHeading)
        ->toContain("{{ __('Pull Latest Images & Restart') }}")
        ->and($serviceConfiguration)
        ->toContain("{{ __('Documentation') }}")
        ->toContain("{{ __('Scheduled Tasks') }}")
        ->toContain("{{ __('Danger Zone') }}")
        ->toContain("__('Confirm Service Application Restart?')")
        ->toContain("__('Restart Service Container')")
        ->toContain("__('Restart Database')")
        ->and($databaseHeading)
        ->toContain("{{ __('Confirm Database Restart?') }}")
        ->and($databaseConfiguration)
        ->toContain("{{ __('Import Backup') }}")
        ->toContain("{{ __('Metrics') }}")
        ->toContain("{{ __('Tags') }}")
        ->and($tagsShow)
        ->toContain("{{ __('Tags') }} | Coolify")
        ->toContain("{{ __('Deployments') }}")
        ->and($resourceSelect)
        ->toContain("__('You can deploy a simple Dockerfile, without Git.')")
        ->toContain("__('You can deploy an existing Docker Image from any Registry, without Git.')")
        ->toContain("__('PostgreSQL is an object-relational database known for its robustness, advanced features, and strong standards compliance.')")
        ->and($globalSearch)
        ->toContain("{{ __('Search resources, paths, everything (type new for create)...') }}")
        ->and($boarding)
        ->toContain("{{ __('Welcome to Coolify') }}")
        ->toContain("title=\"{{ __('Project Setup') }}\"")
        ->toContain("{{ __('Connect your first server and start deploying in minutes') }}")
        ->toContain("{{ __('Let\\'s go!') }}");
});

it('resolves representative T5C translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Servers'))->toBe('服务器')
        ->and(__('Projects'))->toBe('项目')
        ->and(__('Deployments'))->toBe('部署')
        ->and(__('Terminal'))->toBe('终端')
        ->and(__('Please load a Compose file.'))->toBe('请加载 Compose 文件。')
        ->and(__('Docker Compose'))->toBe('Docker Compose')
        ->and(__('Preview Deployments'))->toBe('Preview Deployments')
        ->and(__('Open Repository'))->toBe('打开仓库')
        ->and(__('Change git source to :name', ['name' => '示例来源']))->toBe('将代码源切换为 示例来源')
        ->and(__('Destination Network: :name', ['name' => 'coolify']))->toBe('目标网络：coolify')
        ->and(__('Force deploy (without cache)'))->toBe('强制部署（不使用缓存）')
        ->and(__('Preview Deployment Name'))->toBe('Preview Deployment 名称')
        ->and(__('Preview Deployments based on pull requests are here.'))->toBe('这里列出了基于拉取请求创建的 Preview Deployments。')
        ->and(__('Domain Preview: :preview', ['preview' => 'preview.example.com']))->toBe('预览域名：preview.example.com')
        ->and(__('Rollback'))->toBe('回滚')
        ->and(__('This image is currently running.'))->toBe('这个镜像当前正在运行。')
        ->and(__('Deployment Log'))->toBe('部署日志')
        ->and(__('Force Start'))->toBe('强制启动')
        ->and(__('will detect the required configuration automatically.'))->toBe('会自动检测所需配置。')
        ->and(__('Pull Request #:id', ['id' => 42]))->toBe('拉取请求 # 42')
        ->and(__('Queued'))->toBe('排队中')
        ->and(__('Cancelled'))->toBe('已取消')
        ->and(__('Running for:'))->toBe('已运行：')
        ->and(__('Commit:'))->toBe('提交：')
        ->and(__('Commit SHA'))->toBe('提交 SHA')
        ->and(__('Container name'))->toBe('容器名称')
        ->and(__('Number of CPUs'))->toBe('CPU 数量')
        ->and(__('Limit CPUs'))->toBe('限制 CPU 数量')
        ->and(__('CPU Weight'))->toBe('CPU 权重')
        ->and(__('Image'))->toBe('镜像')
        ->and(__('Make it publicly available'))->toBe('公开访问')
        ->and(__('Swappiness'))->toBe('交换倾向')
        ->and(__('Maximum Swap Limit'))->toBe('最大交换内存限制')
        ->and(__('Postgres URL (public)'))->toBe('Postgres 连接串（公网）')
        ->and(__('Manual'))->toBe('手动')
        ->and(__('Proxy'))->toBe('代理')
        ->and(__('Swarm'))->toBe('Swarm')
        ->and(__('Webhook'))->toBe('Webhook')
        ->and(__('Import Backup'))->toBe('导入备份')
        ->and(__('Documentation'))->toBe('文档')
        ->and(__('Persistent Storage'))->toBe('持久存储')
        ->and(__('Danger Zone'))->toBe('危险区')
        ->and(__('The selected service application will be unavailable during the restart.'))->toBe('选中的服务应用在重启期间将不可用。')
        ->and(__('If the service application is currently in use data could be lost.'))->toBe('如果当前正在使用这个服务应用，数据可能会丢失。')
        ->and(__('Restart Service Container'))->toBe('重启服务容器')
        ->and(__('This service database will be unavailable during the restart.'))->toBe('这个服务数据库在重启期间将不可用。')
        ->and(__('If the service database is currently in use data could be lost.'))->toBe('如果当前正在使用这个服务数据库，数据可能会丢失。')
        ->and(__('Restart Database'))->toBe('重启数据库')
        ->and(__('Docker Registry'))->toBe('Docker 注册表')
        ->and(__('Build'))->toBe('构建')
        ->and(__('HTTP Basic Authentication'))->toBe('HTTP 基本身份验证')
        ->and(__('Pre/Post Deployment Commands'))->toBe('部署前/后命令')
        ->and(__('You can deploy an existing Docker Image from any Registry, without Git.'))->toBe('你可以从任意镜像仓库直接部署现有的 Docker 镜像，无需 Git。')
        ->and(__('PostgreSQL is an object-relational database known for its robustness, advanced features, and strong standards compliance.'))->toBe('PostgreSQL 是一个对象关系型数据库，以稳健性、高级特性和严格的标准兼容性著称。')
        ->and(__('MySQL is an open-source relational database management system.'))->toBe('MySQL 是一个开源关系型数据库管理系统。')
        ->and(__('MariaDB is a community-developed, commercially supported fork of the MySQL relational database management system, intended to remain free and open-source.'))->toBe('MariaDB 是 MySQL 关系型数据库管理系统的社区分支，由商业力量提供支持，并坚持保持自由开源。')
        ->and(__('Welcome to Coolify'))->toBe('欢迎使用 Coolify')
        ->and(__('Connect your first server and start deploying in minutes'))->toBe('连接你的第一台服务器，几分钟内就能开始部署。')
        ->and(__('What You\'ll Set Up'))->toBe('你将要完成的设置')
        ->and(__('Let\'s go!'))->toBe('开始吧！')
        ->and(__('Search resources, paths, everything (type new for create)...'))->toBe('搜索资源、路径等内容（输入 new 可创建）...')
        ->and(__('No resource found with the search term'))->toBe('没有找到与搜索词匹配的资源');
});
