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
    'root_reset_password' => [
        'description' => 'Reset Root Password',
        'about_to_reset' => 'You are about to reset the Root password.',
        'password_prompt' => 'Enter a new password for the Root user:',
        'password_again_prompt' => 'Enter the new password again:',
        'passwords_do_not_match' => 'Passwords do not match.',
        'updating' => 'Updating Root password...',
        'root_user_not_found' => 'Root user not found.',
        'updated_successfully' => 'Root password updated successfully.',
        'failed_to_update' => 'Failed to update Root password.',
    ],
    'migration' => [
        'description' => 'Start Migration',
        'enabled' => 'Migration is enabled on this server.',
        'disabled' => 'Migration is disabled on this server.',
    ],
    'traefik_check_version' => [
        'description' => 'Check Traefik proxy versions on all servers and send notifications for outdated versions',
        'checking' => 'Checking Traefik versions on all servers...',
        'dispatched' => 'Traefik version check job dispatched successfully.',
        'notifications_pending' => 'Notifications will be sent to teams with outdated Traefik versions.',
        'dispatch_failed' => 'Failed to dispatch Traefik version check job: :message',
    ],
    'application_deployment_queue' => [
        'description' => 'Check application deployment queue',
        'info' => [
            'no_deployments_found' => 'No deployments found in the last :seconds seconds.',
            'deployments_found' => 'Found :count deployments created in the last :seconds seconds.',
            'deployment_is_stale' => 'Deployment :deployment_id created at :created_at is older than :seconds seconds. Setting status to failed.',
        ],
        'confirm' => [
            'cancel_deployment' => 'Do you want to cancel deployment :deployment_id created at :created_at?',
        ],
    ],
    'clear_global_search_cache' => [
        'description' => 'Clear the global search cache',
        'error' => [
            'no_authenticated_user' => 'No authenticated user found. Use --team=TEAM_ID or --all.',
            'team_not_found' => 'Team with ID :team_id not found.',
        ],
        'warn' => [
            'no_teams_found' => 'No teams found.',
        ],
        'info' => [
            'cleared_team_cache' => 'Cleared global search cache for team :team_name (ID: :team_id).',
            'cleared_all_teams_cache' => 'Cleared global search cache for :count team(s).',
        ],
    ],
];
