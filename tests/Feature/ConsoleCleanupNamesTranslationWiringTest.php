<?php

it('wires cleanup names console translations in english', function () {
    app()->setLocale('en');

    expect(file_get_contents(base_path('app/Console/Commands/CleanupNames.php')))
        ->toContain("trans('console.cleanup_names.error.unknown_model'")
        ->toContain("trans('console.cleanup_names.info.available_models'")
        ->toContain("trans('console.cleanup_names.info.would_sanitize'")
        ->toContain("trans('console.cleanup_names.info.sanitized'")
        ->toContain("trans('console.cleanup_names.error.processing'");

    expect(trans('console.cleanup_names.error.unknown_model', ['model' => 'Foo']))
        ->toBe('Unknown model: Foo')
        ->and(trans('console.cleanup_names.info.available_models', ['models' => 'Foo, Bar']))
        ->toBe('Available models: Foo, Bar')
        ->and(trans('console.cleanup_names.info.would_sanitize', ['count' => 3]))
        ->toBe('Name cleanup: would sanitize 3 records')
        ->and(trans('console.cleanup_names.info.sanitized', ['count' => 3]))
        ->toBe('Name cleanup: sanitized 3 records')
        ->and(trans('console.cleanup_names.error.processing', ['model' => 'Foo', 'error' => 'boom']))
        ->toBe('Error processing Foo: boom');
});

it('wires cleanup names console translations in chinese', function () {
    app()->setLocale('zh_CN');

    expect(trans('console.cleanup_names.error.unknown_model', ['model' => 'Foo']))
        ->toBe('未知模型：Foo')
        ->and(trans('console.cleanup_names.info.available_models', ['models' => 'Foo, Bar']))
        ->toBe('可用模型：Foo, Bar')
        ->and(trans('console.cleanup_names.info.would_sanitize', ['count' => 3]))
        ->toBe('名称清理：将处理 3 条记录')
        ->and(trans('console.cleanup_names.info.sanitized', ['count' => 3]))
        ->toBe('名称清理：已处理 3 条记录')
        ->and(trans('console.cleanup_names.error.processing', ['model' => 'Foo', 'error' => 'boom']))
        ->toBe('处理 Foo 时出错：boom');
});
