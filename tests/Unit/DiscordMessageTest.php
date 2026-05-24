<?php

use App\Notifications\Dto\DiscordMessage;
use Illuminate\Support\Facades\App;

uses(Tests\TestCase::class);

it('uses the current locale for the timestamp field name in payload', function () {
    App::setLocale('en');

    $englishPayload = (new DiscordMessage(
        title: 'Test Title',
        description: 'Test description',
        color: DiscordMessage::infoColor(),
    ))->toPayload();

    expect($englishPayload['embeds'][0]['fields'])
        ->toHaveCount(1)
        ->and(last($englishPayload['embeds'][0]['fields'])['name'])
        ->toBe('Time');

    App::setLocale('zh_CN');

    $chinesePayload = (new DiscordMessage(
        title: '测试标题',
        description: '测试描述',
        color: DiscordMessage::infoColor(),
    ))->toPayload();

    expect($chinesePayload['embeds'][0]['fields'])
        ->toHaveCount(1)
        ->and(last($chinesePayload['embeds'][0]['fields'])['name'])
        ->toBe('时间');
});
