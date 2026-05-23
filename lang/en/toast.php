<?php

return [
    'default_title' => 'Default Toast Notification',
    'livewire' => [
        'boarding' => [
            'localhost_server_not_found' => 'Localhost server is not found. Something went wrong during installation. Please try to reinstall or contact support.',
            'select_private_key' => 'Please select a private key.',
            'server_exists_in_team' => 'A server with this IP/Domain already exists in your team.',
            'server_exists_in_other_team' => 'A server with this IP/Domain is already in use by another team.',
            'project_not_found' => 'Project not found.',
        ],
        'global_search' => [
            'server_not_found' => 'Server not found.',
            'no_destinations_found' => 'No destinations found on this server.',
            'create_project_first' => 'Please create a project first.',
            'no_environments_found' => 'No environments found in project.',
        ],
        'help' => [
            'feedback_sent' => 'Feedback sent.',
            'feedback_follow_up' => 'We will get in touch with you as soon as possible.',
        ],
        'server_private_key' => [
            'forbidden' => 'You are not allowed to use this private key.',
            'updated' => 'Private key updated successfully.',
            'server_reachable' => 'Server is reachable.',
            'server_not_reachable' => 'Server is not reachable.<br><br>Check this <a target="_blank" class="underline" href=":documentationUrl">documentation</a> for further help.<br><br>Error: :error',
        ],
        'storage' => [
            'backup_already_using_storage' => 'The backup is already using this storage.',
            'backup_moved' => 'Backup moved.',
            'connection_working' => 'Connection is working.',
            'failed_to_test_connection' => 'Failed to test connection.',
            'moved_to' => 'Moved to :name.',
            'no_change' => 'No change.',
            's3_backup_disabled' => 'S3 backup has been disabled for this schedule.',
            's3_disabled' => 'S3 disabled.',
            'storage_not_found' => 'Storage not found.',
            'storage_settings_updated_and_verified' => 'Storage settings updated and connection verified.',
            'tested_with_list_objects_v2' => 'Tested with "ListObjectsV2" action.',
        ],
    ],
    'messages' => [
        'all_logs_downloaded' => 'All logs downloaded.',
        'copied_to_clipboard' => 'Copied to clipboard.',
        'logs_copied_to_clipboard' => 'Logs copied to clipboard.',
    ],
    'titles' => [
        'error' => 'Error',
        'info' => 'Info',
        'success' => 'Success',
        'warning' => 'Warning',
    ],
];
