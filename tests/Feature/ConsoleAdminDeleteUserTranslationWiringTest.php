<?php

use App\Console\Commands\AdminDeleteUser;
use Illuminate\Support\Facades\App;

it('wires admin delete user console translations through console language files', function () {
    $command = file_get_contents(app_path('Console/Commands/AdminDeleteUser.php'));

    expect($command)
        ->toContain("trans('console.admin_delete_user.confirm.phase_2_to_3')")
        ->toContain("trans('console.admin_delete_user.confirm.phase_3_to_4')")
        ->toContain("trans('console.admin_delete_user.confirm.phase_4_to_5')")
        ->toContain("trans('console.admin_delete_user.confirm.phase_5_commit')")
        ->toContain("trans('console.admin_delete_user.confirm.phase_6_stripe')")
        ->toContain("trans('console.admin_delete_user.commit.critical_decision_point')")
        ->toContain("trans('console.admin_delete_user.commit.next_step')")
        ->toContain("trans('console.admin_delete_user.commit.cannot_be_undone')")
        ->toContain("trans('console.admin_delete_user.commit.permanent_delete_warning')")
        ->toContain("trans('console.admin_delete_user.overview.permanent_warning')")
        ->toContain("trans('console.admin_delete_user.overview.continue_prompt')")
        ->toContain("trans('console.admin_delete_user.stripe.manual_action_required')")
        ->toContain("trans('console.admin_delete_user.recovery.title')");
});

it('resolves admin delete user console translations in english', function () {
    App::setLocale('en');

    expect(trans('console.admin_delete_user.confirm.phase_2_to_3'))
        ->toBe('Phase 2 completed. Continue to Phase 3 (Delete Servers)?')
        ->and(trans('console.admin_delete_user.confirm.phase_5_commit'))
        ->toBe('Phase 5 completed. Commit database changes? (THIS IS PERMANENT)')
        ->and(trans('console.admin_delete_user.commit.next_step'))
        ->toBe('Next step: COMMIT database changes (PERMANENT and IRREVERSIBLE)')
        ->and(trans('console.admin_delete_user.commit.cannot_be_undone'))
        ->toBe('⚠️  THIS ACTION CANNOT BE UNDONE')
        ->and(trans('console.admin_delete_user.recovery.title'))
        ->toBe('RECOVERY STEPS')
        ->and((new AdminDeleteUser)->getDescription())
        ->toBe('Delete a user with comprehensive resource cleanup and phase-by-phase confirmation (works on cloud and self-hosted)');
});

it('resolves admin delete user console translations in chinese', function () {
    App::setLocale('zh_CN');

    expect(trans('console.admin_delete_user.confirm.phase_2_to_3'))
        ->toBe('第 2 阶段已完成。继续进入第 3 阶段（删除服务器）吗？')
        ->and(trans('console.admin_delete_user.confirm.phase_5_commit'))
        ->toBe('第 5 阶段已完成。要提交数据库变更吗？（这是永久操作）')
        ->and(trans('console.admin_delete_user.commit.next_step'))
        ->toBe('下一步：提交数据库变更（永久且不可逆）')
        ->and(trans('console.admin_delete_user.commit.cannot_be_undone'))
        ->toBe('⚠️  此操作无法撤销')
        ->and(trans('console.admin_delete_user.recovery.title'))
        ->toBe('恢复步骤')
        ->and((new AdminDeleteUser)->getDescription())
        ->toBe('Delete a user with comprehensive resource cleanup and phase-by-phase confirmation (works on cloud and self-hosted)');
});
