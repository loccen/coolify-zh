<?php

return [
    'title' => '存储详情',
    'validate_connection' => '验证连接',
    'fields' => [
        'name' => '名称',
        'description' => '描述',
        'endpoint' => '端点',
        'bucket' => '存储桶',
        'region' => '区域',
        'access_key' => '访问密钥',
        'secret_key' => '秘密密钥',
    ],
    'delete_confirmation' => [
        'title' => '确认删除存储？',
        'label' => '请输入下方存储名称以确认执行这些操作',
        'short_label' => '存储名称',
        'actions' => [
            'delete_storage' => '所选存储位置将从 Coolify 中永久删除。',
            'update_backups' => ':count 个备份计划将更新为不再保存到 S3，只保留服务器本地备份。',
        ],
    ],
];
