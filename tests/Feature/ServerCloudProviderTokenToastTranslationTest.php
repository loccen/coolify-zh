<?php

use Illuminate\Support\Facades\App;

it('uses server toast translation keys for cloud provider token actions', function () {
    $show = file_get_contents(app_path('Livewire/Server/CloudProviderToken/Show.php'));

    expect($show)
        ->toContain("__('server.toasts.hetzner_token_unauthorized')")
        ->toContain("__('server.toasts.hetzner_token_updated')")
        ->toContain("__('server.toasts.hetzner_token_missing')")
        ->toContain("__('server.toasts.hetzner_token_valid')")
        ->toContain("__('server.toasts.hetzner_token_invalid_permissions')")
        ->toContain("__('server.toasts.hetzner_token_cannot_access_server')")
        ->toContain("__('server.toasts.hetzner_token_validation_failed'");
});

it('resolves cloud provider token toast translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(__('server.toasts.hetzner_token_unauthorized'))
        ->toBe('You are not allowed to use this token.')
        ->and(__('server.toasts.hetzner_token_updated'))
        ->toBe('Hetzner token updated successfully.')
        ->and(__('server.toasts.hetzner_token_missing'))
        ->toBe('No Hetzner token is associated with this server.')
        ->and(__('server.toasts.hetzner_token_valid'))
        ->toBe('Hetzner token is valid and working.')
        ->and(__('server.toasts.hetzner_token_invalid_permissions'))
        ->toBe('This token is invalid or has insufficient permissions.')
        ->and(__('server.toasts.hetzner_token_cannot_access_server'))
        ->toBe('This token cannot access this server. It may belong to a different Hetzner project.')
        ->and(__('server.toasts.hetzner_token_validation_failed', ['error' => 'boom']))
        ->toBe('Failed to validate token: boom');

    App::setLocale('zh_CN');

    expect(__('server.toasts.hetzner_token_unauthorized'))
        ->toBe('你无权使用此令牌。')
        ->and(__('server.toasts.hetzner_token_updated'))
        ->toBe('Hetzner 令牌已更新成功。')
        ->and(__('server.toasts.hetzner_token_missing'))
        ->toBe('此服务器未关联 Hetzner 令牌。')
        ->and(__('server.toasts.hetzner_token_valid'))
        ->toBe('Hetzner 令牌有效且可正常使用。')
        ->and(__('server.toasts.hetzner_token_invalid_permissions'))
        ->toBe('此令牌无效或权限不足。')
        ->and(__('server.toasts.hetzner_token_cannot_access_server'))
        ->toBe('此令牌无法访问该服务器。它可能属于其他 Hetzner 项目。')
        ->and(__('server.toasts.hetzner_token_validation_failed', ['error' => 'boom']))
        ->toBe('验证令牌失败：boom');
});
