<?php

use Tests\TestCase;

uses(TestCase::class);

function worktreePath(string $path = ''): string
{
    $root = dirname(__DIR__, 2);

    return $path === '' ? $root : $root.'/'.$path;
}

test('zh CN translation catalogs contain T5B labels', function () {
    $settings = require worktreePath('lang/zh_CN/settings.php');
    $profile = require worktreePath('lang/zh_CN/profile.php');
    $team = require worktreePath('lang/zh_CN/team.php');

    expect(data_get($settings, 'title'))->toBe('设置')
        ->and(data_get($settings, 'transactional_email'))->toBe('事务邮件')
        ->and(data_get($settings, 'scheduled_jobs_page.heading'))->toBe('计划任务异常')
        ->and(data_get($profile, 'title'))->toBe('个人资料')
        ->and(data_get($profile, 'change_email'))->toBe('更改邮箱')
        ->and(data_get($team, 'title'))->toBe('团队')
        ->and(data_get($team, 'invite_new_member'))->toBe('邀请新成员');
});

test('settings views use explicit translation lookups', function () {
    $navbar = file_get_contents(worktreePath('resources/views/components/settings/navbar.blade.php'));
    $index = file_get_contents(worktreePath('resources/views/livewire/settings/index.blade.php'));
    $advanced = file_get_contents(worktreePath('resources/views/livewire/settings/advanced.blade.php'));
    $updates = file_get_contents(worktreePath('resources/views/livewire/settings/updates.blade.php'));
    $scheduledJobs = file_get_contents(worktreePath('resources/views/livewire/settings/scheduled-jobs.blade.php'));

    expect($navbar)->toContain("__('settings.title')")
        ->and($navbar)->toContain("__('settings.transactional_email')")
        ->and($index)->toContain("__('settings.index.timezone')")
        ->and($advanced)->toContain("__('settings.advanced_page.mcp_server')")
        ->and($updates)->toContain("__('settings.updates_page.check_manually')")
        ->and($scheduledJobs)->toContain("__('settings.scheduled_jobs_page.heading')")
        ->and($scheduledJobs)->toContain("__('settings.scheduled_jobs_page.tabs.executions')");
});

test('profile and team views use explicit translation lookups', function () {
    $profile = file_get_contents(worktreePath('resources/views/livewire/profile/index.blade.php'));
    $teamNavbar = file_get_contents(worktreePath('resources/views/components/team/navbar.blade.php'));
    $teamIndex = file_get_contents(worktreePath('resources/views/livewire/team/index.blade.php'));
    $teamMembers = file_get_contents(worktreePath('resources/views/livewire/team/member/index.blade.php'));
    $teamAdmin = file_get_contents(worktreePath('resources/views/livewire/team/admin-view.blade.php'));
    $inviteLink = file_get_contents(worktreePath('resources/views/livewire/team/invite-link.blade.php'));
    $invitations = file_get_contents(worktreePath('resources/views/livewire/team/invitations.blade.php'));
    $switchTeam = file_get_contents(worktreePath('resources/views/livewire/switch-team.blade.php'));
    $deleteTeam = file_get_contents(worktreePath('resources/views/livewire/navbar-delete-team.blade.php'));

    expect($profile)->toContain("__('profile.title')")
        ->and($profile)->toContain("__('profile.change_email')")
        ->and($profile)->toContain("__('profile.two_factor_authentication')")
        ->and($teamNavbar)->toContain("__('team.title')")
        ->and($teamIndex)->toContain("__('team.danger_zone')")
        ->and($teamMembers)->toContain("__('team.invite_new_member')")
        ->and($teamAdmin)->toContain("__('team.admin_view')")
        ->and($inviteLink)->toContain("__('team.generate_invitation_link')")
        ->and($invitations)->toContain("__('team.pending_invitations')")
        ->and($switchTeam)->toContain("__('navigation.team_switch')")
        ->and($deleteTeam)->toContain("__('team.delete_team_modal_title')");
});
