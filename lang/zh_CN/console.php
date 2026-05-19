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
];
