<?php

use App\Console\Commands\RootResetPassword;
use Illuminate\Support\Facades\App;

it('wires root reset password command strings through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/RootResetPassword.php'));

    expect($command)
        ->toContain("trans('console.root_reset_password.description'")
        ->toContain("trans('console.root_reset_password.about_to_reset'")
        ->toContain("trans('console.root_reset_password.password_prompt'")
        ->toContain("trans('console.root_reset_password.password_again_prompt'")
        ->toContain("trans('console.root_reset_password.passwords_do_not_match'")
        ->toContain("trans('console.root_reset_password.updating'")
        ->toContain("trans('console.root_reset_password.root_user_not_found'")
        ->toContain("trans('console.root_reset_password.updated_successfully'")
        ->toContain("trans('console.root_reset_password.failed_to_update'");
});

it('resolves root reset password command translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.root_reset_password.description'))->toBe('Reset Root Password')
        ->and(trans('console.root_reset_password.password_prompt'))->toBe('Enter a new password for the Root user:')
        ->and(trans('console.root_reset_password.password_again_prompt'))->toBe('Enter the new password again:')
        ->and((new RootResetPassword)->getDescription())->toBe('Reset Root Password');

    App::setLocale('zh_CN');

    expect(trans('console.root_reset_password.description'))->toBe('重置 Root 密码')
        ->and(trans('console.root_reset_password.about_to_reset'))->toBe('即将重置 Root 用户的密码。')
        ->and(trans('console.root_reset_password.password_prompt'))->toBe('请输入 Root 用户的新密码：')
        ->and(trans('console.root_reset_password.password_again_prompt'))->toBe('请再次输入新密码：')
        ->and(trans('console.root_reset_password.passwords_do_not_match'))->toBe('两次输入的密码不一致。')
        ->and(trans('console.root_reset_password.updating'))->toBe('正在更新 Root 密码...')
        ->and(trans('console.root_reset_password.root_user_not_found'))->toBe('未找到 Root 用户。')
        ->and(trans('console.root_reset_password.updated_successfully'))->toBe('Root 密码已更新。')
        ->and(trans('console.root_reset_password.failed_to_update'))->toBe('更新 Root 密码失败。')
        ->and((new RootResetPassword)->getDescription())->toBe('重置 Root 密码');
});
