<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in server show toast dispatches', function () {
    $show = file_get_contents(app_path('Livewire/Server/Show.php'));

    expect($show)
        ->toContain("__('server.toasts.server_reachable')")
        ->toContain("__('server.toasts.server_not_reachable'")
        ->toContain("__('server.toasts.restarting_sentinel')")
        ->toContain("__('server.toasts.sentinel_disabled_for_build_servers')")
        ->toContain("__('server.toasts.sentinel_token_regenerated')")
        ->toContain("__('server.toasts.hetzner_server_or_token_missing')")
        ->toContain("__('server.toasts.server_status_refreshed'")
        ->toContain("__('server.toasts.hetzner_server_starting')")
        ->toContain("__('server.toasts.server_details_fetch_failed')")
        ->toContain("__('server.toasts.no_hetzner_server_selected')")
        ->toContain("__('server.toasts.invalid_hetzner_token_selected')")
        ->toContain("__('server.toasts.hetzner_server_not_found'")
        ->toContain("__('server.toasts.server_linked_to_hetzner')")
        ->toContain("__('server.toasts.server_settings_updated')")
        ->toContain("__('server.toasts.sentinel_cannot_be_enabled_on_build_servers')")
        ->toContain("__('server.toasts.sentinel_restarted')");
});

it('resolves server toast translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('server.toasts.server_reachable'))
        ->toBe('服务器可访问。')
        ->and(__('server.toasts.server_not_reachable', [
            'documentationUrl' => 'https://coolify.io/docs/knowledge-base/server/openssh',
            'error' => 'boom',
        ]))
        ->toBe('服务器无法访问。<br><br>请查看此<a target="_blank" class="underline" href="https://coolify.io/docs/knowledge-base/server/openssh">文档</a>以获得更多帮助。<br><br>错误：boom')
        ->and(__('server.toasts.restarting_sentinel'))
        ->toBe('正在重启 Sentinel。')
        ->and(__('server.toasts.sentinel_disabled_for_build_servers'))
        ->toBe('已禁用 Sentinel，因为构建服务器无法运行 Sentinel。')
        ->and(__('server.toasts.sentinel_token_regenerated'))
        ->toBe('令牌已重新生成。正在重启 Sentinel。')
        ->and(__('server.toasts.hetzner_server_or_token_missing'))
        ->toBe('此服务器未关联 Hetzner Cloud 服务器或令牌。')
        ->and(__('server.toasts.server_status_refreshed', ['status' => 'Running']))
        ->toBe('服务器状态已刷新：Running')
        ->and(__('server.toasts.hetzner_server_starting'))
        ->toBe('Hetzner 服务器正在启动...')
        ->and(__('server.toasts.server_details_fetch_failed'))
        ->toBe('无法获取服务器详情。服务器可访问吗？')
        ->and(__('server.toasts.no_hetzner_server_selected'))
        ->toBe('未选择 Hetzner 服务器。')
        ->and(__('server.toasts.invalid_hetzner_token_selected'))
        ->toBe('所选令牌无效。')
        ->and(__('server.toasts.hetzner_server_not_found', ['id' => 42]))
        ->toBe('未找到 ID 为 42 的 Hetzner 服务器。')
        ->and(__('server.toasts.server_linked_to_hetzner'))
        ->toBe('服务器已成功关联到 Hetzner Cloud！')
        ->and(__('server.toasts.server_settings_updated'))
        ->toBe('服务器设置已更新。')
        ->and(__('server.toasts.sentinel_cannot_be_enabled_on_build_servers'))
        ->toBe('构建服务器无法启用 Sentinel。')
        ->and(__('server.toasts.sentinel_restarted'))
        ->toBe('Sentinel 已成功重启。');
});
