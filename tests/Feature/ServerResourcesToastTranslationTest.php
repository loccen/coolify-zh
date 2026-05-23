<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server resources toast dispatches', function () {
    $resources = file_get_contents(app_path('Livewire/Server/Resources.php'));

    expect($resources)
        ->toContain("__('server.toasts.invalid_container_identifier')")
        ->toContain("__('server.toasts.container_started')")
        ->toContain("__('server.toasts.container_restarted')")
        ->toContain("__('server.toasts.container_stopped')")
        ->toContain("__('server.toasts.resource_statuses_refreshed')");
});

it('resolves server resource toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.invalid_container_identifier'))
        ->toBe('容器标识符无效。')
        ->and(__('server.toasts.container_started'))
        ->toBe('容器已启动。')
        ->and(__('server.toasts.container_restarted'))
        ->toBe('容器已重启。')
        ->and(__('server.toasts.container_stopped'))
        ->toBe('容器已停止。')
        ->and(__('server.toasts.resource_statuses_refreshed'))
        ->toBe('资源状态已刷新。');
});
