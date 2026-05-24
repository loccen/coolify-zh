<?php

use App\Console\Commands\ViewScheduledLogs;
use Illuminate\Support\Facades\App;

it('wires scheduled logs command strings through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/ViewScheduledLogs.php'));

    expect($command)
        ->toContain("trans('console.scheduled_logs.description'")
        ->toContain("trans('console.scheduled_logs.error.invalid_date_format'")
        ->toContain("trans('console.scheduled_logs.info.following_logs'")
        ->toContain("trans('console.scheduled_logs.info.showing_last_lines'")
        ->toContain("trans('console.scheduled_logs.warn.no_logs_found'")
        ->toContain("trans('console.scheduled_logs.info.available_log_files'")
        ->toContain("trans('console.scheduled_logs.info.normal_logs'")
        ->toContain("trans('console.scheduled_logs.info.error_logs'");
});

it('resolves scheduled logs command translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.scheduled_logs.description'))->toBe('View scheduled backups and tasks logs with optional filtering')
        ->and(trans('console.scheduled_logs.error.invalid_date_format'))->toBe('Invalid date format. Use Y-m-d (e.g. 2025-01-31).')
        ->and(trans('console.scheduled_logs.info.following_logs', ['type' => 'normal', 'date' => '2025-01-31', 'filter' => '']))->toBe('Following normal logs for 2025-01-31 (Press Ctrl+C to stop)...')
        ->and(trans('console.scheduled_logs.info.showing_last_lines', ['lines' => 50, 'type' => 'error', 'date' => '2025-01-31', 'filter' => '']))->toBe('Showing last 50 lines of error logs for 2025-01-31:')
        ->and(trans('console.scheduled_logs.warn.no_logs_found', ['type' => 'all', 'date' => '2025-01-31']))->toBe('No all logs found for date 2025-01-31')
        ->and(trans('console.scheduled_logs.info.available_log_files'))->toBe('Available scheduled log files:')
        ->and(trans('console.scheduled_logs.info.normal_logs'))->toBe('  Normal logs:')
        ->and(trans('console.scheduled_logs.info.error_logs'))->toBe('  Error logs:')
        ->and((new ViewScheduledLogs)->getDescription())->toBe('View scheduled backups and tasks logs with optional filtering');

    App::setLocale('zh_CN');

    expect(trans('console.scheduled_logs.description'))->toBe('查看可选过滤的计划任务备份和任务日志')
        ->and(trans('console.scheduled_logs.error.invalid_date_format'))->toBe('日期格式无效。请使用 Y-m-d（例如 2025-01-31）。')
        ->and(trans('console.scheduled_logs.info.following_logs', ['type' => '正常', 'date' => '2025-01-31', 'filter' => '']))->toBe('正在跟踪 2025-01-31 的正常日志（按 Ctrl+C 停止）...')
        ->and(trans('console.scheduled_logs.info.showing_last_lines', ['lines' => 50, 'type' => '错误', 'date' => '2025-01-31', 'filter' => '']))->toBe('显示 2025-01-31 的错误日志最后 50 行：')
        ->and(trans('console.scheduled_logs.warn.no_logs_found', ['type' => '全部', 'date' => '2025-01-31']))->toBe('未找到 2025-01-31 的全部日志')
        ->and(trans('console.scheduled_logs.info.available_log_files'))->toBe('可用的计划日志文件：')
        ->and(trans('console.scheduled_logs.info.normal_logs'))->toBe('  正常日志：')
        ->and(trans('console.scheduled_logs.info.error_logs'))->toBe('  错误日志：')
        ->and((new ViewScheduledLogs)->getDescription())->toBe('查看可选过滤的计划任务备份和任务日志');
});
