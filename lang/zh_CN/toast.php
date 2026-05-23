<?php

return [
    'default_title' => '通知',
    'livewire' => [
        'boarding' => [
            'localhost_server_not_found' => '未找到 Localhost 服务器。安装过程中出了问题。请重新安装或联系支持。',
            'select_private_key' => '请选择一个私钥。',
            'server_exists_in_team' => '你的团队中已存在使用此 IP/Domain 的服务器。',
            'server_exists_in_other_team' => '此 IP/Domain 已被其他团队使用。',
            'project_not_found' => '未找到项目。',
        ],
        'global_search' => [
            'server_not_found' => '未找到服务器。',
            'no_destinations_found' => '此服务器上未找到目标位置。',
            'create_project_first' => '请先创建一个项目。',
            'no_environments_found' => '项目中未找到环境。',
        ],
        'help' => [
            'feedback_sent' => '反馈已发送。',
            'feedback_follow_up' => '我们会尽快与你联系。',
        ],
        'server_private_key' => [
            'forbidden' => '你无权使用这个私钥。',
            'updated' => '私钥已更新。',
            'server_reachable' => '服务器可访问。',
            'server_not_reachable' => '服务器无法访问。<br><br>请查看此<a target="_blank" class="underline" href=":documentationUrl">文档</a>以获得更多帮助。<br><br>错误：:error',
        ],
        'storage' => [
            'backup_already_using_storage' => '该备份已在使用此存储。',
            'backup_moved' => '备份已迁移。',
            'connection_working' => '连接正常。',
            'failed_to_test_connection' => '测试连接失败。',
            'moved_to' => '已迁移到 :name。',
            'no_change' => '没有变化。',
            's3_backup_disabled' => '已为此计划禁用 S3 备份。',
            's3_disabled' => 'S3 已禁用。',
            'storage_not_found' => '未找到存储。',
            'storage_settings_updated_and_verified' => '存储设置已更新，且连接已验证。',
            'tested_with_list_objects_v2' => '已使用 “ListObjectsV2” 操作测试。',
        ],
    ],
    'messages' => [
        'all_logs_downloaded' => '已下载所有日志。',
        'copied_to_clipboard' => '已复制到剪贴板。',
        'logs_copied_to_clipboard' => '日志已复制到剪贴板。',
    ],
    'titles' => [
        'error' => '错误',
        'info' => '提示',
        'success' => '成功',
        'warning' => '警告',
    ],
];
