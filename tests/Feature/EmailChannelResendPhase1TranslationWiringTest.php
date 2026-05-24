<?php

use Illuminate\Support\Facades\App;

it('wires resend ErrorException status messages through mail translations', function () {
    $basePath = dirname(__DIR__, 2);
    $channel = file_get_contents($basePath.'/app/Notifications/Channels/EmailChannel.php');
    $enTranslations = require $basePath.'/lang/en/mail.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/mail.php';

    expect($channel)
        ->toContain("trans('mail.channels.email.resend.invalid_api_key')")
        ->toContain("trans('mail.channels.email.resend.restricted_api_key')")
        ->toContain("trans('mail.channels.email.resend.rate_limit_exceeded')")
        ->toContain("trans('mail.channels.email.resend.validation_failed', ['message' => \$e->getErrorMessage()])")
        ->toContain("trans('mail.channels.email.resend.send_failed', ['message' => \$e->getErrorMessage()])");

    expect($enTranslations['channels']['email']['resend']['invalid_api_key'])
        ->toBe('Invalid Resend API key. Please verify your API key in the Resend dashboard and update it in settings.')
        ->and($enTranslations['channels']['email']['resend']['restricted_api_key'])
        ->toBe('Your Resend API key has restricted permissions. Please use an API key with Full Access permissions.')
        ->and($enTranslations['channels']['email']['resend']['rate_limit_exceeded'])
        ->toBe('Resend rate limit exceeded. Please try again in a few minutes.')
        ->and($enTranslations['channels']['email']['resend']['validation_failed'])
        ->toBe('Email validation failed: :message')
        ->and($enTranslations['channels']['email']['resend']['send_failed'])
        ->toBe('Failed to send email via Resend: :message')
        ->and($zhTranslations['channels']['email']['resend']['invalid_api_key'])
        ->toBe('Resend API Key 无效。请在 Resend 控制台检查 API Key，并在设置中更新。')
        ->and($zhTranslations['channels']['email']['resend']['restricted_api_key'])
        ->toBe('当前 Resend API Key 权限受限。请使用具有 Full Access 权限的 API Key。')
        ->and($zhTranslations['channels']['email']['resend']['rate_limit_exceeded'])
        ->toBe('已达到 Resend 的速率限制。请几分钟后再试。')
        ->and($zhTranslations['channels']['email']['resend']['validation_failed'])
        ->toBe('邮件校验失败：:message')
        ->and($zhTranslations['channels']['email']['resend']['send_failed'])
        ->toBe('通过 Resend 发送邮件失败：:message');
});

it('resolves resend ErrorException translations and placeholders in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('mail.channels.email.resend.invalid_api_key'))
        ->toBe('Invalid Resend API key. Please verify your API key in the Resend dashboard and update it in settings.')
        ->and(trans('mail.channels.email.resend.restricted_api_key'))
        ->toBe('Your Resend API key has restricted permissions. Please use an API key with Full Access permissions.')
        ->and(trans('mail.channels.email.resend.rate_limit_exceeded'))
        ->toBe('Resend rate limit exceeded. Please try again in a few minutes.')
        ->and(trans('mail.channels.email.resend.validation_failed', ['message' => 'Invalid email format.']))
        ->toBe('Email validation failed: Invalid email format.')
        ->and(trans('mail.channels.email.resend.send_failed', ['message' => 'Internal server error.']))
        ->toBe('Failed to send email via Resend: Internal server error.');

    App::setLocale('zh_CN');

    expect(trans('mail.channels.email.resend.invalid_api_key'))
        ->toBe('Resend API Key 无效。请在 Resend 控制台检查 API Key，并在设置中更新。')
        ->and(trans('mail.channels.email.resend.restricted_api_key'))
        ->toBe('当前 Resend API Key 权限受限。请使用具有 Full Access 权限的 API Key。')
        ->and(trans('mail.channels.email.resend.rate_limit_exceeded'))
        ->toBe('已达到 Resend 的速率限制。请几分钟后再试。')
        ->and(trans('mail.channels.email.resend.validation_failed', ['message' => 'Invalid email format.']))
        ->toBe('邮件校验失败：Invalid email format.')
        ->and(trans('mail.channels.email.resend.send_failed', ['message' => 'Internal server error.']))
        ->toBe('通过 Resend 发送邮件失败：Internal server error.');
});
