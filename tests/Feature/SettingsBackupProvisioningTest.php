<?php

use App\Livewire\SettingsBackup;
use App\Models\ScheduledDatabaseBackup;
use App\Models\StandaloneDocker;
use App\Models\StandalonePostgresql;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

test('settings backup provisioning reuses existing coolify database resource', function () {
    $database = StandalonePostgresql::create([
        'id' => 0,
        'name' => 'coolify-db',
        'description' => 'Old description',
        'postgres_user' => 'old-user',
        'postgres_password' => 'old-password',
        'postgres_db' => 'old-db',
        'status' => 'exited',
        'destination_type' => StandaloneDocker::class,
        'destination_id' => 0,
    ]);

    $component = new SettingsBackup;
    $method = new ReflectionMethod(SettingsBackup::class, 'syncCoolifyDatabaseFromEnv');
    $method->setAccessible(true);

    /** @var StandalonePostgresql $synced */
    $synced = $method->invoke($component, [
        'POSTGRES_USER' => 'coolify',
        'POSTGRES_PASSWORD' => 'password',
        'POSTGRES_DB' => 'coolify',
    ]);

    expect(StandalonePostgresql::query()->where('name', 'coolify-db')->count())->toBe(1);
    expect($synced->id)->toBe($database->id);
    expect($synced->postgres_user)->toBe('coolify');
    expect($synced->postgres_db)->toBe('coolify');
    expect($synced->status)->toStartWith('running');
});

test('settings backup provisioning creates scheduled backup only once for coolify database', function () {
    $database = StandalonePostgresql::create([
        'id' => 0,
        'name' => 'coolify-db',
        'description' => 'Coolify database',
        'postgres_user' => 'coolify',
        'postgres_password' => 'password',
        'postgres_db' => 'coolify',
        'status' => 'running',
        'destination_type' => StandaloneDocker::class,
        'destination_id' => 0,
    ]);

    $component = new SettingsBackup;
    $method = new ReflectionMethod(SettingsBackup::class, 'ensureCoolifyDatabaseBackup');
    $method->setAccessible(true);

    /** @var ScheduledDatabaseBackup $first */
    $first = $method->invoke($component, $database, 0);
    /** @var ScheduledDatabaseBackup $second */
    $second = $method->invoke($component, $database, 0);

    expect(ScheduledDatabaseBackup::query()
        ->where('database_id', $database->id)
        ->where('database_type', StandalonePostgresql::class)
        ->count())->toBe(1);
    expect($second->id)->toBe($first->id);
});

test('settings backup provisioning accepts docker envs as collection', function () {
    $component = new SettingsBackup;
    $method = new ReflectionMethod(SettingsBackup::class, 'syncCoolifyDatabaseFromEnv');
    $method->setAccessible(true);

    /** @var StandalonePostgresql $database */
    $database = $method->invoke($component, new Collection([
        'POSTGRES_USER' => 'coolify',
        'POSTGRES_PASSWORD' => 'password',
        'POSTGRES_DB' => 'coolify',
    ]));

    expect($database->name)->toBe('coolify-db');
    expect($database->postgres_user)->toBe('coolify');
    expect($database->postgres_db)->toBe('coolify');
});
