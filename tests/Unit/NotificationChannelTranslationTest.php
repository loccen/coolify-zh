<?php

use App\Models\ScheduledTask;
use App\Models\ScheduledDatabaseBackup;
use App\Models\Server;
use App\Notifications\Container\ContainerStopped;
use App\Notifications\Database\BackupSuccessWithS3Warning;
use App\Notifications\Internal\GeneralNotification;
use App\Notifications\ScheduledTask\TaskFailed;
use App\Notifications\Server\DockerCleanupFailed;
use App\Notifications\Server\ForceDisabled;
use App\Notifications\Server\Reachable;
use App\Notifications\Server\Unreachable;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function () {
    Mockery::close();
});

function mockServer(string $name = 'Test Server', string $uuid = 'server-uuid'): Server
{
    $server = Mockery::mock(Server::class)->makePartial();
    $server->id = 1;
    $server->name = $name;
    $server->uuid = $uuid;

    return $server;
}

it('renders scheduled task, container, and backup notifications in chinese', function () {
    $task = Mockery::mock(ScheduledTask::class)->makePartial();
    $task->name = 'nightly-cleanup';
    $task->uuid = 'task-1';
    $task->application = null;
    $task->service = null;

    $taskFailed = new TaskFailed($task, '命令超时');
    $taskFailed->locale = 'zh_CN';

    expect($taskFailed->toSlack()->title)->toBe('定时任务执行失败')
        ->and($taskFailed->toSlack()->description)->toContain('错误输出')
        ->and($taskFailed->toTelegram()['message'])->toContain('定时任务（nightly-cleanup）执行失败');

    $server = mockServer('prod-server');
    $containerStopped = new ContainerStopped('nginx', $server);
    $containerStopped->locale = 'zh_CN';

    expect($containerStopped->toDiscord()->title)->toBe(':cross_mark: 资源已停止')
        ->and($containerStopped->toSlack()->description)->toContain('资源（nginx）已意外停止');

    $backup = Mockery::mock(ScheduledDatabaseBackup::class)->makePartial();
    $backup->frequency = 'daily';
    $database = (object) ['name' => 'main-db', 'uuid' => 'db-1'];

    $backupWarning = new BackupSuccessWithS3Warning($backup, $database, 'postgres', '认证失败');
    $backupWarning->locale = 'zh_CN';

    expect($backupWarning->toDiscord()->title)->toBe(':warning: 数据库备份已在本地完成，但上传到 S3 失败')
        ->and($backupWarning->toTelegram()['message'])->toContain('S3 错误');
});

it('renders server state and cleanup notifications in chinese', function () {
    $server = mockServer('cn-server');

    $reachable = new Reachable($server);
    $reachable->locale = 'zh_CN';
    expect($reachable->toTelegram()['message'])->toContain("服务器 'cn-server' 已恢复");

    $unreachable = new Unreachable($server);
    $unreachable->locale = 'zh_CN';
    expect($unreachable->toSlack()->title)->toBe('服务器不可达')
        ->and($unreachable->toSlack()->description)->toContain('所有自动化与集成都已关闭');

    $dockerCleanupFailed = new DockerCleanupFailed($server, '镜像清理失败');
    $dockerCleanupFailed->locale = 'zh_CN';
    expect($dockerCleanupFailed->toSlack()->title)->toBe('Coolify: [需要处理] Docker 清理任务失败')
        ->and($dockerCleanupFailed->toPushover()->message)->toContain('Docker 清理任务失败');

    $forceDisabled = new ForceDisabled($server);
    $forceDisabled->locale = 'zh_CN';
    expect($forceDisabled->toTelegram()['message'])->toContain('因未付费已被禁用');
});

it('renders general notification titles in chinese', function () {
    $notification = new GeneralNotification('这是一条系统消息');
    $notification->locale = 'zh_CN';

    expect($notification->toDiscord()->title)->toBe('Coolify: 通用通知')
        ->and($notification->toPushover()->title)->toBe('通用通知')
        ->and($notification->toSlack()->title)->toBe('Coolify: 通用通知');
});
