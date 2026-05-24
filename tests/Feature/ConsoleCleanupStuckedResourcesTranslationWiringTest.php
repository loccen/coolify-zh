<?php

use App\Console\Commands\CleanupStuckedResources;
use Illuminate\Support\Facades\App;

it('wires cleanup stucked resources batch a console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CleanupStuckedResources.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';
    $batchBCommandWiring = [
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stucked_resources', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_servers', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application_deployment_queue', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_postgresql', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_redis', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_keydb', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_dragonfly', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_clickhouse', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mongodb', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mysql', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mariadb', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_service', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_serviceapp', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_scheduledtasks', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_checking_server_for_scheduledbackup', ['id' => \$scheduled_backup->id, 'error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_scheduledbackups', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_application', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_postgresql', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_redis', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_mongodb', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_mysql', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_mariadb', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_service', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_service_applications', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_service_databases', ['error' => \$e->getMessage()], locale: app()->getLocale())",
        "trans('console.cleanup_stucked_resources.error.error_in_cleaning_orphaned_ssl_certificates', ['error' => \$e->getMessage()], locale: app()->getLocale())",
    ];
    $expectedEnErrors = [
        'error_in_cleaning_stucked_resources' => 'Error in cleaning stucked resources: :error',
        'error_in_cleaning_stuck_servers' => 'Error in cleaning stuck servers: :error',
        'error_in_cleaning_stuck_application_deployment_queue' => 'Error in cleaning stuck application deployment queue: :error',
        'error_in_cleaning_stuck_application' => 'Error in cleaning stuck application: :error',
        'error_in_cleaning_stuck_postgresql' => 'Error in cleaning stuck postgresql: :error',
        'error_in_cleaning_stuck_redis' => 'Error in cleaning stuck redis: :error',
        'error_in_cleaning_stuck_keydb' => 'Error in cleaning stuck keydb: :error',
        'error_in_cleaning_stuck_dragonfly' => 'Error in cleaning stuck dragonfly: :error',
        'error_in_cleaning_stuck_clickhouse' => 'Error in cleaning stuck clickhouse: :error',
        'error_in_cleaning_stuck_mongodb' => 'Error in cleaning stuck mongodb: :error',
        'error_in_cleaning_stuck_mysql' => 'Error in cleaning stuck mysql: :error',
        'error_in_cleaning_stuck_mariadb' => 'Error in cleaning stuck mariadb: :error',
        'error_in_cleaning_stuck_service' => 'Error in cleaning stuck service: :error',
        'error_in_cleaning_stuck_serviceapp' => 'Error in cleaning stuck serviceapp: :error',
        'error_in_cleaning_stuck_scheduledtasks' => 'Error in cleaning stuck scheduledtasks: :error',
        'error_checking_server_for_scheduledbackup' => 'Error checking server for scheduledbackup :id: :error',
        'error_in_cleaning_stuck_scheduledbackups' => 'Error in cleaning stuck scheduledbackups: :error',
        'error_in_application' => 'Error in application: :error',
        'error_in_postgresql' => 'Error in postgresql: :error',
        'error_in_redis' => 'Error in redis: :error',
        'error_in_mongodb' => 'Error in mongodb: :error',
        'error_in_mysql' => 'Error in mysql: :error',
        'error_in_mariadb' => 'Error in mariadb: :error',
        'error_in_service' => 'Error in service: :error',
        'error_in_service_applications' => 'Error in serviceApplications: :error',
        'error_in_service_databases' => 'Error in ServiceDatabases: :error',
        'error_in_cleaning_orphaned_ssl_certificates' => 'Error in cleaning orphaned SSL certificates: :error',
    ];
    $expectedZhErrors = [
        'error_in_cleaning_stucked_resources' => '清理 stucked resources 时出错：:error',
        'error_in_cleaning_stuck_servers' => '清理卡住的服务器时出错：:error',
        'error_in_cleaning_stuck_application_deployment_queue' => '清理卡住的应用部署队列时出错：:error',
        'error_in_cleaning_stuck_application' => '清理卡住的应用时出错：:error',
        'error_in_cleaning_stuck_postgresql' => '清理卡住的 postgresql 时出错：:error',
        'error_in_cleaning_stuck_redis' => '清理卡住的 redis 时出错：:error',
        'error_in_cleaning_stuck_keydb' => '清理卡住的 keydb 时出错：:error',
        'error_in_cleaning_stuck_dragonfly' => '清理卡住的 dragonfly 时出错：:error',
        'error_in_cleaning_stuck_clickhouse' => '清理卡住的 clickhouse 时出错：:error',
        'error_in_cleaning_stuck_mongodb' => '清理卡住的 mongodb 时出错：:error',
        'error_in_cleaning_stuck_mysql' => '清理卡住的 mysql 时出错：:error',
        'error_in_cleaning_stuck_mariadb' => '清理卡住的 mariadb 时出错：:error',
        'error_in_cleaning_stuck_service' => '清理卡住的服务时出错：:error',
        'error_in_cleaning_stuck_serviceapp' => '清理卡住的 serviceapp 时出错：:error',
        'error_in_cleaning_stuck_scheduledtasks' => '清理卡住的 scheduledtasks 时出错：:error',
        'error_checking_server_for_scheduledbackup' => '检查 scheduledbackup :id 的服务器时出错：:error',
        'error_in_cleaning_stuck_scheduledbackups' => '清理卡住的 scheduledbackups 时出错：:error',
        'error_in_application' => '处理 application 时出错：:error',
        'error_in_postgresql' => '处理 postgresql 时出错：:error',
        'error_in_redis' => '处理 redis 时出错：:error',
        'error_in_mongodb' => '处理 mongodb 时出错：:error',
        'error_in_mysql' => '处理 mysql 时出错：:error',
        'error_in_mariadb' => '处理 mariadb 时出错：:error',
        'error_in_service' => '处理服务时出错：:error',
        'error_in_service_applications' => '处理 serviceApplications 时出错：:error',
        'error_in_service_databases' => '处理 ServiceDatabases 时出错：:error',
        'error_in_cleaning_orphaned_ssl_certificates' => '清理孤立的 SSL 证书时出错：:error',
    ];

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

    foreach ($batchBCommandWiring as $wiring) {
        expect($command)->toContain($wiring);
    }

    expect($enTranslations['cleanup_stucked_resources']['description'])->toBe('Cleanup stucked resources.')
        ->and($enTranslations['cleanup_stucked_resources']['info']['force_deleting_stuck_server'])->toBe('Force deleting stuck server: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_stuck_application'])->toBe('Deleting stuck application: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_stuck_serviceapp'])->toBe('Deleting stuck serviceapp: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['application_without_server'])->toBe('Application without server: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['service_database_without_service'])->toBe('ServiceDatabase without service: :name')
        ->and($enTranslations['cleanup_stucked_resources']['info']['deleting_orphaned_ssl_certificate'])->toBe('Deleting orphaned SSL certificate: :id (server_id: :server_id)');

    foreach ($expectedEnErrors as $key => $value) {
        expect($enTranslations['cleanup_stucked_resources']['error'][$key])->toBe($value);
    }

    expect($zhTranslations['cleanup_stucked_resources']['description'])->toBe('清理卡住的资源。')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['force_deleting_stuck_server'])->toBe('正在强制删除卡住的服务器：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_stuck_application'])->toBe('正在删除卡住的应用：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_stuck_serviceapp'])->toBe('正在删除卡住的 serviceapp：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['application_without_server'])->toBe('应用缺少服务器：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['service_database_without_service'])->toBe('ServiceDatabase 缺少 service：:name')
        ->and($zhTranslations['cleanup_stucked_resources']['info']['deleting_orphaned_ssl_certificate'])->toBe('正在删除孤立的 SSL 证书：:id（server_id: :server_id）');

    foreach ($expectedZhErrors as $key => $value) {
        expect($zhTranslations['cleanup_stucked_resources']['error'][$key])->toBe($value);
    }
});

