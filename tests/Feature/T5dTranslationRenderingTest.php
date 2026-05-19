<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->team = Team::create(['name' => '翻译团队']);
    $this->user = User::create([
        'name' => '翻译用户',
        'email' => 'translator@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->team->members()->attach($this->user->id, ['role' => 'owner']);

    $this->actingAs($this->user);
    session(['currentTeam' => $this->team]);

    config([
        'app.locale' => 'zh_CN',
        'app.fallback_locale' => 'zh_CN',
        'constants.coolify.self_hosted' => true,
    ]);
    app()->setLocale('zh_CN');

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag);
    view()->share('errors', $errors);
});

it('renders translated github source permission copy', function () {
    auth()->logout();

    $html = view('livewire.source.github.create')->render();
    $catalog = json_decode(file_get_contents(__DIR__.'/../../lang/zh_CN.json'), true, 512, JSON_THROW_ON_ERROR);
    expect($html)
        ->not->toBeEmpty()
        ->and($catalog['You do not have permission to create new GitHub Apps. Please contact your team administrator for access.'])
        ->toBe('你没有权限创建新的 GitHub Apps，请联系团队管理员开通权限。');
});

it('renders translated slack notification settings copy', function () {
    $html = view('livewire.notifications.slack', [
        'settings' => $this->team->fresh()->slackNotificationSettings,
        'slackEnabled' => false,
    ])->render();
    $catalog = json_decode(file_get_contents(__DIR__.'/../../lang/zh_CN.json'), true, 512, JSON_THROW_ON_ERROR);
    expect($html)
        ->not->toBeEmpty()
        ->and($catalog['Notification Settings'])
        ->toBe('通知设置')
        ->and($catalog['Select events for which you would like to receive Slack notifications.'])
        ->toBe('选择你希望接收 Slack 通知的事件。');
});
