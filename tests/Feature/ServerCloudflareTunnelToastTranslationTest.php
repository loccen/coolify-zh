<?php

use Illuminate\Support\Facades\App;

it('uses server toast translation keys for cloudflare tunnel dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/CloudflareTunnel.php'));

    expect($component)
        ->toContain("__('server.toasts.cloudflare_tunnel_disabled_ip_restored')")
        ->toContain("__('server.toasts.cloudflare_tunnel_disabled_update_ip_required')")
        ->toContain("__('server.toasts.cloudflare_tunnel_enabled')");
});

it('resolves cloudflare tunnel toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.cloudflare_tunnel_disabled_ip_restored'))
        ->toBe('已禁用 Cloudflare Tunnel。<br><br>已手动将服务器 IP 地址更新回之前的 IP 地址。')
        ->and(__('server.toasts.cloudflare_tunnel_disabled_update_ip_required'))
        ->toBe('已禁用 Cloudflare Tunnel。需要操作：请在高级设置中将服务器 IP 地址更新为真实 IP 地址。')
        ->and(__('server.toasts.cloudflare_tunnel_enabled'))
        ->toBe('Cloudflare Tunnel 已启用。');
});
