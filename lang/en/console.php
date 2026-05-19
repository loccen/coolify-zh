<?php

return [
    'emails' => [
        'description' => 'Send test or production emails',
        'select_prompt' => 'Which email should be sent?',
        'types' => [
            'updates' => 'Send update email to all users',
            'emails_test' => 'Test',
            'database_backup_statuses_daily' => 'Database - Backup Statuses (Daily)',
            'application_deployment_success_daily' => 'Application - Deployment Success (Daily)',
            'application_deployment_success' => 'Application - Deployment Success',
            'application_deployment_failed' => 'Application - Deployment Failed',
            'application_status_changed' => 'Application - Status Changed',
            'backup_success' => 'Database - Backup Success',
            'backup_failed' => 'Database - Backup Failed',
            'realusers_before_trial' => 'REAL - Registered users before trial without subscription',
            'realusers_server_lost_connection' => 'REAL - Server lost connection',
        ],
        'email_prompt' => 'Email address to send to:',
        'test_subject' => 'Test Email',
        'no_teams' => 'No teams found.',
        'sending_emails' => 'Sending to :count emails.',
        'sending_admins' => 'Sending to :count admins.',
        'confirm' => 'Are you sure?',
        'server_id' => 'Server ID',
        'server_not_found' => 'Server not found.',
        'sent_successfully' => 'Email sent to :email successfully.',
    ],
];
