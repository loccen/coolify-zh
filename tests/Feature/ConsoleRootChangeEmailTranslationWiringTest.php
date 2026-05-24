<?php

use App\Console\Commands\RootChangeEmail;
use Illuminate\Support\Facades\App;

it('wires root change email command strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/RootChangeEmail.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.root_change_email.description'")
        ->toContain("trans('console.root_change_email.about_to_change'")
        ->toContain("trans('console.root_change_email.email_prompt'")
        ->toContain("trans('console.root_change_email.updating'")
        ->toContain("trans('console.root_change_email.updated_successfully'")
        ->toContain("trans('console.root_change_email.failed_to_update'");

    expect($enTranslations['root_change_email']['description'])->toBe('Change Root Email')
        ->and($enTranslations['root_change_email']['about_to_change'])->toBe("You are about to change the root user's email.")
        ->and($enTranslations['root_change_email']['email_prompt'])->toBe('Give me a new email for root user')
        ->and($enTranslations['root_change_email']['updating'])->toBe('Updating root email...')
        ->and($enTranslations['root_change_email']['updated_successfully'])->toBe("Root user's email updated successfully.")
        ->and($enTranslations['root_change_email']['failed_to_update'])->toBe("Failed to update root user's email.");

    expect($zhTranslations['root_change_email']['description'])->toBe('更改 Root 邮箱')
        ->and($zhTranslations['root_change_email']['about_to_change'])->toBe('即将更改 Root 用户的邮箱。')
        ->and($zhTranslations['root_change_email']['email_prompt'])->toBe('请输入 Root 用户的新邮箱：')
        ->and($zhTranslations['root_change_email']['updating'])->toBe('正在更新 Root 邮箱...')
        ->and($zhTranslations['root_change_email']['updated_successfully'])->toBe('Root 用户的邮箱已成功更新。')
        ->and($zhTranslations['root_change_email']['failed_to_update'])->toBe('更新 Root 用户邮箱失败。');
});

it('resolves root change email translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.root_change_email.description'))->toBe('Change Root Email')
        ->and(trans('console.root_change_email.about_to_change'))->toBe("You are about to change the root user's email.")
        ->and(trans('console.root_change_email.email_prompt'))->toBe('Give me a new email for root user')
        ->and(trans('console.root_change_email.updating'))->toBe('Updating root email...')
        ->and(trans('console.root_change_email.updated_successfully'))->toBe("Root user's email updated successfully.")
        ->and(trans('console.root_change_email.failed_to_update'))->toBe("Failed to update root user's email.")
        ->and((new RootChangeEmail)->getDescription())->toBe('Change Root Email');

    App::setLocale('zh_CN');

    expect(trans('console.root_change_email.description'))->toBe('更改 Root 邮箱')
        ->and(trans('console.root_change_email.about_to_change'))->toBe('即将更改 Root 用户的邮箱。')
        ->and(trans('console.root_change_email.email_prompt'))->toBe('请输入 Root 用户的新邮箱：')
        ->and(trans('console.root_change_email.updating'))->toBe('正在更新 Root 邮箱...')
        ->and(trans('console.root_change_email.updated_successfully'))->toBe('Root 用户的邮箱已成功更新。')
        ->and(trans('console.root_change_email.failed_to_update'))->toBe('更新 Root 用户邮箱失败。')
        ->and((new RootChangeEmail)->getDescription())->toBe('更改 Root 邮箱');
});
