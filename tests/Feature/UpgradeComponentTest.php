<?php

use App\Livewire\Upgrade;
use App\Models\InstanceSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function setUpgradeLocale(): void
{
    config([
        'app.env' => 'production',
        'app.locale' => 'en',
        'app.fallback_locale' => 'en',
    ]);

    app()->setLocale('en');
}

function createRootInstanceSettings(bool $newVersionAvailable): InstanceSettings
{
    $settings = new InstanceSettings;
    $settings->forceFill([
        'id' => 0,
        'new_version_available' => $newVersionAvailable,
    ])->save();

    return $settings;
}

it('initializes latest version during mount from cached versions data', function () {
    setUpgradeLocale();

    config(['constants.coolify.version' => '4.0.0-beta.998']);
    createRootInstanceSettings(true);

    Cache::shouldReceive('remember')
        ->once()
        ->with('coolify:versions:all', 3600, Mockery::type(Closure::class))
        ->andReturn([
            'coolify' => [
                'v4' => [
                    'version' => '4.0.0-beta.999',
                ],
            ],
        ]);

    Livewire::test(Upgrade::class)
        ->assertSet('currentVersion', '4.0.0-beta.998')
        ->assertSet('latestVersion', '4.0.0-beta.999')
        ->assertSet('isUpgradeAvailable', true)
        ->assertSee('4.0.0-beta.998')
        ->assertSee('4.0.0-beta.999')
        ->assertSee('Upgrade')
        ->assertSee('Upgrade Available')
        ->assertSee('Cancel')
        ->assertSee('Upgrade Now')
        ->assertSee('Any deployments running during the update process will fail.');
});

it('falls back to 0.0.0 during mount when cached versions data is unavailable', function () {
    setUpgradeLocale();
    createRootInstanceSettings(false);

    Cache::shouldReceive('remember')
        ->once()
        ->with('coolify:versions:all', 3600, Mockery::type(Closure::class))
        ->andReturn(null);

    Livewire::test(Upgrade::class)
        ->assertSet('latestVersion', '0.0.0');
});

it('clears stale upgrade availability when current version already matches latest version', function () {
    setUpgradeLocale();
    config(['constants.coolify.version' => '4.0.0-beta.999']);
    $settings = createRootInstanceSettings(true);

    Cache::shouldReceive('remember')
        ->once()
        ->with('coolify:versions:all', 3600, Mockery::type(Closure::class))
        ->andReturn([
            'coolify' => [
                'v4' => [
                    'version' => '4.0.0-beta.999',
                ],
            ],
        ]);

    Livewire::test(Upgrade::class)
        ->assertSet('latestVersion', '4.0.0-beta.999')
        ->assertSet('isUpgradeAvailable', false);

    expect((bool) $settings->refresh()->new_version_available)->toBeFalse();
});

it('clears stale upgrade availability when current version is newer than cached latest version', function () {
    setUpgradeLocale();
    config(['constants.coolify.version' => '4.0.0-beta.1000']);
    $settings = createRootInstanceSettings(true);

    Cache::shouldReceive('remember')
        ->once()
        ->with('coolify:versions:all', 3600, Mockery::type(Closure::class))
        ->andReturn([
            'coolify' => [
                'v4' => [
                    'version' => '4.0.0-beta.999',
                ],
            ],
        ]);

    Livewire::test(Upgrade::class)
        ->assertSet('latestVersion', '4.0.0-beta.999')
        ->assertSet('isUpgradeAvailable', false);

    expect((bool) $settings->refresh()->new_version_available)->toBeFalse();
});
