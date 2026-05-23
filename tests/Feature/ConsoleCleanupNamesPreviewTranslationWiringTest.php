<?php

use App\Console\Commands\CleanupNames;
use Illuminate\Support\Facades\App;

it('wires cleanup names dry-run preview strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupNames.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.cleanup_names.preview.header'")
        ->toContain("trans('console.cleanup_names.preview.from'")
        ->toContain("trans('console.cleanup_names.preview.to'");

    expect($enTranslations['cleanup_names']['preview']['header'])->toBe('🧹 :model #:id')
        ->and($enTranslations['cleanup_names']['preview']['from'])->toBe('    From: :value')
        ->and($enTranslations['cleanup_names']['preview']['to'])->toBe('    To: :value');

    expect($zhTranslations['cleanup_names']['preview']['header'])->toBe('🧹 :model #:id')
        ->and($zhTranslations['cleanup_names']['preview']['from'])->toBe('    从：:value')
        ->and($zhTranslations['cleanup_names']['preview']['to'])->toBe('    到：:value');
});

it('resolves cleanup names dry-run preview strings in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_names.preview.header', ['model' => 'Project', 'id' => 42]))->toBe('🧹 Project #42')
        ->and(trans('console.cleanup_names.preview.from', ['value' => 'bad name']))->toBe('    From: bad name')
        ->and(trans('console.cleanup_names.preview.to', ['value' => 'good-name']))->toBe('    To: good-name')
        ->and((new CleanupNames)->getDescription())->toBe('Sanitize name fields by removing dangerous characters');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_names.preview.header', ['model' => 'Project', 'id' => 42]))->toBe('🧹 Project #42')
        ->and(trans('console.cleanup_names.preview.from', ['value' => 'bad name']))->toBe('    从：bad name')
        ->and(trans('console.cleanup_names.preview.to', ['value' => 'good-name']))->toBe('    到：good-name')
        ->and((new CleanupNames)->getDescription())->toBe('Sanitize name fields by removing dangerous characters');
});
