<?php

use Illuminate\Support\Facades\App;

it('uses server translation lookups for validate and install toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/ValidateAndInstall.php'));

    expect($component)
        ->toContain("__('server.toasts.docker_swarm_initiated')")
        ->toContain("__('server.toasts.server_validated_proxy_starting')");
});

it('resolves validate and install toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.docker_swarm_initiated'))
        ->toBe('Docker Swarm is initiated.')
        ->and(__('server.toasts.server_validated_proxy_starting'))
        ->toBe('Server validated, proxy is starting in a moment.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.docker_swarm_initiated'))
        ->toBe('Docker Swarm 已初始化。')
        ->and(__('server.toasts.server_validated_proxy_starting'))
        ->toBe('服务器已验证，代理即将启动。');
});
