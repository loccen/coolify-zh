<?php

use App\Console\Commands\Emails;
use App\Livewire\Help;
use App\Livewire\SettingsDropdown;
use App\Models\InstanceSettings;
use App\Models\User;
use App\Services\ChangelogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::create(['id' => 0]);
    config([
        'constants.coolify.self_hosted' => true,
        'constants.coolify.version' => '4.0.0',
    ]);

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag);
    view()->share('errors', $errors);
});

function setEnLocale(): void
{
    config([
        'app.locale' => 'en',
        'app.fallback_locale' => 'en',
    ]);

    app()->setLocale('en');
}

function setZhLocale(): void
{
    config([
        'app.locale' => 'zh_CN',
        'app.fallback_locale' => 'en',
    ]);

    App::setLocale('zh_CN');
}

function fakeChangelogService(Collection $entries, int $unreadCount): ChangelogService
{
    return new class($entries, $unreadCount) extends ChangelogService
    {
        public function __construct(
            private Collection $entries,
            private int $unreadCount,
        ) {}

        public function getEntriesForUser(User $user): Collection
        {
            return $this->entries;
        }

        public function getUnreadCountForUser(User $user): int
        {
            return $this->unreadCount;
        }

        public function markAsReadForUser(string $version, User $user): void {}

        public function markAllAsReadForUser(User $user): void {}
    };
}

it('renders settings dropdown translations in component output', function () {
    setEnLocale();

    $user = User::factory()->create(['name' => '设置用户']);
    $team = $user->teams()->firstOrFail();

    $this->actingAs($user);
    session(['currentTeam' => ['id' => $team->id]]);

    app()->instance(ChangelogService::class, fakeChangelogService(collect([
        (object) [
            'tag_name' => 'v4.0.0',
            'title' => 'v4.0.0',
            'content' => 'test',
            'content_html' => '<p>test</p>',
            'published_at' => now()->toIso8601String(),
            'is_read' => false,
        ],
    ]), 1));

    Livewire::test(SettingsDropdown::class)
        ->assertSee('Language')
        ->assertSee('Appearance')
        ->call('openWhatsNewModal')
        ->assertSee('Changelog')
        ->assertSee('Stay up to date with the latest features and improvements.')
        ->assertSee('Current version:')
        ->assertSee('Search updates...')
        ->assertSee('CURRENT VERSION')
        ->assertSee('mark as read');
});

it('renders translated modal buttons and default confirm copy', function () {
    setEnLocale();

    $modalHtml = Blade::render('<x-modal modal-id="danger" :yes-or-no="true" />');
    $confirmHtml = Blade::render('<x-confirm-modal />');

    expect($modalHtml)
        ->toContain('Cancel')
        ->toContain('Continue')
        ->and($confirmHtml)
        ->toContain('Are you sure?')
        ->toContain('Confirm')
        ->toContain('Cancel');
});

it('renders translated navbar labels in component output', function () {
    setEnLocale();

    $user = User::factory()->create(['name' => '导航用户']);
    $team = $user->teams()->firstOrFail();

    $this->actingAs($user);
    session(['currentTeam' => ['id' => $team->id]]);

    app()->instance(ChangelogService::class, fakeChangelogService(collect(), 0));

    $html = Blade::render('<x-navbar />');

    expect($html)
        ->toContain('Dashboard')
        ->toContain('Projects')
        ->toContain('Servers')
        ->toContain('Sources')
        ->toContain('Destinations')
        ->toContain('Sponsor us');
});

it('renders translated password visibility labels in form components', function () {
    setEnLocale();

    $inputHtml = Blade::render('<x-forms.input type="password" id="secret" />');
    $textareaHtml = Blade::render('<x-forms.textarea type="password" id="secret" />');

    expect($inputHtml)
        ->toContain('Toggle password visibility')
        ->and($textareaHtml)
        ->toContain('Toggle password visibility');
});

it('keeps modal confirmation wiring safe for dynamic confirmation text and native input attributes', function () {
    $html = file_get_contents(resource_path('views/components/modal-confirmation.blade.php'));

    expect($html)
        ->toContain('textarea.innerHTML = @js($confirmationText);')
        ->toContain('placeholder="{{ __(\'Enter your password\') }}"')
        ->not->toContain('textarea.innerHTML = @js(__($confirmationText));')
        ->not->toContain(':placeholder="__(\'Enter your password\')"');
});

it('renders translated default toast title in component output', function () {
    setEnLocale();

    $html = Blade::render('<x-toast />');

    expect($html)->toContain('Default Toast Notification');
});

it('defines the frontend i18n payload for toast, logs, and terminal prompts', function () {
    $layout = file_get_contents(dirname(__DIR__, 2).'/resources/views/layouts/base.blade.php');

    expect($layout)
        ->toContain('window.coolifyI18n = Object.freeze({')
        ->toContain("__('toast.titles.success')")
        ->toContain('logsCopiedToClipboard')
        ->toContain('matchesSuffix')
        ->toContain("__('terminal.toasts.reconnecting')");
});

it('localizes the emails command description and terminal search copy', function () {
    setZhLocale();

    $globalSearch = file_get_contents(app_path('Livewire/GlobalSearch.php'));
    $command = app(Emails::class);

    expect($command->getDescription())
        ->toBe('发送测试邮件或正式邮件')
        ->and(__('terminal.navigation.access_server'))
        ->toBe('访问服务器终端')
        ->and($globalSearch)
        ->toContain("__('Terminal')")
        ->toContain("__('terminal.navigation.access_server')");
});

it('wires shell popup translations and renders help form copy', function () {
    setEnLocale();

    $layoutPopups = file_get_contents(resource_path('views/livewire/layout-popups.blade.php'));

    expect($layoutPopups)
        ->toContain("__('Love Coolify? Support our work.')")
        ->toContain("__('Maybe next time')")
        ->toContain("__('Acknowledge & Disable This Popup')")
        ->toContain("__('No notifications enabled.')")
        ->toContain("__('Accept and Close')");

    Livewire::test(Help::class)
        ->assertSee('Your feedback helps us to improve Coolify. Thank you! 💜')
        ->assertSee('Subject')
        ->assertSee('Help with...')
        ->assertSee('Description')
        ->assertSee('Having trouble with... Please provide as much information as possible.')
        ->assertSee('Send');
});
