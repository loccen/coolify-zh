<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server log drains toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/LogDrains.php'));

    expect($component)
        ->toContain("__('server.toasts.log_drain_service_started')")
        ->toContain("__('server.toasts.log_drain_service_stopped')")
        ->toContain("__('server.toasts.settings_saved')");
});

it('resolves server log drains toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.log_drain_service_started'))
        ->toBe('Log drain service started.')
        ->and(__('server.toasts.log_drain_service_stopped'))
        ->toBe('Log drain service stopped.')
        ->and(__('server.toasts.settings_saved'))
        ->toBe('Settings saved.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.log_drain_service_started'))
        ->toBe('日志收集服务已启动。')
        ->and(__('server.toasts.log_drain_service_stopped'))
        ->toBe('日志收集服务已停止。')
        ->and(__('server.toasts.settings_saved'))
        ->toBe('设置已保存。');
});
