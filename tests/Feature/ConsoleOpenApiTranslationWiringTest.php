<?php

use App\Console\Commands\Generate\OpenApi;
use Illuminate\Support\Facades\App;

it('wires generate openapi command strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/Generate/OpenApi.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.generate_openapi.description'")
        ->toContain("trans('console.generate_openapi.generating'")
        ->toContain("trans('console.generate_openapi.converted'");

    expect($enTranslations['generate_openapi']['description'])->toBe('Generate OpenAPI documentation.')
        ->and($enTranslations['generate_openapi']['generating'])->toBe('Generating OpenAPI documentation.')
        ->and($enTranslations['generate_openapi']['converted'])->toBe('Converted OpenAPI YAML to JSON.');

    expect($zhTranslations['generate_openapi']['description'])->toBe('生成 OpenAPI 文档。')
        ->and($zhTranslations['generate_openapi']['generating'])->toBe('正在生成 OpenAPI 文档。')
        ->and($zhTranslations['generate_openapi']['converted'])->toBe('已将 OpenAPI YAML 转换为 JSON。');
});

it('resolves generate openapi translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.generate_openapi.description'))->toBe('Generate OpenAPI documentation.')
        ->and(trans('console.generate_openapi.generating'))->toBe('Generating OpenAPI documentation.')
        ->and(trans('console.generate_openapi.converted'))->toBe('Converted OpenAPI YAML to JSON.')
        ->and((new OpenApi)->getDescription())->toBe('Generate OpenAPI documentation.');

    App::setLocale('zh_CN');

    expect(trans('console.generate_openapi.description'))->toBe('生成 OpenAPI 文档。')
        ->and(trans('console.generate_openapi.generating'))->toBe('正在生成 OpenAPI 文档。')
        ->and(trans('console.generate_openapi.converted'))->toBe('已将 OpenAPI YAML 转换为 JSON。')
        ->and((new OpenApi)->getDescription())->toBe('生成 OpenAPI 文档。');
});
