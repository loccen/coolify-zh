<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in the server terminal access component', function () {
    $component = file_get_contents(app_path('Livewire/Server/Security/TerminalAccess.php'));

    expect($component)
        ->toContain("__('auth.failed.password')")
        ->toContain("__('server.toasts.terminal_access_enabled')")
        ->toContain("__('server.toasts.terminal_access_disabled')");
});

it('resolves server terminal access translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('auth.failed.password'))
        ->toBe('The provided password is incorrect.')
        ->and(__('server.toasts.terminal_access_enabled'))
        ->toBe('Terminal access is enabled.')
        ->and(__('server.toasts.terminal_access_disabled'))
        ->toBe('Terminal access is disabled.');

    App::setLocale('zh_CN');

    expect(__('auth.failed.password'))
        ->toBe('密码不正确。')
        ->and(__('server.toasts.terminal_access_enabled'))
        ->toBe('终端访问已启用。')
        ->and(__('server.toasts.terminal_access_disabled'))
        ->toBe('终端访问已禁用。');
});
