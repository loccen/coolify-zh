<?php

use App\Models\Application;
use App\Models\Project;
use App\Models\PrivateKey;
use App\Models\S3Storage;
use App\Models\Server;
use App\Models\StandalonePostgresql;
use App\Models\StandaloneRedis;
use Database\Seeders\ApplicationSeeder;
use Database\Seeders\PrivateKeySeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\S3StorageSeeder;
use Database\Seeders\ServerSeeder;
use Database\Seeders\StandaloneDockerSeeder;
use Database\Seeders\StandalonePostgresqlSeeder;
use Database\Seeders\StandaloneRedisSeeder;
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

it('seeds localized development application, database, and private key examples', function () {
    $this->seed([
        UserSeeder::class,
        TeamSeeder::class,
        PrivateKeySeeder::class,
        ServerSeeder::class,
        ProjectSeeder::class,
        StandaloneDockerSeeder::class,
        ApplicationSeeder::class,
        StandalonePostgresqlSeeder::class,
        StandaloneRedisSeeder::class,
    ]);

    $dockerfileExample = Application::query()->where('uuid', 'dockerfile')->first();
    $crashLoopExample = Application::query()->where('uuid', 'crashloop')->first();
    $postgresql = StandalonePostgresql::query()->where('uuid', 'postgresql')->first();
    $redis = StandaloneRedis::query()->first();
    $sshKey = PrivateKey::query()->where('uuid', 'ssh')->first();
    $githubKey = PrivateKey::query()->where('uuid', 'github-key')->first();

    expect($dockerfileExample)
        ->not->toBeNull()
        ->and($dockerfileExample->name)->toBe('Dockerfile 示例');

    expect($crashLoopExample)
        ->not->toBeNull()
        ->and($crashLoopExample->name)->toBe('崩溃循环示例');

    expect($postgresql)
        ->not->toBeNull()
        ->and($postgresql->name)->toBe('本地 PostgreSQL')
        ->and($postgresql->description)->toBe('用于测试的本地 PostgreSQL');

    expect($redis)
        ->not->toBeNull()
        ->and($redis->name)->toBe('本地 Redis')
        ->and($redis->description)->toBe('用于测试的本地 Redis');

    expect($sshKey)
        ->not->toBeNull()
        ->and($sshKey->name)->toBe('测试主机密钥')
        ->and($sshKey->description)->toBe('这是测试 Docker 容器使用的私钥');

    expect($githubKey)
        ->not->toBeNull()
        ->and($githubKey->name)->toBe('开发环境 GitHub App')
        ->and($githubKey->description)->toBe('这是开发环境 GitHub App 使用的私钥');
});
