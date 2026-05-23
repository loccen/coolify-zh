<?php

use Illuminate\Support\Facades\App;

it('uses the server translation key for invalid docker cleanup frequency', function () {
    $component = file_get_contents(app_path('Livewire/Server/DockerCleanup.php'));

    expect($component)
        ->toContain("__('server.toasts.invalid_docker_cleanup_frequency')");
});

it('resolves the docker cleanup frequency translation in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.invalid_docker_cleanup_frequency'))
        ->toBe('Invalid Cron / Human expression for Docker Cleanup Frequency.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.invalid_docker_cleanup_frequency'))
        ->toBe('Docker 清理频率的 Cron / 人类可读表达式无效。');
});
