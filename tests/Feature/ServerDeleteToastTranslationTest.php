<?php

use Illuminate\Support\Facades\App;

it('uses the server delete resources translation in the delete component', function () {
    $component = file_get_contents(app_path('Livewire/Server/Delete.php'));

    expect($component)
        ->toContain("__('server.toasts.delete_resources_first')");
});

it('resolves the server delete resources translation in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.delete_resources_first'))
        ->toBe('Server has defined resources. Please delete them first or select "Delete all resources".');

    App::setLocale('zh_CN');

    expect(__('server.toasts.delete_resources_first'))
        ->toBe('服务器已定义资源。请先删除这些资源，或选择“删除所有资源”。');
});
