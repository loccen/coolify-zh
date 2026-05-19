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
