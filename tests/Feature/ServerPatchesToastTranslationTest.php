<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server security patches toast dispatches', function () {
    $component = file_get_contents(app_path('Livewire/Server/Security/Patches.php'));

    expect($component)
        ->toContain("__('server.toasts.check_for_updates_first')")
        ->toContain("__('server.toasts.test_email_development_only')")
        ->toContain("__('server.toasts.test_email_sent_successfully')")
        ->toContain("__('server.toasts.test_email_failed', ['error' => \$e->getMessage()])");
});

it('resolves server security patches toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.check_for_updates_first'))
        ->toBe('请先执行“检查更新”。')
        ->and(__('server.toasts.test_email_development_only'))
        ->toBe('测试邮件功能仅在开发模式下可用。')
        ->and(__('server.toasts.test_email_sent_successfully'))
        ->toBe('测试邮件发送成功！请检查你的邮箱。')
        ->and(__('server.toasts.test_email_failed', ['error' => 'boom']))
        ->toBe('发送测试邮件失败：boom');
});
