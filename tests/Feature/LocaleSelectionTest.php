<?php

use App\Models\InstanceSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::forceCreate(['id' => 0]);
    User::factory()->create(['email' => 'locale@example.com']);
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

it('prefers the locale cookie over the accept language header', function () {
    $response = $this
        ->withCookie('coolify_locale', 'en')
        ->withHeaders(['Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8'])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('lang="en"', false);
});

it('uses the accept language header when the cookie is missing', function () {
    $response = $this
        ->withHeaders(['Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8'])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('lang="zh-CN"', false);
});

it('falls back to zh_CN when no supported locale preference is provided', function () {
    $response = $this
        ->withHeaders(['Accept-Language' => 'fr-FR,fr;q=0.9'])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('lang="zh-CN"', false);
});

it('rejects unsupported locales during switching', function () {
    $response = $this->post(route('locale.switch'), [
        'locale' => 'fr',
        'redirect_to' => '/login',
    ]);

    $response->assertNotFound();
    expect(collect($response->headers->getCookies())->contains(
        fn ($cookie) => $cookie->getName() === 'coolify_locale'
    ))->toBeFalse();
});

it('stores the selected locale in a long-lived cookie and redirects back', function () {
    $response = $this->post(route('locale.switch'), [
        'locale' => 'en',
        'redirect_to' => '/login?tab=security',
    ]);

    $response->assertRedirect(url('/login?tab=security'));
    $response->assertCookie('coolify_locale', 'en');
});

it('rejects external bounce-back targets when switching locale', function () {
    $response = $this->post(route('locale.switch'), [
        'locale' => 'zh_CN',
        'redirect_to' => 'https://example.com/elsewhere',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertCookie('coolify_locale', 'zh_CN');
});
