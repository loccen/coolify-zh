<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server advanced toast dispatches', function () {
    $advanced = file_get_contents(app_path('Livewire/Server/Advanced.php'));

    expect($advanced)
        ->toContain("__('server.toasts.server_updated')")
        ->toContain("__('server.toasts.invalid_disk_usage_check_frequency')");
});

it('resolves server advanced toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.server_updated'))
        ->toBe('服务器已更新。')
        ->and(__('server.toasts.invalid_disk_usage_check_frequency'))
        ->toBe('磁盘使用情况检查频率的 Cron / 人类可读表达式无效。');
});
