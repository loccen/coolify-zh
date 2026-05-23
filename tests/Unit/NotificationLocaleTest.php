<?php

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\' => dirname(__DIR__, 2).'/app/',
        'Tests\\' => dirname(__DIR__, 1).'/',
    ];

    foreach ($prefixes as $prefix => $basePath) {
        if (! str_starts_with($class, $prefix)) {
            continue;
        }

        $relativePath = str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
        $path = $basePath.$relativePath;

        if (is_file($path)) {
            require_once $path;
        }

        return;
    }
}, prepend: true);

use App\Notifications\TransactionalEmails\ResetPassword;
use App\Notifications\TransactionalEmails\Test as TransactionalEmailTest;
use App\Support\UserVisibleLocale;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

function bindRequestWithRoute(): void
{
    $request = Request::create('/test-locale', 'GET');
    $request->setRouteResolver(fn () => new Route('GET', '/test-locale', fn () => null));

    app()->instance('request', $request);
}

it('captures the current request locale for queued transactional emails', function () {
    bindRequestWithRoute();
    App::setLocale('zh_CN');

    $notification = new TransactionalEmailTest('test@example.com');

    expect($notification->locale)->toBe('zh_CN');

});

it('falls back to english for queued transactional emails outside a request context', function () {
    app()->instance('request', Request::create('/'));
    App::setLocale('zh_CN');

    $notification = new TransactionalEmailTest('test@example.com');

    expect($notification->locale)->toBe('en');

});

it('falls back to english for non-queued transactional mail notifications outside a request context', function () {
    Schema::create('instance_settings', function (Blueprint $table) {
        $table->integer('id')->primary();
    });
    DB::table('instance_settings')->insert(['id' => 0]);

    app()->instance('request', Request::create('/'));
    App::setLocale('zh_CN');

    $notification = new ResetPassword('token-value');

    expect($notification->locale)->toBe('en');

});

it('renders transactional email subjects with the current request locale', function () {
    bindRequestWithRoute();
    App::setLocale('zh_CN');

    $notification = new TransactionalEmailTest('test@example.com');

    expect($notification->toMail()->subject)->toBe('Coolify: 测试邮件');
});

it('renders transactional email bodies with the captured locale', function () {
    bindRequestWithRoute();
    App::setLocale('zh_CN');

    $notification = new TransactionalEmailTest('test@example.com');
    App::setLocale('en');

    $rendered = UserVisibleLocale::withLocale(
        $notification->locale,
        fn () => (string) $notification->toMail()->render()
    );

    expect($rendered)
        ->toContain('你好，')
        ->toContain('如果你收到了这封邮件，说明邮件设置正确。')
        ->toContain('此致')
        ->toContain('联系支持');
});
