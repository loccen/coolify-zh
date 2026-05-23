<?php

use Illuminate\Support\Facades\App;

it('uses server translation lookups in the docker cleanup component toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/DockerCleanup.php'));

    expect($component)
        ->toContain("__('server.toasts.server_updated')")
        ->toContain("__('server.toasts.manual_cleanup_job_started')");
});

it('resolves docker cleanup toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.server_updated'))
        ->toBe('Server updated.')
        ->and(__('server.toasts.manual_cleanup_job_started'))
        ->toBe('Manual cleanup job started. Depending on the amount of data, this might take a while.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.server_updated'))
        ->toBe('服务器已更新。')
        ->and(__('server.toasts.manual_cleanup_job_started'))
        ->toBe('手动清理任务已启动。根据数据量大小，这可能需要一段时间。');
});
