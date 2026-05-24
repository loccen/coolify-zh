<?php

use App\Console\Commands\Generate\Services;
use Illuminate\Support\Facades\App;

it('wires generate services console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/Generate/Services.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.generate_services.description'")
        ->toContain("trans('console.generate_services.info.ignoring'")
        ->toContain("trans('console.generate_services.info.processing'");

    expect($enTranslations['generate_services']['description'])->toBe('Generates service-templates json file based on /templates/compose directory')
        ->and($enTranslations['generate_services']['info']['ignoring'])->toBe('Ignoring :file')
        ->and($enTranslations['generate_services']['info']['processing'])->toBe('Processing :file');

    expect($zhTranslations['generate_services']['description'])->toBe('根据 /templates/compose 目录生成 service-templates json 文件')
        ->and($zhTranslations['generate_services']['info']['ignoring'])->toBe('忽略 :file')
        ->and($zhTranslations['generate_services']['info']['processing'])->toBe('正在处理 :file');
});

it('resolves generate services translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.generate_services.description'))->toBe('Generates service-templates json file based on /templates/compose directory')
        ->and(trans('console.generate_services.info.ignoring', ['file' => 'demo.yaml']))->toBe('Ignoring demo.yaml')
        ->and(trans('console.generate_services.info.processing', ['file' => 'demo.yaml']))->toBe('Processing demo.yaml')
        ->and((new Services)->getDescription())->toBe('Generates service-templates json file based on /templates/compose directory');

    App::setLocale('zh_CN');

    expect(trans('console.generate_services.description'))->toBe('根据 /templates/compose 目录生成 service-templates json 文件')
        ->and(trans('console.generate_services.info.ignoring', ['file' => 'demo.yaml']))->toBe('忽略 demo.yaml')
        ->and(trans('console.generate_services.info.processing', ['file' => 'demo.yaml']))->toBe('正在处理 demo.yaml')
        ->and((new Services)->getDescription())->toBe('根据 /templates/compose 目录生成 service-templates json 文件');
});
