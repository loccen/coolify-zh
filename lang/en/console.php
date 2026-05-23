<?php

return [
    'scheduled_logs' => [
        'description' => 'View scheduled backups and tasks logs with optional filtering',
        'error' => [
            'invalid_date_format' => 'Invalid date format. Use Y-m-d (e.g. 2025-01-31).',
        ],
        'info' => [
            'following_logs' => 'Following :type logs for :date:filter (Press Ctrl+C to stop)...',
            'showing_last_lines' => 'Showing last :lines lines of :type logs for :date:filter:',
            'available_log_files' => 'Available scheduled log files:',
            'normal_logs' => '  Normal logs:',
            'error_logs' => '  Error logs:',
        ],
        'warn' => [
            'no_logs_found' => 'No :type logs found for date :date',
        ],
        'types' => [
            'normal' => 'normal',
            'error' => 'error',
            'all' => 'all',
        ],
    ],
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
    'root_change_email' => [
        'description' => 'Change Root Email',
        'about_to_change' => 'You are about to change the root user\'s email.',
        'email_prompt' => 'Give me a new email for root user',
        'updating' => 'Updating root email...',
        'updated_successfully' => 'Root user\'s email updated successfully.',
        'failed_to_update' => 'Failed to update root user\'s email.',
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
    'seeder' => [
        'description' => 'Start Seeder',
        'enabled' => 'Seeder is enabled on this server.',
        'disabled' => 'Seeder is disabled on this server.',
    ],
    'cleanup_database' => [
        'description' => 'Cleanup database.',
        'running' => 'Running database cleanup...',
        'running_dry_run' => 'Running database cleanup in dry-run mode...',
        'keep_days' => 'Keep days: :days',
        'delete_failed_jobs' => 'Delete :count entries from failed_jobs.',
        'delete_sessions' => 'Delete :count entries from sessions.',
        'delete_activity_log' => 'Delete :count entries from activity_log.',
        'delete_application_deployment_queues' => 'Delete :count entries from application_deployment_queues.',
        'delete_scheduled_task_executions' => 'Delete :count entries from scheduled_task_executions.',
    ],
    'cleanup_redis' => [
        'info' => [
            'would_delete' => 'Redis cleanup: would delete :count items',
            'deleted' => 'Redis cleanup: deleted :count items',
        ],
        'warn' => [
            'would_delete_stale_lock' => 'Would delete STALE lock (no expiration): :key',
            'would_mark_failed' => 'Would mark as FAILED: :jobClass (processing for :minutes min) - :reason',
        ],
        'error' => [
            'redis_scan_failed' => 'Redis scan failed, stopping key retrieval',
            'failed_to_decode_job_payload' => 'Failed to decode job payload for :key: :error. Payload: :payload',
        ],
    ],
    'cleanup_names' => [
        'error' => [
            'unknown_model' => 'Unknown model: :model',
            'processing' => 'Error processing :model: :error',
        ],
        'preview' => [
            'header' => '🧹 :model #:id',
            'from' => '    From: :value',
            'to' => '    To: :value',
        ],
        'info' => [
            'available_models' => 'Available models: :models',
            'would_sanitize' => 'Name cleanup: would sanitize :count records',
            'sanitized' => 'Name cleanup: sanitized :count records',
        ],
    ],
    'cleanup_unreachable_servers' => [
        'description' => 'Cleanup Unreachable Servers (7 days)',
        'running' => 'Running unreachable server cleanup...',
        'cleanup_server' => 'Cleanup unreachable server :id with name :name',
    ],
    'scheduled_job_diagnostics' => [
        'description' => 'Inspect dedup cache state and scheduling decisions for all scheduled jobs',
        'heartbeat' => 'Scheduler heartbeat: :heartbeat (:age)',
        'heartbeat_missing' => 'Scheduler heartbeat: MISSING — ScheduledJobManager may not be running',
        'sections' => [
            'docker_cleanup' => '=== Docker Cleanup Jobs ===',
            'scheduled_backups' => '=== Scheduled Backups ===',
            'scheduled_tasks' => '=== Scheduled Tasks ===',
            'server_manager_jobs' => '=== Server Manager Jobs ===',
        ],
    ],
    'generate_testing_schema' => [
        'description' => 'Generate SQLite testing schema from the PostgreSQL database',
        'error' => [
            'not_postgresql' => "Connection ':connection' is not PostgreSQL.",
        ],
        'info' => [
            'reading_schema' => 'Reading schema from PostgreSQL...',
            'schema_written' => 'Schema written to :path',
            'summary' => ':tables tables, :migrations migration records.',
        ],
    ],
    'generate_openapi' => [
        'description' => 'Generate OpenAPI documentation.',
        'generating' => 'Generating OpenAPI documentation.',
        'converted' => 'Converted OpenAPI YAML to JSON.',
    ],
    'generate_services' => [
        'info' => [
            'ignoring' => 'Ignoring :file',
            'processing' => 'Processing :file',
        ],
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
