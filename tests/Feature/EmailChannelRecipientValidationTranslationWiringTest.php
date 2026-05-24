<?php

use Illuminate\Support\Facades\App;

it('wires email recipient validation through mail translations', function () {
    $basePath = dirname(__DIR__, 2);
    $channel = file_get_contents($basePath.'/app/Notifications/Channels/EmailChannel.php');
    $enTranslations = require $basePath.'/lang/en/mail.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/mail.php';

    expect($channel)
        ->toContain("trans('mail.channels.email.no_email_recipients_found')");

    expect($enTranslations['channels']['email']['no_email_recipients_found'])
        ->toBe('No email recipients found.')
        ->and($zhTranslations['channels']['email']['no_email_recipients_found'])
        ->toBe('未找到邮件收件人。');
});

it('resolves email recipient validation translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('mail.channels.email.no_email_recipients_found'))
        ->toBe('No email recipients found.');

    App::setLocale('zh_CN');

    expect(trans('mail.channels.email.no_email_recipients_found'))
        ->toBe('未找到邮件收件人。');
});
