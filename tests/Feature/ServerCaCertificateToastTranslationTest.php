<?php

use Illuminate\Support\Facades\App;

it('uses server translation lookups for ca certificate toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/CaCertificate/Show.php'));

    expect($component)
        ->toContain("__('server.toasts.ca_certificate_saved_successfully')")
        ->toContain("__('server.toasts.ca_certificate_regenerated_successfully')");
});

it('resolves ca certificate toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.ca_certificate_saved_successfully'))
        ->toBe('CA Certificate saved successfully.')
        ->and(__('server.toasts.ca_certificate_regenerated_successfully'))
        ->toBe('CA Certificate regenerated successfully.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.ca_certificate_saved_successfully'))
        ->toBe('CA 证书已保存。')
        ->and(__('server.toasts.ca_certificate_regenerated_successfully'))
        ->toBe('CA 证书已重新生成。');
});
