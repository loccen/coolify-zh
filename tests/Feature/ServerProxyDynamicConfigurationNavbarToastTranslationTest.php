<?php

use Illuminate\Support\Facades\App;

it('uses server toast translation keys for proxy dynamic configuration delete messages', function () {
    $component = file_get_contents(app_path('Livewire/Server/Proxy/DynamicConfigurationNavbar.php'));
    $enTranslations = require base_path('lang/en/server.php');
    $zhTranslations = require base_path('lang/zh_CN/server.php');

    expect($component)
        ->toContain("__('server.toasts.proxy_dynamic_configuration_cannot_delete_caddyfile')")
        ->toContain("__('server.toasts.proxy_dynamic_configuration_file_deleted')");

    expect($enTranslations['toasts']['proxy_dynamic_configuration_cannot_delete_caddyfile'])->toBe('Cannot delete Caddyfile.')
        ->and($enTranslations['toasts']['proxy_dynamic_configuration_file_deleted'])->toBe('File deleted.');

    expect($zhTranslations['toasts']['proxy_dynamic_configuration_cannot_delete_caddyfile'])->toBe('无法删除 Caddyfile。')
        ->and($zhTranslations['toasts']['proxy_dynamic_configuration_file_deleted'])->toBe('文件已删除。');
});

it('resolves server proxy dynamic configuration delete messages in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.proxy_dynamic_configuration_cannot_delete_caddyfile'))->toBe('Cannot delete Caddyfile.')
        ->and(__('server.toasts.proxy_dynamic_configuration_file_deleted'))->toBe('File deleted.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.proxy_dynamic_configuration_cannot_delete_caddyfile'))->toBe('无法删除 Caddyfile。')
        ->and(__('server.toasts.proxy_dynamic_configuration_file_deleted'))->toBe('文件已删除。');
});
