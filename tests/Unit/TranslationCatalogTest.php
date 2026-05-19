<?php

use Illuminate\Support\Facades\App;
use Tests\TestCase;

uses(TestCase::class);

it('resolves migrated flat translation keys from php language files', function () {
    App::setLocale('en');

    expect(__('auth.login'))->toBe('Login')
        ->and(__('auth.login.github'))->toBe('Login with GitHub')
        ->and(__('button.save'))->toBe('Save')
        ->and(__('input.password'))->toBe('Password')
        ->and(__('input.password.again'))->toBe('Password again')
        ->and(__('database.delete_backups_locally'))->toBe('All backups will be permanently deleted from local storage.')
        ->and(__('resource.docker_cleanup'))->toBe('Run Docker Cleanup (remove unused images and builder cache).')
        ->and(__('service.stop'))->toBe('This service will be stopped.');
});

it('keeps json translations working beside migrated php files', function () {
    App::setLocale('zh_CN');

    expect(__('auth.login'))->toBe('登录')
        ->and(__('auth.login.github'))->toBe('使用 GitHub 登录')
        ->and(__('repository.url'))->toContain('https://github.com/coollabsio/coolify-examples')
        ->and(__('warning.sslipdomain'))->toContain('sslip');
});

it('resolves migrated flat translation keys from zh_CN php language files', function () {
    App::setLocale('zh_CN');

    expect(__('database.delete_backups_locally'))->toBe('所有备份都会从本地存储中永久删除。')
        ->and(__('resource.docker_cleanup'))->toBe('执行 Docker 清理（删除未使用的镜像和构建缓存）。')
        ->and(__('service.stop'))->toBe('此服务即将停止。')
        ->and(__('terminal.toasts.reconnecting'))->toBe('终端 WebSocket 连接已断开，正在重新连接...');
});
