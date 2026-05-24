<?php

use Illuminate\Support\Facades\App;

it('keeps english detail statuses lower-case when translator is bound', function () {
    App::setLocale('en');

    expect(formatContainerStatus('running:healthy:excluded'))->toBe('Running (healthy, excluded)');
});

it('translates excluded detail statuses in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(formatContainerStatus('running:healthy:excluded'))->toBe('运行中 (健康, 已排除)')
        ->and(formatContainerStatus('exited:excluded'))->toBe('已退出 (已排除)');
});
