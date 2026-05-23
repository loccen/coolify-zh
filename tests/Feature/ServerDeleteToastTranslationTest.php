<?php

use Illuminate\Support\Facades\App;

it('uses the server delete resources translation in the delete component', function () {
    $component = file_get_contents(app_path('Livewire/Server/Delete.php'));

    expect($component)
        ->toContain("__('The provided password is incorrect.')")
        ->toContain("__('server.toasts.delete_resources_first')")
        ->toContain("__('server.delete.force_delete_resources_label', ['count' => \$resourceCount])")
        ->toContain("__('server.delete.force_delete_resources_warning')")
        ->toContain("__('server.delete.delete_from_hetzner_label')")
        ->toContain("__('server.delete.delete_from_hetzner_warning')");
});

it('resolves the server delete resources translation in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('The provided password is incorrect.'))
        ->toBe('The provided password is incorrect.')
        ->and(__('server.delete.force_delete_resources_label', ['count' => 3]))
        ->toBe('Delete all resources (3 total)')
        ->and(__('server.delete.force_delete_resources_warning'))
        ->toBe('Server cannot be deleted while it has resources.')
        ->and(__('server.delete.delete_from_hetzner_label'))
        ->toBe('Also delete server from Hetzner Cloud')
        ->and(__('server.delete.delete_from_hetzner_warning'))
        ->toBe('The actual server on Hetzner Cloud will NOT be deleted.');

    expect(__('server.toasts.delete_resources_first'))
        ->toBe('Server has defined resources. Please delete them first or select "Delete all resources".');

    App::setLocale('zh_CN');

    expect(__('The provided password is incorrect.'))
        ->toBe('输入的密码不正确。')
        ->and(__('server.delete.force_delete_resources_label', ['count' => 3]))
        ->toBe('删除所有资源（共 3 个）')
        ->and(__('server.delete.force_delete_resources_warning'))
        ->toBe('服务器在仍有资源时无法删除。')
        ->and(__('server.delete.delete_from_hetzner_label'))
        ->toBe('同时从 Hetzner Cloud 删除服务器')
        ->and(__('server.delete.delete_from_hetzner_warning'))
        ->toBe('Hetzner Cloud 上的实际服务器不会被删除。');

    expect(__('server.toasts.delete_resources_first'))
        ->toBe('服务器已定义资源。请先删除这些资源，或选择“删除所有资源”。');
});
