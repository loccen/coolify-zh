<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server navbar toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/Navbar.php'));

    expect($component)
        ->toContain("__('server.toasts.proxy_running')")
        ->toContain("__('server.toasts.proxy_restart_failed')")
        ->not->toContain('Proxy is running.')
        ->not->toContain('Proxy restart failed. Check logs.');
});

it('resolves server navbar toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.proxy_running'))
        ->toBe('Proxy is running.')
        ->and(__('server.toasts.proxy_restart_failed'))
        ->toBe('Proxy restart failed. Check logs.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.proxy_running'))
        ->toBe('代理正在运行。')
        ->and(__('server.toasts.proxy_restart_failed'))
        ->toBe('代理重启失败。请查看日志。');
});
