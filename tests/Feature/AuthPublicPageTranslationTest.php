<?php

use App\Models\InstanceSettings;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::forceCreate([
        'id' => 0,
        'is_registration_enabled' => true,
    ]);

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

it('renders translated login copy in english when the locale cookie wins', function () {
    User::factory()->create(['email' => 'login@example.com']);

    $response = $this
        ->withCookie('coolify_locale', 'en')
        ->withHeaders(['Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8'])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('lang="en"', false);
    $response->assertSee("Don't have an account?");
    $response->assertSee('Forgot password?');
});

it('renders translated login copy in zh_CN from the accept-language header', function () {
    User::factory()->create(['email' => 'login@example.com']);

    $response = $this
        ->withHeaders(['Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8'])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('lang="zh-CN"', false);
    $response->assertSee('没有账号？');
    $response->assertSee('忘记密码？');
});

it('renders invitation page copy through translations for both locales', function () {
    $team = Team::factory()->create(['name' => '测试团队']);
    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'uuid' => 'translation-test-invitation',
        'email' => 'invitee@example.com',
        'role' => 'member',
        'link' => url('/invitations/translation-test-invitation'),
        'via' => 'link',
    ]);

    app()->setLocale('en');
    $enHtml = view('invitation.accept', [
        'invitation' => $invitation,
        'team' => $team,
        'alreadyMember' => false,
    ])->render();

    expect($enHtml)
        ->toContain('Team Invitation')
        ->toContain('Role:')
        ->toContain('Accept Invitation');

    app()->setLocale('zh_CN');
    $zhHtml = view('invitation.accept', [
        'invitation' => $invitation,
        'team' => $team,
        'alreadyMember' => false,
    ])->render();

    expect($zhHtml)
        ->toContain('团队邀请')
        ->toContain('角色：')
        ->toContain('接受邀请');
});
