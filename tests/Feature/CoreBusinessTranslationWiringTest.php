<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative core business views', function () {
    $viewsRoot = __DIR__.'/../../resources/views/livewire';

    $serverIndex = file_get_contents($viewsRoot.'/server/index.blade.php');
    $applicationHeading = file_get_contents($viewsRoot.'/project/application/heading.blade.php');
    $applicationConfiguration = file_get_contents($viewsRoot.'/project/application/configuration.blade.php');
    $serviceHeading = file_get_contents($viewsRoot.'/project/service/heading.blade.php');
    $serviceConfiguration = file_get_contents($viewsRoot.'/project/service/configuration.blade.php');
    $databaseHeading = file_get_contents($viewsRoot.'/project/database/heading.blade.php');
    $databaseConfiguration = file_get_contents($viewsRoot.'/project/database/configuration.blade.php');
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
        ->and(__('Welcome to Coolify'))->toBe('欢迎使用 Coolify')
        ->and(__('Connect your first server and start deploying in minutes'))->toBe('连接你的第一台服务器，几分钟内就能开始部署。')
        ->and(__('What You\'ll Set Up'))->toBe('你将要完成的设置')
        ->and(__('Let\'s go!'))->toBe('开始吧！')
        ->and(__('Search resources, paths, everything (type new for create)...'))->toBe('搜索资源、路径等内容（输入 new 可创建）...')
        ->and(__('No resource found with the search term'))->toBe('没有找到与搜索词匹配的资源');
});
