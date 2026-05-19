<?php

return [
    'subtitle' => '无需离开浏览器即可在服务器和容器上执行命令。',
    'loading_containers' => '正在加载服务器和容器...',
    'select_target' => '选择服务器或容器',
    'no_servers' => '未找到具有终端访问权限的服务器。',
    'help' => [
        'connection' => '如果连接服务器遇到问题，请确认端口已开放。<br><br><a class=\'underline\' href=\':url\' target=\'_blank\'>文档</a>',
    ],
    'reasons' => [
        'connection_timeout' => '连接超时',
        'failed_to_create_websocket_connection' => '创建 WebSocket 连接失败：:message',
        'websocket_error_occurred' => '发生 WebSocket 错误',
    ],
    'separators' => [
        'connection_lost' => ':time 连接中断，正在重连...',
        'reconnected' => ':time 已重新连接',
    ],
    'status' => [
        'connection_closed' => '（连接已关闭）',
        'connection_failed' => '（连接失败，已超过最大重试次数）',
        'unexpected_error' => '（抱歉，出了点问题，请重试）',
    ],
    'toasts' => [
        'connection_error' => '终端连接错误：:reason',
        'inactivity_closed' => '终端因 30 分钟无操作已关闭。',
        'reconnecting' => '终端 WebSocket 连接已断开，正在重新连接...',
    ],
];
