<?php

return [
    'scheduled_logs' => [
        'description' => '查看可选过滤的计划任务备份和任务日志',
        'error' => [
            'invalid_date_format' => '日期格式无效。请使用 Y-m-d（例如 2025-01-31）。',
        ],
        'info' => [
            'following_logs' => '正在跟踪 :date 的:type日志（按 Ctrl+C 停止）...',
            'showing_last_lines' => '显示 :date 的:type日志最后 :lines 行：',
            'available_log_files' => '可用的计划日志文件：',
            'normal_logs' => '  正常日志：',
            'error_logs' => '  错误日志：',
        ],
        'warn' => [
            'no_logs_found' => '未找到 :date 的:type日志',
        ],
        'types' => [
            'normal' => '正常',
            'error' => '错误',
            'all' => '全部',
        ],
    ],
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
    'root_change_email' => [
        'description' => '更改 Root 邮箱',
        'about_to_change' => '即将更改 Root 用户的邮箱。',
        'email_prompt' => '请输入 Root 用户的新邮箱：',
        'updating' => '正在更新 Root 邮箱...',
        'updated_successfully' => 'Root 用户的邮箱已成功更新。',
        'failed_to_update' => '更新 Root 用户邮箱失败。',
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
    'cleanup_database' => [
        'description' => '清理数据库。',
        'running' => '正在清理数据库...',
        'running_dry_run' => '正在以 dry-run 模式清理数据库...',
        'keep_days' => '保留天数：:days',
        'delete_failed_jobs' => '删除 failed_jobs 中的 :count 条记录。',
        'delete_sessions' => '删除 sessions 中的 :count 条记录。',
        'delete_activity_log' => '删除 activity_log 中的 :count 条记录。',
        'delete_application_deployment_queues' => '删除 application_deployment_queues 中的 :count 条记录。',
        'delete_scheduled_task_executions' => '删除 scheduled_task_executions 中的 :count 条记录。',
    ],
    'cleanup_redis' => [
        'info' => [
            'would_delete' => 'Redis 清理：将删除 :count 项',
            'deleted' => 'Redis 清理：已删除 :count 项',
        ],
        'warn' => [
            'would_delete_stale_lock' => '将删除过期锁（无过期时间）：:key',
            'would_mark_failed' => '将标记为失败：:jobClass（处理了 :minutes 分钟）- :reason',
        ],
        'error' => [
            'redis_scan_failed' => 'Redis 扫描失败，停止获取键',
            'failed_to_decode_job_payload' => '解码作业负载失败：:key：:error。负载：:payload',
        ],
    ],
    'cleanup_names' => [
        'error' => [
            'unknown_model' => '未知模型：:model',
            'processing' => '处理 :model 时出错：:error',
        ],
        'info' => [
            'available_models' => '可用模型：:models',
            'would_sanitize' => '名称清理：将处理 :count 条记录',
            'sanitized' => '名称清理：已处理 :count 条记录',
        ],
    ],
    'cleanup_unreachable_servers' => [
        'description' => '清理不可达服务器（7 天）',
        'running' => '正在清理不可达服务器...',
        'cleanup_server' => '清理不可达服务器 :id，名称为 :name',
    ],
    'generate_testing_schema' => [
        'description' => '生成 PostgreSQL 数据库的 SQLite 测试 schema',
        'error' => [
            'not_postgresql' => "连接 ':connection' 不是 PostgreSQL。",
        ],
        'info' => [
            'reading_schema' => '正在从 PostgreSQL 读取 schema...',
            'schema_written' => 'Schema 已写入 :path',
            'summary' => ':tables 个表，:migrations 条 migration 记录。',
        ],
    ],
    'generate_openapi' => [
        'description' => '生成 OpenAPI 文档。',
        'generating' => '正在生成 OpenAPI 文档。',
        'converted' => '已将 OpenAPI YAML 转换为 JSON。',
    ],
    'generate_services' => [
        'info' => [
            'ignoring' => '忽略 :file',
            'processing' => '正在处理 :file',
        ],
    ],
    'traefik_check_version' => [
        'description' => '检查所有服务器上的 Traefik 代理版本，并向过期版本发送通知',
        'checking' => '正在检查所有服务器上的 Traefik 版本...',
        'dispatched' => 'Traefik 版本检查任务已成功分发。',
        'notifications_pending' => '系统将向使用过期 Traefik 版本的团队发送通知。',
        'dispatch_failed' => '分发 Traefik 版本检查任务失败：:message',
    ],
    'application_deployment_queue' => [
        'description' => '检查应用部署队列',
        'info' => [
            'no_deployments_found' => '最近 :seconds 秒内未找到部署。',
            'deployments_found' => '找到 :count 个在最近 :seconds 秒内创建的部署。',
            'deployment_is_stale' => '部署 :deployment_id 创建于 :created_at，已超过 :seconds 秒。正在将状态设为失败。',
        ],
        'confirm' => [
            'cancel_deployment' => '要取消部署 :deployment_id（创建于 :created_at）吗？',
        ],
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
