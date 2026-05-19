<?php

return [
    'title' => 'Storage Details',
    'validate_connection' => 'Validate Connection',
    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'endpoint' => 'Endpoint',
        'bucket' => 'Bucket',
        'region' => 'Region',
        'access_key' => 'Access Key',
        'secret_key' => 'Secret Key',
    ],
    'delete_confirmation' => [
        'title' => 'Confirm Storage Deletion?',
        'label' => 'Please confirm the execution of the actions by entering the Storage Name below',
        'short_label' => 'Storage Name',
        'actions' => [
            'delete_storage' => 'The selected storage location will be permanently deleted from Coolify.',
            'update_backups' => ':count backup schedule(s) will be updated to no longer save to S3 and will only store backups locally on the server.',
        ],
    ],
];
