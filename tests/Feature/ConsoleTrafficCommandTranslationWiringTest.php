<?php

use App\Console\Commands\CheckTraefikVersionCommand;
use App\Console\Commands\Migration;
use Illuminate\Support\Facades\App;

it('wires console traffic command strings through console translations', function () {
    $migrationCommand = file_get_contents(app_path('Console/Commands/Migration.php'));
    $traefikCommand = file_get_contents(app_path('Console/Commands/CheckTraefikVersionCommand.php'));

    expect($migrationCommand)
        ->toContain("trans('console.migration.description'")
        ->toContain("trans('console.migration.enabled'")
        ->toContain("trans('console.migration.disabled'");

    expect($traefikCommand)
        ->toContain("trans('console.traefik_check_version.description'")
        ->toContain("trans('console.traefik_check_version.checking'")
        ->toContain("trans('console.traefik_check_version.dispatched'")
        ->toContain("trans('console.traefik_check_version.notifications_pending'")
        ->toContain("trans('console.traefik_check_version.dispatch_failed'");
});

it('resolves console traffic command translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.migration.description'))->toBe('Start Migration')
        ->and(trans('console.migration.enabled'))->toBe('Migration is enabled on this server.')
        ->and(trans('console.migration.disabled'))->toBe('Migration is disabled on this server.')
        ->and((new Migration)->getDescription())->toBe('Start Migration')
        ->and(trans('console.traefik_check_version.description'))->toBe('Check Traefik proxy versions on all servers and send notifications for outdated versions')
        ->and(trans('console.traefik_check_version.checking'))->toBe('Checking Traefik versions on all servers...')
        ->and(trans('console.traefik_check_version.dispatched'))->toBe('Traefik version check job dispatched successfully.')
        ->and(trans('console.traefik_check_version.notifications_pending'))->toBe('Notifications will be sent to teams with outdated Traefik versions.')
        ->and(trans('console.traefik_check_version.dispatch_failed', ['message' => 'boom']))->toBe('Failed to dispatch Traefik version check job: boom')
        ->and((new CheckTraefikVersionCommand)->getDescription())->toBe('Check Traefik proxy versions on all servers and send notifications for outdated versions');

    App::setLocale('zh_CN');

    expect(trans('console.migration.description'))->toBe('开始迁移')
        ->and(trans('console.migration.enabled'))->toBe('本服务器已启用迁移。')
        ->and(trans('console.migration.disabled'))->toBe('本服务器未启用迁移。')
        ->and((new Migration)->getDescription())->toBe('开始迁移')
        ->and(trans('console.traefik_check_version.description'))->toBe('检查所有服务器上的 Traefik 代理版本，并向过期版本发送通知')
        ->and(trans('console.traefik_check_version.checking'))->toBe('正在检查所有服务器上的 Traefik 版本...')
        ->and(trans('console.traefik_check_version.dispatched'))->toBe('Traefik 版本检查任务已成功分发。')
        ->and(trans('console.traefik_check_version.notifications_pending'))->toBe('系统将向使用过期 Traefik 版本的团队发送通知。')
        ->and(trans('console.traefik_check_version.dispatch_failed', ['message' => 'boom']))->toBe('分发 Traefik 版本检查任务失败：boom')
        ->and((new CheckTraefikVersionCommand)->getDescription())->toBe('检查所有服务器上的 Traefik 代理版本，并向过期版本发送通知');
});
