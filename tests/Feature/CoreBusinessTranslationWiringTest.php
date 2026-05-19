<?php

use Illuminate\Support\Facades\App;
use Tests\TestCase;

uses(TestCase::class);

it('uses explicit translation lookups in representative core business views', function () {
    $viewsRoot = __DIR__.'/../../resources/views/livewire';

    $serverIndex = file_get_contents($viewsRoot.'/server/index.blade.php');
    $applicationHeading = file_get_contents($viewsRoot.'/project/application/heading.blade.php');
    $serviceHeading = file_get_contents($viewsRoot.'/project/service/heading.blade.php');
    $databaseHeading = file_get_contents($viewsRoot.'/project/database/heading.blade.php');
    $globalSearch = file_get_contents($viewsRoot.'/global-search.blade.php');
    $boarding = file_get_contents($viewsRoot.'/boarding/index.blade.php');

    expect($serverIndex)
        ->toContain("{{ __('Servers') }}")
        ->and($applicationHeading)
        ->toContain("{{ __('Deployments') }}")
        ->and($serviceHeading)
        ->toContain("{{ __('Pull Latest Images & Restart') }}")
        ->and($databaseHeading)
        ->toContain("{{ __('Confirm Database Restart?') }}")
        ->and($globalSearch)
        ->toContain("{{ __('Search resources, paths, everything (type new for create)...') }}")
        ->and($boarding)
        ->toContain("{{ __('Welcome to Coolify') }}")
        ->toContain("title=\"{{ __('Project Setup') }}\"");
});

it('resolves representative T5C translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Servers'))->toBe('服务器')
        ->and(__('Projects'))->toBe('项目')
        ->and(__('Deployments'))->toBe('部署')
        ->and(__('Terminal'))->toBe('终端')
        ->and(__('Please load a Compose file.'))->toBe('请加载 Compose 文件。');
});
