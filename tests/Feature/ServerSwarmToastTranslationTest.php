<?php

use Illuminate\Support\Facades\App;

it('uses server translation lookups in the swarm component toast dispatch', function () {
    $component = file_get_contents(app_path('Livewire/Server/Swarm.php'));

    expect($component)
        ->toContain("__('server.toasts.swarm_settings_updated')");
});

it('resolves swarm toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.swarm_settings_updated'))
        ->toBe('Swarm settings updated.');

    App::setLocale('zh_CN');

    expect(__('server.toasts.swarm_settings_updated'))
        ->toBe('Swarm 设置已更新。');
});
