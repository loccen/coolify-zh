<?php

use App\Console\Commands\CleanupUnreachableServers;
use Illuminate\Support\Facades\App;

it('wires cleanup unreachable servers console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupUnreachableServers.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.cleanup_unreachable_servers.description'")
        ->toContain("trans('console.cleanup_unreachable_servers.running'")
        ->toContain("trans('console.cleanup_unreachable_servers.cleanup_server'");

    expect($enTranslations['cleanup_unreachable_servers']['description'])->toBe('Cleanup Unreachable Servers (7 days)')
        ->and($enTranslations['cleanup_unreachable_servers']['running'])->toBe('Running unreachable server cleanup...')
        ->and($enTranslations['cleanup_unreachable_servers']['cleanup_server'])->toBe('Cleanup unreachable server :id with name :name');

    expect($zhTranslations['cleanup_unreachable_servers']['description'])->toBe('清理不可达服务器（7 天）')
        ->and($zhTranslations['cleanup_unreachable_servers']['running'])->toBe('正在清理不可达服务器...')
        ->and($zhTranslations['cleanup_unreachable_servers']['cleanup_server'])->toBe('清理不可达服务器 :id，名称为 :name');
});

it('resolves cleanup unreachable servers strings in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_unreachable_servers.running'))->toBe('Running unreachable server cleanup...')
        ->and(trans('console.cleanup_unreachable_servers.cleanup_server', ['id' => 12, 'name' => 'alpha']))->toBe('Cleanup unreachable server 12 with name alpha')
        ->and((new CleanupUnreachableServers)->getDescription())->toBe('Cleanup Unreachable Servers (7 days)');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_unreachable_servers.running'))->toBe('正在清理不可达服务器...')
        ->and(trans('console.cleanup_unreachable_servers.cleanup_server', ['id' => 12, 'name' => 'alpha']))->toBe('清理不可达服务器 12，名称为 alpha')
        ->and((new CleanupUnreachableServers)->getDescription())->toBe('清理不可达服务器（7 天）');
});
