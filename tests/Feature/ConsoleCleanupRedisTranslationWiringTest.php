<?php

use App\Console\Commands\CleanupRedis;
use Illuminate\Support\Facades\App;

it('wires cleanup redis summary lines through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/CleanupRedis.php'));

    expect($command)
        ->toContain("trans('console.cleanup_redis.info.would_delete'")
        ->toContain("trans('console.cleanup_redis.info.deleted'")
        ->toContain("trans('console.cleanup_redis.warn.would_delete_stale_lock'")
        ->toContain("trans('console.cleanup_redis.error.redis_scan_failed'")
        ->toContain("trans('console.cleanup_redis.error.failed_to_decode_job_payload'")
        ->toContain("trans('console.cleanup_redis.warn.would_mark_failed'");
});

it('resolves cleanup redis summary translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_redis.info.would_delete', ['count' => 3]))->toBe('Redis cleanup: would delete 3 items')
        ->and(trans('console.cleanup_redis.info.deleted', ['count' => 3]))->toBe('Redis cleanup: deleted 3 items')
        ->and(trans('console.cleanup_redis.warn.would_delete_stale_lock', ['key' => 'lock:123']))->toBe('Would delete STALE lock (no expiration): lock:123')
        ->and(trans('console.cleanup_redis.error.redis_scan_failed'))->toBe('Redis scan failed, stopping key retrieval')
        ->and(trans('console.cleanup_redis.error.failed_to_decode_job_payload', [
            'key' => 'job:123',
            'error' => 'Syntax error',
            'payload' => '{"bad":',
        ]))->toBe('Failed to decode job payload for job:123: Syntax error. Payload: {"bad":')
        ->and(trans('console.cleanup_redis.warn.would_mark_failed', [
            'jobClass' => 'App\\Jobs\\DemoJob',
            'minutes' => 12.5,
            'reason' => 'Processing for more than 12 hours',
        ]))->toBe('Would mark as FAILED: App\\Jobs\\DemoJob (processing for 12.5 min) - Processing for more than 12 hours')
        ->and((new CleanupRedis)->getDescription())->toBe('Cleanup Redis (Horizon jobs, metrics, overlapping queues, cache locks, and related data)');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_redis.info.would_delete', ['count' => 3]))->toBe('Redis 清理：将删除 3 项')
        ->and(trans('console.cleanup_redis.info.deleted', ['count' => 3]))->toBe('Redis 清理：已删除 3 项')
        ->and(trans('console.cleanup_redis.warn.would_delete_stale_lock', ['key' => 'lock:123']))->toBe('将删除过期锁（无过期时间）：lock:123')
        ->and(trans('console.cleanup_redis.error.redis_scan_failed'))->toBe('Redis 扫描失败，停止获取键')
        ->and(trans('console.cleanup_redis.error.failed_to_decode_job_payload', [
            'key' => 'job:123',
            'error' => 'Syntax error',
            'payload' => '{"bad":',
        ]))->toBe('解码作业负载失败：job:123：Syntax error。负载：{"bad":')
        ->and(trans('console.cleanup_redis.warn.would_mark_failed', [
            'jobClass' => 'App\\Jobs\\DemoJob',
            'minutes' => 12.5,
            'reason' => '处理超过 12 小时',
        ]))->toBe('将标记为失败：App\\Jobs\\DemoJob（处理了 12.5 分钟）- 处理超过 12 小时')
        ->and((new CleanupRedis)->getDescription())->toBe('Cleanup Redis (Horizon jobs, metrics, overlapping queues, cache locks, and related data)');
});
