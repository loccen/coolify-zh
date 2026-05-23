<?php

use App\Console\Commands\CleanupRedis;
use Illuminate\Support\Facades\App;

it('wires cleanup redis summary lines through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/CleanupRedis.php'));

    expect($command)
        ->toContain("trans('console.cleanup_redis.info.would_delete'")
        ->toContain("trans('console.cleanup_redis.info.deleted'");
});

it('resolves cleanup redis summary translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.cleanup_redis.info.would_delete', ['count' => 3]))->toBe('Redis cleanup: would delete 3 items')
        ->and(trans('console.cleanup_redis.info.deleted', ['count' => 3]))->toBe('Redis cleanup: deleted 3 items')
        ->and((new CleanupRedis)->getDescription())->toBe('Cleanup Redis (Horizon jobs, metrics, overlapping queues, cache locks, and related data)');

    App::setLocale('zh_CN');

    expect(trans('console.cleanup_redis.info.would_delete', ['count' => 3]))->toBe('Redis 清理：将删除 3 项')
        ->and(trans('console.cleanup_redis.info.deleted', ['count' => 3]))->toBe('Redis 清理：已删除 3 项')
        ->and((new CleanupRedis)->getDescription())->toBe('Cleanup Redis (Horizon jobs, metrics, overlapping queues, cache locks, and related data)');
});
