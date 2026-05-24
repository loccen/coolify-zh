<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server proxy toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/Proxy.php'));

    expect($component)
        ->toContain("__('server.toasts.settings_saved')")
        ->toContain("__('server.toasts.proxy_configuration_saved')")
        ->toContain("__('server.toasts.proxy_configuration_reset_to_default')");
});

it('resolves server proxy toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.settings_saved'))
        ->toBe('Settings saved.')
        ->and(__('server.toasts.proxy_configuration_saved'))
        ->toBe('Proxy configuration saved.')
        ->and(__('server.toasts.proxy_configuration_reset_to_default'))
        ->toBe('Proxy configuration reset to default.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.settings_saved'))
        ->toBe('设置已保存。')
        ->and(__('server.toasts.proxy_configuration_saved'))
        ->toBe('代理配置已保存。')
        ->and(__('server.toasts.proxy_configuration_reset_to_default'))
        ->toBe('代理配置已重置为默认值。');
});
