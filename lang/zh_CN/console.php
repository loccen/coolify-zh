<?php

return [
    'emails' => [
        'description' => '发送测试邮件或正式邮件',
        'select_prompt' => '要发送哪一封邮件？',
        'types' => [
            'updates' => '向所有用户发送更新邮件',
            'emails_test' => '测试',
            'database_backup_statuses_daily' => '数据库 - 备份状态（每日）',
            'application_deployment_success_daily' => '应用 - 部署成功（每日）',
            'application_deployment_success' => '应用 - 部署成功',
            'application_deployment_failed' => '应用 - 部署失败',
            'application_status_changed' => '应用 - 状态变化',
            'backup_success' => '数据库 - 备份成功',
            'backup_failed' => '数据库 - 备份失败',
            'realusers_before_trial' => '正式环境 - 已注册但未订阅、尚未进入试用期的用户',
            'realusers_server_lost_connection' => '正式环境 - 服务器连接丢失',
        ],
        'email_prompt' => '要发送到的邮箱地址：',
        'test_subject' => '测试邮件',
        'no_teams' => '没有找到团队。',
        'sending_emails' => '将发送到 :count 个邮箱地址。',
        'sending_admins' => '将发送给 :count 位管理员。',
        'confirm' => '确认继续吗？',
        'server_id' => '服务器 ID',
        'server_not_found' => '未找到服务器。',
        'sent_successfully' => '邮件已成功发送至 :email。',
    ],
    'root_reset_password' => [
        'description' => '重置 Root 密码',
        'about_to_reset' => '即将重置 Root 用户的密码。',
        'password_prompt' => '请输入 Root 用户的新密码：',
        'password_again_prompt' => '请再次输入新密码：',
        'passwords_do_not_match' => '两次输入的密码不一致。',
        'updating' => '正在更新 Root 密码...',
        'root_user_not_found' => '未找到 Root 用户。',
        'updated_successfully' => 'Root 密码已更新。',
        'failed_to_update' => '更新 Root 密码失败。',
    ],
    'migration' => [
        'description' => '开始迁移',
        'enabled' => '本服务器已启用迁移。',
        'disabled' => '本服务器未启用迁移。',
    ],
    'traefik_check_version' => [
        'description' => '检查所有服务器上的 Traefik 代理版本，并向过期版本发送通知',
        'checking' => '正在检查所有服务器上的 Traefik 版本...',
        'dispatched' => 'Traefik 版本检查任务已成功分发。',
        'notifications_pending' => '系统将向使用过期 Traefik 版本的团队发送通知。',
        'dispatch_failed' => '分发 Traefik 版本检查任务失败：:message',
    ],
    'clear_global_search_cache' => [
        'description' => '清除全局搜索缓存',
        'error' => [
            'no_authenticated_user' => '未找到已登录用户。请使用 --team=TEAM_ID 或 --all。',
            'team_not_found' => '未找到 ID 为 :team_id 的团队。',
        ],
        'warn' => [
            'no_teams_found' => '未找到团队。',
        ],
        'info' => [
            'cleared_team_cache' => '已清除团队 :team_name（ID：:team_id）的全局搜索缓存。',
            'cleared_all_teams_cache' => '已清除 :count 个团队的全局搜索缓存。',
        ],
    ],
];
