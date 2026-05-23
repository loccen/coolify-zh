<?php

use App\Models\Application;
use App\Models\GithubApp;
use App\Models\GitlabApp;
use App\Models\InstanceSettings;
use App\Models\PrivateKey;
use App\Models\Project;
use App\Models\S3Storage;
use App\Models\Server;
use App\Models\StandaloneDocker;
use App\Models\StandalonePostgresql;
use App\Models\StandaloneRedis;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\ApplicationSeeder;
use Database\Seeders\DevelopmentRailpackExamplesSeeder;
use Database\Seeders\GithubAppSeeder;
use Database\Seeders\GitlabAppSeeder;
use Database\Seeders\PrivateKeySeeder;
use Database\Seeders\ProductionSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\RootUserSeeder;
use Database\Seeders\S3StorageSeeder;
use Database\Seeders\ServerSeeder;
use Database\Seeders\StandaloneDockerSeeder;
use Database\Seeders\StandalonePostgresqlSeeder;
use Database\Seeders\StandaloneRedisSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds localized development user examples', function () {
    $this->seed(UserSeeder::class);

    expect(Application::query()->count())->toBe(0)
        ->and(User::query()->find(0)?->name)->toBe('根用户')
        ->and(User::query()->find(1)?->name)->toBe('普通用户（属于根团队）')
        ->and(User::query()->find(2)?->name)->toBe('普通用户（不属于根团队）');
});

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

it('seeds localized GitLab app example', function () {
    $this->seed(GitlabAppSeeder::class);

    $gitlabApp = GitlabApp::query()->find(1);

    expect($gitlabApp)
        ->not->toBeNull()
        ->and($gitlabApp->name)->toBe('公开 GitLab');
});

it('seeds localized development team, destination, and source examples', function () {
    $this->seed([
        UserSeeder::class,
        TeamSeeder::class,
        PrivateKeySeeder::class,
        ServerSeeder::class,
        StandaloneDockerSeeder::class,
        GithubAppSeeder::class,
    ]);

    $team = Team::query()->find(0);
    $destination = StandaloneDocker::query()
        ->where('server_id', 0)
        ->where('network', 'coolify')
        ->latest('id')
        ->first();
    $githubApp = GithubApp::query()->find(0);

    expect($team)
        ->not->toBeNull()
        ->and($team->name)->toBe('根团队')
        ->and($team->description)->toBe('系统默认根团队');

    expect($destination)
        ->not->toBeNull()
        ->and($destination->getRawOriginal('name'))->toBe('本地 Standalone Docker');

    expect($githubApp)
        ->not->toBeNull()
        ->and($githubApp->name)->toBe('公开 GitHub');
});

it('seeds localized development railpack prerequisites when created on demand', function () {
    config()->set('app.env', 'local');

    $this->seed(DevelopmentRailpackExamplesSeeder::class);

    $team = Team::query()->find(0);
    $destination = StandaloneDocker::query()
        ->where('server_id', 0)
        ->where('network', 'coolify')
        ->latest('id')
        ->first();
    $githubApp = GithubApp::query()->find(0);

    expect($team)
        ->not->toBeNull()
        ->and($team->name)->toBe('根团队')
        ->and($team->description)->toBe('系统默认根团队');

    expect($destination)
        ->not->toBeNull()
        ->and($destination->getRawOriginal('name'))->toBe('本地 Standalone Docker');

    expect($githubApp)
        ->not->toBeNull()
        ->and($githubApp->name)->toBe('公开 GitHub');
});

it('seeds localized root user defaults from environment variables', function () {
    putenv('ROOT_USER_EMAIL=root@laravel.com');
    putenv('ROOT_USER_PASSWORD=V3ry-Safe!9472');
    putenv('ROOT_USERNAME');
    $_ENV['ROOT_USER_EMAIL'] = 'root@laravel.com';
    $_SERVER['ROOT_USER_EMAIL'] = 'root@laravel.com';
    $_ENV['ROOT_USER_PASSWORD'] = 'V3ry-Safe!9472';
    $_SERVER['ROOT_USER_PASSWORD'] = 'V3ry-Safe!9472';
    unset($_ENV['ROOT_USERNAME'], $_SERVER['ROOT_USERNAME']);

    $this->seed(RootUserSeeder::class);

    expect(User::query()->find(0))
        ->not->toBeNull()
        ->and(User::query()->find(0)?->name)->toBe('根用户');

    expect(InstanceSettings::query()->find(0))
        ->not->toBeNull()
        ->and((bool) InstanceSettings::query()->find(0)?->is_registration_enabled)->toBeFalse();
});

it('seeds localized production public source examples in cloud mode', function () {
    config()->set('constants.coolify.self_hosted', false);

    $this->seed([
        UserSeeder::class,
        TeamSeeder::class,
        ProductionSeeder::class,
    ]);

    expect(GithubApp::query()->find(0))
        ->not->toBeNull()
        ->and(GithubApp::query()->find(0)?->name)->toBe('公开 GitHub');

    expect(GitlabApp::query()->find(0))
        ->not->toBeNull()
        ->and(GitlabApp::query()->find(0)?->name)->toBe('公开 GitLab');
});
