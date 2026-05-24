<?php

use App\Console\Commands\Dev;
use Illuminate\Support\Facades\App;

it('wires dev command phase one strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/Dev.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.dev.description', locale: app()->getLocale())")
        ->toContain("trans('console.dev.info.generating_app_key'")
        ->toContain("trans('console.dev.info.generating_storage_link'")
        ->toContain("trans('console.dev.info.initializing_instance'")
        ->toContain("trans('console.dev.info.instance_already_initialized'")
        ->toContain("trans('console.dev.info.cleaning_up_redis'")
        ->toContain("trans('console.dev.info.redis_cleanup_completed'")
        ->not->toContain("trans('console.dev.error.redis_cleanup_failed'")
        ->not->toContain("trans('console.dev.info.marked_as_failed'")
        ->not->toContain("trans('console.dev.info.marked_backup_as_failed'");

    expect($enTranslations['dev']['description'])->toBe('Helper commands for development.')
        ->and($enTranslations['dev']['info']['generating_app_key'])->toBe('Generating APP_KEY.')
        ->and($enTranslations['dev']['info']['generating_storage_link'])->toBe('Generating storage link.')
        ->and($enTranslations['dev']['info']['initializing_instance'])->toBe('Initializing instance, seeding database.')
        ->and($enTranslations['dev']['info']['instance_already_initialized'])->toBe('Instance already initialized.')
        ->and($enTranslations['dev']['info']['cleaning_up_redis'])->toBe('Cleaning up Redis (stuck jobs and stale locks)...')
        ->and($enTranslations['dev']['info']['redis_cleanup_completed'])->toBe('Redis cleanup completed.');

    expect($zhTranslations['dev']['description'])->toBe('开发辅助命令。')
        ->and($zhTranslations['dev']['info']['generating_app_key'])->toBe('正在生成 APP_KEY。')
        ->and($zhTranslations['dev']['info']['generating_storage_link'])->toBe('正在生成 storage 链接。')
        ->and($zhTranslations['dev']['info']['initializing_instance'])->toBe('正在初始化实例并填充数据库。')
        ->and($zhTranslations['dev']['info']['instance_already_initialized'])->toBe('实例已初始化。')
        ->and($zhTranslations['dev']['info']['cleaning_up_redis'])->toBe('正在清理 Redis（卡住的作业和过期锁）...')
        ->and($zhTranslations['dev']['info']['redis_cleanup_completed'])->toBe('Redis 清理完成。');
});

it('resolves dev command phase one translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.dev.description'))->toBe('Helper commands for development.')
        ->and(trans('console.dev.info.generating_app_key'))->toBe('Generating APP_KEY.')
        ->and(trans('console.dev.info.generating_storage_link'))->toBe('Generating storage link.')
        ->and(trans('console.dev.info.initializing_instance'))->toBe('Initializing instance, seeding database.')
        ->and(trans('console.dev.info.instance_already_initialized'))->toBe('Instance already initialized.')
        ->and(trans('console.dev.info.cleaning_up_redis'))->toBe('Cleaning up Redis (stuck jobs and stale locks)...')
        ->and(trans('console.dev.info.redis_cleanup_completed'))->toBe('Redis cleanup completed.')
        ->and((new Dev)->getDescription())->toBe('Helper commands for development.');

    App::setLocale('zh_CN');

    expect(trans('console.dev.description'))->toBe('开发辅助命令。')
        ->and(trans('console.dev.info.generating_app_key'))->toBe('正在生成 APP_KEY。')
        ->and(trans('console.dev.info.generating_storage_link'))->toBe('正在生成 storage 链接。')
        ->and(trans('console.dev.info.initializing_instance'))->toBe('正在初始化实例并填充数据库。')
        ->and(trans('console.dev.info.instance_already_initialized'))->toBe('实例已初始化。')
        ->and(trans('console.dev.info.cleaning_up_redis'))->toBe('正在清理 Redis（卡住的作业和过期锁）...')
        ->and(trans('console.dev.info.redis_cleanup_completed'))->toBe('Redis 清理完成。')
        ->and((new Dev)->getDescription())->toBe('开发辅助命令。');
});
