<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative core business views', function () {
    $viewsRoot = __DIR__.'/../../resources/views/livewire';

    $serverIndex = file_get_contents($viewsRoot.'/server/index.blade.php');
    $applicationHeading = file_get_contents($viewsRoot.'/project/application/heading.blade.php');
    $applicationConfiguration = file_get_contents($viewsRoot.'/project/application/configuration.blade.php');
    $applicationGeneral = file_get_contents($viewsRoot.'/project/application/general.blade.php');
    $serviceHeading = file_get_contents($viewsRoot.'/project/service/heading.blade.php');
    $serviceConfiguration = file_get_contents($viewsRoot.'/project/service/configuration.blade.php');
    $databaseHeading = file_get_contents($viewsRoot.'/project/database/heading.blade.php');
    $databaseConfiguration = file_get_contents($viewsRoot.'/project/database/configuration.blade.php');
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
        ->and($serviceHeading)
        ->toContain("{{ __('Pull Latest Images & Restart') }}")
        ->and($serviceConfiguration)
        ->toContain("{{ __('Documentation') }}")
        ->toContain("{{ __('Scheduled Tasks') }}")
        ->toContain("{{ __('Danger Zone') }}")
        ->and($databaseHeading)
        ->toContain("{{ __('Confirm Database Restart?') }}")
        ->and($databaseConfiguration)
        ->toContain("{{ __('Import Backup') }}")
        ->toContain("{{ __('Metrics') }}")
        ->toContain("{{ __('Tags') }}")
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
        ->and(__('Proxy'))->toBe('代理')
        ->and(__('Swarm'))->toBe('Swarm')
        ->and(__('Webhook'))->toBe('Webhook')
        ->and(__('Import Backup'))->toBe('导入备份')
        ->and(__('Documentation'))->toBe('文档')
        ->and(__('Persistent Storage'))->toBe('持久存储')
        ->and(__('Danger Zone'))->toBe('危险区')
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
