<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server proxy dynamic configuration toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/Proxy/NewDynamicConfiguration.php'));

    expect($component)
        ->toContain("__('server.toasts.proxy_dynamic_configuration_file_name_reserved')")
        ->toContain("__('server.toasts.proxy_dynamic_configuration_file_already_exists')")
        ->toContain("__('server.toasts.proxy_dynamic_configuration_saved')");
});

it('resolves server proxy dynamic configuration toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.proxy_dynamic_configuration_file_name_reserved'))
        ->toBe('File name is reserved.')
        ->and(__('server.toasts.proxy_dynamic_configuration_file_already_exists'))
        ->toBe('File already exists')
        ->and(__('server.toasts.proxy_dynamic_configuration_saved'))
        ->toBe('Dynamic configuration saved.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.proxy_dynamic_configuration_file_name_reserved'))
        ->toBe('文件名已被保留。')
        ->and(__('server.toasts.proxy_dynamic_configuration_file_already_exists'))
        ->toBe('文件已存在。')
        ->and(__('server.toasts.proxy_dynamic_configuration_saved'))
        ->toBe('动态配置已保存。');
});
