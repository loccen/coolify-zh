<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in representative livewire toast dispatches', function () {
    $databaseRedisGeneral = file_get_contents(app_path('Livewire/Project/Database/Redis/General.php'));
    $databaseHeading = file_get_contents(app_path('Livewire/Project/Database/Heading.php'));
    $serviceIndex = file_get_contents(app_path('Livewire/Project/Service/Index.php'));
    $serviceConfiguration = file_get_contents(app_path('Livewire/Project/Service/Configuration.php'));
    $serviceStorage = file_get_contents(app_path('Livewire/Project/Service/Storage.php'));
    $serviceFileStorage = file_get_contents(app_path('Livewire/Project/Service/FileStorage.php'));
    $serviceStackForm = file_get_contents(app_path('Livewire/Project/Service/StackForm.php'));
    $serviceEditCompose = file_get_contents(app_path('Livewire/Project/Service/EditCompose.php'));

    expect($databaseRedisGeneral)
        ->toContain("__('Database updated.')")
        ->toContain("__('Database must be started to be publicly accessible.')")
        ->toContain("__('SSL configuration updated.')")
        ->and($databaseHeading)
        ->toContain("__('Gracefully stopping database.')")
        ->and($serviceIndex)
        ->toContain("__('Database deleted.')")
        ->toContain("__('Application deleted.')")
        ->toContain("__('Database saved.')")
        ->and($serviceConfiguration)
        ->toContain("__('Service application restarted successfully.')")
        ->toContain("__('Service database restarted successfully.')")
        ->and($serviceStorage)
        ->toContain("__('Volume added successfully')")
        ->toContain("__('Directory mount added successfully')")
        ->and($serviceFileStorage)
        ->toContain("__('File storage loaded from server.')")
        ->toContain("__('File updated.')")
        ->and($serviceStackForm)
        ->toContain("__('Service settings saved.')")
        ->toContain("__('Service saved.')")
        ->and($serviceEditCompose)
        ->toContain("__('Docker compose is valid.')")
        ->toContain("__('Saving new docker compose...')")
        ->toContain("__('Service updated successfully')");
});

it('resolves representative livewire toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Database updated.'))->toBe('数据库已更新。')
        ->and(__('You need to restart the service for the changes to take effect.'))->toBe('需要重启服务后，更改才会生效。')
        ->and(__('Database must be started to be publicly accessible.'))->toBe('数据库必须先启动，才能公开访问。')
        ->and(__('SSL configuration updated.'))->toBe('SSL 配置已更新。')
        ->and(__('Database deleted.'))->toBe('数据库已删除。')
        ->and(__('Application deleted.'))->toBe('应用已删除。')
        ->and(__('Service settings saved.'))->toBe('服务设置已保存。')
        ->and(__('Service saved.'))->toBe('服务已保存。')
        ->and(__('Docker compose is valid.'))->toBe('Docker Compose 配置有效。')
        ->and(__('Saving new docker compose...'))->toBe('正在保存新的 Docker Compose...')
        ->and(__('Service updated successfully'))->toBe('服务已成功更新。')
        ->and(__('Gracefully stopping database.'))->toBe('正在优雅停止数据库。');
});
