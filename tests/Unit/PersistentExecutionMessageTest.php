<?php

use App\Support\PersistentExecutionMessage;
use Illuminate\Support\Facades\App;

uses(Tests\TestCase::class);

it('translates the known startup interruption message for zh cn display', function () {
    App::setLocale('zh_CN');

    expect(PersistentExecutionMessage::forDisplay('Marked as failed during Coolify startup - job was interrupted'))
        ->toBe('Coolify 启动时已标记为失败，任务已中断');
});

it('keeps unknown persistent execution messages unchanged', function () {
    App::setLocale('zh_CN');

    expect(PersistentExecutionMessage::forDisplay('Backup failed: connection refused'))
        ->toBe('Backup failed: connection refused');
});
