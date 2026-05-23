<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server destinations toast dispatches', function () {
    $destinations = file_get_contents(app_path('Livewire/Server/Destinations.php'));

    expect($destinations)
        ->toContain("__('server.toasts.network_already_added')")
        ->toContain("__('server.toasts.no_new_destinations_found')")
        ->toContain("__('server.toasts.scan_done')");
});

it('resolves server destinations toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.network_already_added'))
        ->toBe('网络已添加到此服务器。')
        ->and(__('server.toasts.no_new_destinations_found'))
        ->toBe('此服务器上未找到新的目标位置。')
        ->and(__('server.toasts.scan_done'))
        ->toBe('扫描完成。');
});
