<?php

use App\Models\Project;
use App\Models\S3Storage;
use App\Models\Server;
use Database\Seeders\PrivateKeySeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\S3StorageSeeder;
use Database\Seeders\ServerSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds localized development project, server, and storage examples', function () {
    $this->seed([
        UserSeeder::class,
        TeamSeeder::class,
        PrivateKeySeeder::class,
        ProjectSeeder::class,
        ServerSeeder::class,
        S3StorageSeeder::class,
    ]);

    $project = Project::query()->where('uuid', 'project')->first();
    $server = Server::query()->where('uuid', 'localhost')->first();
    $storage = S3Storage::query()->where('uuid', 'minio')->first();

    expect($project)
        ->not->toBeNull()
        ->and($project->name)->toBe('我的第一个项目')
        ->and($project->description)->toBe('这是开发环境中的测试项目');

    expect($server)
        ->not->toBeNull()
        ->and($server->description)->toBe('这是开发模式下的测试 Docker 容器');

    expect($storage)
        ->not->toBeNull()
        ->and($storage->name)->toBe('本地 MinIO')
        ->and($storage->description)->toBe('本地 MinIO S3 存储');
});
