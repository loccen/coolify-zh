<?php

use App\Console\Commands\CleanupStuckedResources;
use Illuminate\Support\Facades\App;

it('wires cleanup stucked resources batch a console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupStuckedResources.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.cleanup_stucked_resources.description', locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.force_deleting_stuck_server', ['name' => \$server->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_application', ['name' => \$application->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_postgresql', ['name' => \$postgresql->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_redis', ['name' => \$redis->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => \$serviceApp->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => \$serviceDb->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_stuck_scheduledtask', ['name' => \$scheduled_task->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.application_without_environment', ['name' => \$application->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.application_without_destination', ['name' => \$application->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.application_without_server', ['name' => \$application->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.postgresql_without_environment', ['name' => \$postgresql->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.redis_without_server', ['name' => \$redis->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.mongodb_without_server', ['name' => \$mongodb->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.mysql_without_destination', ['name' => \$mysql->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.mariadb_without_server', ['name' => \$mariadb->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.service_without_environment', ['name' => \$service->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.service_application_without_service', ['name' => \$service->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.service_database_without_service', ['name' => \$service->name], locale: app()->getLocale())")
        ->toContain("trans('console.cleanup_stucked_resources.info.deleting_orphaned_ssl_certificate', ['id' => \$cert->id, 'server_id' => \$cert->server_id], locale: app()->getLocale())");

    expect($enTranslations['cleanup_stucked_resources']['description'])->toBe('Cleanup stucked resources.')
        ->and($enTranslations['cleanup_stucked_resources']['info']['force_deleting_stuck_server'])->toBe('Force deleting stuck server: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_stuck_application'])->toBe('Deleting stuck application: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_stuck_serviceapp'])->toBe('Deleting stuck serviceapp: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['application_without_server'])->toBe('Application without server: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['service_database_without_service'])->toBe('ServiceDatabase without service: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_orphaned_ssl_certificate'])->toBe('Deleting orphaned SSL certificate: :id (server_id: :server_id)');

    expect($zhTranslations['cleanup_stucked_resources']['description'])->toBe('清理卡住的资源。')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['force_deleting_stuck_server'])->toBe('正在强制删除卡住的服务器：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_stuck_application'])->toBe('正在删除卡住的应用：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_stuck_serviceapp'])->toBe('正在删除卡住的 serviceapp：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['application_without_server'])->toBe('应用缺少服务器：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['service_database_without_service'])->toBe('ServiceDatabase 缺少 service：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_orphaned_ssl_certificate'])->toBe('正在删除孤立的 SSL 证书：:id（server_id: :server_id）');
});

it('resolves cleanup stucked resources batch a console strings in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_stucked_resources.description'))->toBe('Cleanup stucked resources.')
        ->and(trans('console.cleanup_stucked_resources.info.force_deleting_stuck_server', ['name' => 'srv-a']))->toBe('Force deleting stuck server: srv-a')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_stuck_application_preview', ['identifier' => 'app.example.test']))->toBe('Deleting stuck application preview: app.example.test')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => 'svc-app']))->toBe('Deleting stuck serviceapp: svc-app')
        ->and(trans('console.cleanup_stucked_resources.info.application_without_destination', ['name' => 'demo-app']))->toBe('Application without destination: demo-app')
        ->and(trans('console.cleanup_stucked_resources.info.postgresql_without_server', ['name' => 'pg-main']))->toBe('Postgresql without server: pg-main')
        ->and(trans('console.cleanup_stucked_resources.info.redis_without_environment', ['name' => 'redis-cache']))->toBe('Redis without environment: redis-cache')
        ->and(trans('console.cleanup_stucked_resources.info.service_application_without_service', ['name' => 'svc-app']))->toBe('ServiceApplication without service: svc-app')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_orphaned_ssl_certificate', ['id' => 12, 'server_id' => 34]))->toBe('Deleting orphaned SSL certificate: 12 (server_id: 34)')
        ->and((new CleanupStuckedResources)->getDescription())->toBe('Cleanup stucked resources.');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_stucked_resources.description'))->toBe('清理卡住的资源。')
        ->and(trans('console.cleanup_stucked_resources.info.force_deleting_stuck_server', ['name' => 'srv-a']))->toBe('正在强制删除卡住的服务器：srv-a')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_stuck_application_preview', ['identifier' => 'app.example.test']))->toBe('正在删除卡住的应用预览：app.example.test')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => 'svc-app']))->toBe('正在删除卡住的 serviceapp：svc-app')
        ->and(trans('console.cleanup_stucked_resources.info.application_without_destination', ['name' => 'demo-app']))->toBe('应用缺少目标位置：demo-app')
        ->and(trans('console.cleanup_stucked_resources.info.postgresql_without_server', ['name' => 'pg-main']))->toBe('Postgresql 缺少服务器：pg-main')
        ->and(trans('console.cleanup_stucked_resources.info.redis_without_environment', ['name' => 'redis-cache']))->toBe('Redis 缺少环境：redis-cache')
        ->and(trans('console.cleanup_stucked_resources.info.service_application_without_service', ['name' => 'svc-app']))->toBe('ServiceApplication 缺少 service：svc-app')
        ->and(trans('console.cleanup_stucked_resources.info.deleting_orphaned_ssl_certificate', ['id' => 12, 'server_id' => 34]))->toBe('正在删除孤立的 SSL 证书：12（server_id: 34）')
        ->and((new CleanupStuckedResources)->getDescription())->toBe('清理卡住的资源。');
});
