<?php

return [
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
        'reconnecting' => '终端 WebSocket 连接已断开，正在重连...',
    ],
];
