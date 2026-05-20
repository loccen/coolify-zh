<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative server views', function () {
    $viewsRoot = base_path('resources/views');

    $sidebar = file_get_contents($viewsRoot.'/components/server/sidebar.blade.php');
    $sidebarProxy = file_get_contents($viewsRoot.'/components/server/sidebar-proxy.blade.php');
    $sidebarSecurity = file_get_contents($viewsRoot.'/components/server/sidebar-security.blade.php');
    $navbar = file_get_contents($viewsRoot.'/livewire/server/navbar.blade.php');
    $show = file_get_contents($viewsRoot.'/livewire/server/show.blade.php');
    $advanced = file_get_contents($viewsRoot.'/livewire/server/advanced.blade.php');
    $destinations = file_get_contents($viewsRoot.'/livewire/server/destinations.blade.php');
    $charts = file_get_contents($viewsRoot.'/livewire/server/charts.blade.php');
    $logDrains = file_get_contents($viewsRoot.'/livewire/server/log-drains.blade.php');
    $terminalAccess = file_get_contents($viewsRoot.'/livewire/server/security/terminal-access.blade.php');
    $privateKey = file_get_contents($viewsRoot.'/livewire/server/private-key/show.blade.php');
    $cloudProviderToken = file_get_contents($viewsRoot.'/livewire/server/cloud-provider-token/show.blade.php');
    $cloudflareTunnel = file_get_contents($viewsRoot.'/livewire/server/cloudflare-tunnel.blade.php');
    $securityPatches = file_get_contents($viewsRoot.'/livewire/server/security/patches.blade.php');
    $resources = file_get_contents($viewsRoot.'/livewire/server/resources.blade.php');
    $validateAndInstall = file_get_contents($viewsRoot.'/livewire/server/validate-and-install.blade.php');
    $serverDelete = file_get_contents($viewsRoot.'/livewire/server/delete.blade.php');
    $serverCreate = file_get_contents($viewsRoot.'/livewire/server/create.blade.php');

    expect($sidebar)
        ->toContain("{{ __('General') }}")
        ->toContain("{{ __('Log Drains') }}")
        ->toContain("{{ __('Metrics') }}")
        ->and($sidebarProxy)
        ->toContain("{{ __('Configuration') }}")
        ->toContain("{{ __('Dynamic Configurations') }}")
        ->and($sidebarSecurity)
        ->toContain("{{ __('Server Patching') }}")
        ->toContain("{{ __('Terminal Access') }}")
        ->and($navbar)
        ->toContain("{{ __('Server') }}")
        ->toContain("__('Proxy Running')")
        ->toContain("__('Restart Proxy')")
        ->and($show)
        ->toContain("{{ __('General') }}")
        ->toContain("__('Revalidate server')")
        ->toContain("__('Server Details')")
        ->toContain("__('Server Timezone')")
        ->and($advanced)
        ->toContain("{{ __('Advanced') }}")
        ->toContain("__('Disk Usage')")
        ->and($destinations)
        ->toContain("{{ __('Destinations') }}")
        ->toContain("__('Found Destinations')")
        ->and($charts)
        ->toContain("{{ __('Metrics') }}")
        ->toContain("__('CPU Usage')")
        ->and($logDrains)
        ->toContain("{{ __('Log Drains') }}")
        ->toContain("__('Sends service logs to 3rd party tools.')")
        ->and($terminalAccess)
        ->toContain("{{ __('Terminal Access') }}")
        ->toContain("__('Operational')")
        ->and($privateKey)
        ->toContain("{{ __('Private Key') }}")
        ->toContain("__('Check connection')")
        ->and($cloudProviderToken)
        ->toContain("{{ __('Hetzner Token') }}")
        ->toContain("__('Validate token')")
        ->and($cloudflareTunnel)
        ->toContain("{{ __('Cloudflare Tunnel') }}")
        ->toContain("__('Cloudflare Tunnel Configuration')")
        ->and($securityPatches)
        ->toContain("{{ __('Server Patching') }}")
        ->toContain("__('Check for Updates')")
        ->and($resources)
        ->toContain("{{ __('Resources') }}")
        ->toContain("__('Managed')")
        ->and($validateAndInstall)
        ->toContain("__('Server is reachable:')")
        ->toContain("__('Installation Logs')")
        ->and($serverDelete)
        ->toContain("{{ __('Danger Zone') }}")
        ->toContain("__('Confirm Server Deletion?')")
        ->and($serverCreate)
        ->toContain("{{ __('Connect a Hetzner Server') }}")
        ->toContain("{{ __('Add Server by IP Address') }}");
});

it('resolves representative server translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('General'))->toBe('常规')
        ->and(__('Advanced'))->toBe('高级')
        ->and(__('Log Drains'))->toBe('日志流')
        ->and(__('Name'))->toBe('名称')
        ->and(__('Port'))->toBe('端口')
        ->and(__('Save'))->toBe('保存')
        ->and(__('Status'))->toBe('状态')
        ->and(__('Disabled'))->toBe('已禁用')
        ->and(__('Terminal Access'))->toBe('终端访问')
        ->and(__('Hetzner Token'))->toBe('Hetzner 令牌')
        ->and(__('5 minutes (live)'))->toBe('5 分钟（实时）')
        ->and(__('Proxy Running'))->toBe('代理运行中')
        ->and(__('Revalidate server'))->toBe('重新验证服务器')
        ->and(__('Server is reachable and validated.'))->toBe('服务器可达，且已完成验证。')
        ->and(__('Check connection'))->toBe('检查连接')
        ->and(__('Use it as a build server?'))->toBe('将它用作构建服务器？')
        ->and(__('Cloudflare Tunnel'))->toBe('Cloudflare 隧道')
        ->and(__('Check for Updates'))->toBe('检查更新')
        ->and(__('Managed'))->toBe('受管')
        ->and(__('Server Resources'))->toBe('服务器资源')
        ->and(__('Retry Validation'))->toBe('重试验证');
});
