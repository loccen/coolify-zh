<?php

use Illuminate\Support\Facades\App;

it('wires shared webhook translations', function () {
    $view = file_get_contents(resource_path('views/livewire/project/shared/webhooks.blade.php'));

    expect($view)
        ->toContain("__('Deploy Webhook (auth required)')")
        ->toContain("__('Manual Git Webhooks')")
        ->toContain("__('Webhook Configuration on GitHub')")
        ->toContain("__('You are using an official Git App. You do not need manual webhooks.')");
});

it('resolves representative webhook translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Webhook Configuration on GitHub'))->toBe('GitHub 上的 Webhook 配置')
        ->and(__('You are using an official Git App. You do not need manual webhooks.'))->toBe('你正在使用官方代码源应用，因此不需要手动配置 Webhook。');
});
