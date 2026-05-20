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
    $backupEdit = file_get_contents($viewsRoot.'/livewire/project/database/backup-edit.blade.php');
    $backupNow = file_get_contents($viewsRoot.'/livewire/project/database/backup-now.blade.php');
    $dockerImage = file_get_contents($viewsRoot.'/livewire/project/new/docker-image.blade.php');
    $simpleDockerfile = file_get_contents($viewsRoot.'/livewire/project/new/simple-dockerfile.blade.php');

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
        ->toContain("{{ __('Dockerfile') }}");
});

it('resolves representative follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Comment'))->toBe('备注')
        ->and(__('Image Name'))->toBe('镜像名称')
        ->and(__('Is Multiline?'))->toBe('多行值？')
        ->and(__('Commands'))->toBe('命令')
        ->and(__('Backup Now'))->toBe('立即备份')
        ->and(__('Proxy Dynamic Configuration'))->toBe('代理动态配置')
        ->and(__('File:'))->toBe('文件：')
        ->and(__('In sync'))->toBe('已同步')
        ->and(__('Out of sync'))->toBe('未同步')
        ->and(__('settings.instance_updated'))->toBe('设置已更新。')
        ->and(trans('settings.instance_updated'))->toBe('设置已更新。');
});
