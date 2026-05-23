<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in the server sentinel component', function () {
    $sentinel = file_get_contents(app_path('Livewire/Server/Sentinel.php'));

    expect($sentinel)
        ->toContain("__('server.toasts.sentinel_restarted')")
        ->toContain("__('server.toasts.sentinel_cannot_be_enabled_on_build_servers')")
        ->toContain("__('server.toasts.sentinel_token_regenerated')")
        ->toContain("__('server.toasts.server_settings_updated')");
});

it('resolves sentinel toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.sentinel_restarted'))
        ->toBe('Sentinel 已成功重启。')
        ->and(__('server.toasts.sentinel_cannot_be_enabled_on_build_servers'))
        ->toBe('构建服务器无法启用 Sentinel。')
        ->and(__('server.toasts.sentinel_token_regenerated'))
        ->toBe('令牌已重新生成。正在重启 Sentinel。')
        ->and(__('server.toasts.server_settings_updated'))
        ->toBe('服务器设置已更新。');
});
