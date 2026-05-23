<?php

return [
    'subtitle' => 'Execute commands on your servers and containers without leaving the browser.',
    'loading_containers' => 'Loading servers and containers...',
    'select_target' => 'Select a server or container',
    'no_servers' => 'No servers with terminal access found.',
    'navigation' => [
        'access_server' => 'Access server terminal',
    ],
    'help' => [
        'connection' => 'If you\'re having trouble connecting to your server, make sure that the port is open.<br><br><a class=\'underline\' href=\':url\' target=\'_blank\'>Documentation</a>',
    ],
    'reasons' => [
        'connection_timeout' => 'Connection timeout',
        'failed_to_create_websocket_connection' => 'Failed to create WebSocket connection: :message',
        'websocket_error_occurred' => 'WebSocket error occurred',
    ],
    'separators' => [
        'connection_lost' => 'Connection lost at :time, reconnecting...',
        'reconnected' => 'Reconnected at :time',
    ],
    'status' => [
        'connection_closed' => '(connection closed)',
        'connection_failed' => '(connection failed - max retries exceeded)',
        'unexpected_error' => '(sorry, something went wrong, please try again)',
    ],
    'toasts' => [
        'connection_error' => 'Terminal connection error: :reason',
        'inactivity_closed' => 'Terminal closed after 30 minutes of inactivity.',
        'reconnecting' => 'Terminal websocket connection lost. Reconnecting...',
    ],
];
