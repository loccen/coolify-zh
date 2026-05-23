<?php

use App\Console\Commands\CleanupApplicationDeploymentQueue;
use Illuminate\Support\Facades\App;

it('wires cleanup application deployment queue console description through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupApplicationDeploymentQueue.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.cleanup_application_deployment_queue.description'");

    expect($enTranslations['cleanup_application_deployment_queue']['description'])->toBe('Cleanup application deployment queue.')
        ->and($zhTranslations['cleanup_application_deployment_queue']['description'])->toBe('清理应用部署队列。');
});

it('resolves cleanup application deployment queue description in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_application_deployment_queue.description'))->toBe('Cleanup application deployment queue.')
        ->and((new CleanupApplicationDeploymentQueue)->getDescription())->toBe('Cleanup application deployment queue.');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_application_deployment_queue.description'))->toBe('清理应用部署队列。')
        ->and((new CleanupApplicationDeploymentQueue)->getDescription())->toBe('清理应用部署队列。');
});
