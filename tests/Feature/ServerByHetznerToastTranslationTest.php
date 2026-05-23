<?php

use Illuminate\Support\Facades\App;

it('uses server toast translation keys for hetzner server creation errors', function () {
    $component = file_get_contents(app_path('Livewire/Server/New/ByHetzner.php'));

    expect($component)
        ->toContain("__('server.toasts.select_valid_hetzner_token')")
        ->toContain("__('server.toasts.server_limit_reached')");
});

it('resolves hetzner server creation toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.select_valid_hetzner_token'))
        ->toBe('Please select a valid Hetzner token.')
        ->and(__('server.toasts.server_limit_reached'))
        ->toBe('You have reached the server limit for your subscription.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.select_valid_hetzner_token'))
        ->toBe('请选择一个有效的 Hetzner 令牌。')
        ->and(__('server.toasts.server_limit_reached'))
        ->toBe('你已达到当前订阅的服务器数量上限。');
});
