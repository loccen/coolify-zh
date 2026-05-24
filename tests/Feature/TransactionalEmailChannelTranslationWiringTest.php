<?php

use Illuminate\Support\Facades\App;

it('wires transactional email missing-settings exceptions through mail translations', function () {
    $basePath = dirname(__DIR__, 2);
    $channel = file_get_contents($basePath.'/app/Notifications/Channels/TransactionalEmailChannel.php');
    $resetPassword = file_get_contents($basePath.'/app/Notifications/TransactionalEmails/ResetPassword.php');
    $enTranslations = require $basePath.'/lang/en/mail.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/mail.php';

    expect($channel)
        ->toContain("trans('mail.channels.transactional_email.no_email_settings_found')")
        ->and($resetPassword)
        ->toContain("trans('mail.channels.transactional_email.no_email_settings_found')");

    expect($enTranslations['channels']['transactional_email']['no_email_settings_found'])->toBe('No email settings found.')
        ->and($zhTranslations['channels']['transactional_email']['no_email_settings_found'])->toBe('未找到邮件设置。');
});

it('resolves transactional email missing-settings translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('mail.channels.transactional_email.no_email_settings_found'))->toBe('No email settings found.');

    App::setLocale('zh_CN');

    expect(trans('mail.channels.transactional_email.no_email_settings_found'))->toBe('未找到邮件设置。');
});
