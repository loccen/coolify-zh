<?php

use Illuminate\Support\Facades\App;

it('wires github source management translations', function () {
    $view = file_get_contents(resource_path('views/livewire/source/github/change.blade.php'));

    expect($view)
        ->toContain("__('GitHub App Name')")
        ->toContain("__('Install Repositories on GitHub')")
        ->toContain("__('No resources are currently using this GitHub App.')")
        ->toContain("__('Register Now')")
        ->toContain("__('Please select a webhook endpoint.')");
});

it('resolves representative github source follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('GitHub App Name'))->toBe('GitHub App 名称')
        ->and(__('Register Now'))->toBe('立即注册')
        ->and(__('Please select a webhook endpoint.'))->toBe('请选择 webhook 端点。');
});
