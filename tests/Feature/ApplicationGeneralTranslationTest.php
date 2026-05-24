<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in application general view', function () {
    $view = file_get_contents(base_path('resources/views/livewire/project/application/general.blade.php'));

    expect($view)
        ->toContain("{{ __('General') }}")
        ->toContain("__('Reload Compose File')")
        ->toContain("__('Load Compose File')")
        ->toContain("__('Build Pack')")
        ->toContain("__('Railpack (Beta)')")
        ->toContain("__('Generate Domain')")
        ->toContain("__('Docker Registry')")
        ->toContain("__('Show Raw Compose')")
        ->toContain("__('Escape special characters in labels?')")
        ->toContain("__('Pre/Post Deployment Commands')");
});

it('resolves application general translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('General'))->toBe('常规')
        ->and(__('Save'))->toBe('保存')
        ->and(__('Build Pack'))->toBe('构建包')
        ->and(__('Railpack (Beta)'))->toBe('Railpack（测试版）')
        ->and(__('Reload Compose File'))->toBe('重新加载 Compose 文件')
        ->and(__('Load Compose File'))->toBe('加载 Compose 文件')
        ->and(__('Generate Domain'))->toBe('生成域名')
        ->and(__('Show Raw Compose'))->toBe('显示原始 Compose')
        ->and(__('Permanently Reset Labels'))->toBe('永久重置标签')
        ->and(__('PORT environment variable detected (:port)', ['port' => 3000]))->toBe('检测到 PORT 环境变量（3000）');
});
