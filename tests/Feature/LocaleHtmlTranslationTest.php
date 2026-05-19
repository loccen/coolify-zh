<?php

use App\Models\InstanceSettings;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::forceCreate(['id' => 0]);
    config()->set('cache.default', 'array');
    config()->set('cache.stores.array', [
        'driver' => 'array',
        'serialize' => false,
    ]);
    config()->set('app.maintenance.store', 'array');

    File::ensureDirectoryExists(public_path('build/assets'));
    File::put(public_path('build/assets/app.js'), 'console.log("test");');
    File::put(public_path('build/assets/app.css'), 'body{}');
    File::put(public_path('build/manifest.json'), json_encode([
        'resources/js/app.js' => [
            'file' => 'assets/app.js',
            'src' => 'resources/js/app.js',
            'isEntry' => true,
        ],
        'resources/css/app.css' => [
            'file' => 'assets/app.css',
            'src' => 'resources/css/app.css',
            'isEntry' => true,
        ],
    ], JSON_THROW_ON_ERROR));
});

it('renders the login page in chinese when the locale cookie is set', function () {
    $response = $this->withCookie('coolify_locale', 'zh-cn')->followingRedirects()->get('/login');

    $response->assertOk();
    $response->assertSee('lang="zh-cn"', false);
});

it('renders the login page in english when the locale cookie is set', function () {
    $response = $this->withCookie('coolify_locale', 'en')->followingRedirects()->get('/login');

    $response->assertOk();
    $response->assertSee('lang="en"', false);
});

it('renders the dashboard in chinese for authenticated users', function () {
    $user = User::factory()->create([
        'id' => 0,
        'name' => 'Root User',
        'email' => 'test@example.com',
    ]);

    Team::findOrFail(0)->update(['show_boarding' => false]);

    $response = $this
        ->actingAs($user)
        ->withCookie('coolify_locale', 'zh-cn')
        ->get('/');

    $response->assertOk();
    $response->assertSee('仪表盘', false);
    $response->assertSee('服务器', false);
    $response->assertSee('设置', false);
});

it('stores the selected locale in a cookie and redirects back', function () {
    $response = $this
        ->from('/login')
        ->get(route('locale.switch', ['locale' => 'en']));

    $response->assertRedirect('/login');
    $response->assertCookie('coolify_locale', 'en');
});