it('resolves cleanup stucked resources batch a console strings in en and zh_CN', function () {
    $expectedEnErrors = [
        'error_in_cleaning_stucked_resources' => 'Error in cleaning stucked resources: :error',
        'error_in_cleaning_stuck_servers' => 'Error in cleaning stuck servers: :error',
        'error_in_cleaning_stuck_application_deployment_queue' => 'Error in cleaning stuck application deployment queue: :error',
        'error_in_cleaning_stuck_application' => 'Error in cleaning stuck application: :error',
        'error_in_cleaning_stuck_postgresql' => 'Error in cleaning stuck postgresql: :error',
        'error_in_cleaning_stuck_redis' => 'Error in cleaning stuck redis: :error',
        'error_in_cleaning_stuck_keydb' => 'Error in cleaning stuck keydb: :error',
        'error_in_cleaning_stuck_dragonfly' => 'Error in cleaning stuck dragonfly: :error',
        'error_in_cleaning_stuck_clickhouse' => 'Error in cleaning stuck clickhouse: :error',
        'error_in_cleaning_stuck_mongodb' => 'Error in cleaning stuck mongodb: :error',
        'error_in_cleaning_stuck_mysql' => 'Error in cleaning stuck mysql: :error',
        'error_in_cleaning_stuck_mariadb' => 'Error in cleaning stuck mariadb: :error',
        'error_in_cleaning_stuck_service' => 'Error in cleaning stuck service: :error',
        'error_in_cleaning_stuck_serviceapp' => 'Error in cleaning stuck serviceapp: :error',
        'error_in_cleaning_stuck_scheduledtasks' => 'Error in cleaning stuck scheduledtasks: :error',
        'error_checking_server_for_scheduledbackup' => 'Error checking server for scheduledbackup :id: :error',
        'error_in_cleaning_stuck_scheduledbackups' => 'Error in cleaning stuck scheduledbackups: :error',
        'error_in_application' => 'Error in application: :error',
        'error_in_postgresql' => 'Error in postgresql: :error',
        'error_in_redis' => 'Error in redis: :error',
        'error_in_mongodb' => 'Error in mongodb: :error',
        'error_in_mysql' => 'Error in mysql: :error',
        'error_in_mariadb' => 'Error in mariadb: :error',
        'error_in_service' => 'Error in service: :error',
        'error_in_service_applications' => 'Error in serviceApplications: :error',
        'error_in_service_databases' => 'Error in ServiceDatabases: :error',
        'error_in_cleaning_orphaned_ssl_certificates' => 'Error in cleaning orphaned SSL certificates: :error',
    ];
    $expectedZhErrors = [
        'error_in_cleaning_stucked_resources' => '清理 stucked resources 时出错：:error',
        'error_in_cleaning_stuck_servers' => '清理卡住的服务器时出错：:error',
        'error_in_cleaning_stuck_application_deployment_queue' => '清理卡住的应用部署队列时出错：:error',
        'error_in_cleaning_stuck_application' => '清理卡住的应用时出错：:error',
        'error_in_cleaning_stuck_postgresql' => '清理卡住的 postgresql 时出错：:error',
        'error_in_cleaning_stuck_redis' => '清理卡住的 redis 时出错：:error',
        'error_in_cleaning_stuck_keydb' => '清理卡住的 keydb 时出错：:error',
        'error_in_cleaning_stuck_dragonfly' => '清理卡住的 dragonfly 时出错：:error',
        'error_in_cleaning_stuck_clickhouse' => '清理卡住的 clickhouse 时出错：:error',
        'error_in_cleaning_stuck_mongodb' => '清理卡住的 mongodb 时出错：:error',
        'error_in_cleaning_stuck_mysql' => '清理卡住的 mysql 时出错：:error',
        'error_in_cleaning_stuck_mariadb' => '清理卡住的 mariadb 时出错：:error',
        'error_in_cleaning_stuck_service' => '清理卡住的服务时出错：:error',
        'error_in_cleaning_stuck_serviceapp' => '清理卡住的 serviceapp 时出错：:error',
        'error_in_cleaning_stuck_scheduledtasks' => '清理卡住的 scheduledtasks 时出错：:error',
        'error_checking_server_for_scheduledbackup' => '检查 scheduledbackup :id 的服务器时出错：:error',
        'error_in_cleaning_stuck_scheduledbackups' => '清理卡住的 scheduledbackups 时出错：:error',
        'error_in_application' => '处理 application 时出错：:error',
        'error_in_postgresql' => '处理 postgresql 时出错：:error',
        'error_in_redis' => '处理 redis 时出错：:error',
        'error_in_mongodb' => '处理 mongodb 时出错：:error',
        'error_in_mysql' => '处理 mysql 时出错：:error',
        'error_in_mariadb' => '处理 mariadb 时出错：:error',
        'error_in_service' => '处理服务时出错：:error',
        'error_in_service_applications' => '处理 serviceApplications 时出错：:error',
        'error_in_service_databases' => '处理 ServiceDatabases 时出错：:error',
        'error_in_cleaning_orphaned_ssl_certificates' => '清理孤立的 SSL 证书时出错：:error',
    ];

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

    foreach ($expectedEnErrors as $key => $value) {
        $parameters = ['error' => 'boom'];
        if ($key === 'error_checking_server_for_scheduledbackup') {
            $parameters['id'] = 88;
        }

        expect(trans("console.cleanup_stucked_resources.error.{$key}", $parameters))
            ->toBe(str_replace([':id', ':error'], ['88', 'boom'], $value));
    }

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

    foreach ($expectedZhErrors as $key => $value) {
        $parameters = ['error' => 'boom'];
        if ($key === 'error_checking_server_for_scheduledbackup') {
            $parameters['id'] = 88;
        }

        expect(trans("console.cleanup_stucked_resources.error.{$key}", $parameters))
            ->toBe(str_replace([':id', ':error'], ['88', 'boom'], $value));
    }
});
