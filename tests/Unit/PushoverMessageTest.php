<?php

use App\Notifications\Dto\PushoverMessage;
use Illuminate\Support\Facades\App;

uses(Tests\TestCase::class);

it('uses the current locale default button text in payload when button text is missing', function () {
    App::setLocale('en');

    $englishMessage = new PushoverMessage(
        title: 'Test Title',
        message: 'Base message',
        buttons: [['url' => 'https://coolify.test/resource']],
    );

    $englishPayload = $englishMessage->toPayload('token', 'user');

    expect($englishPayload['message'])
        ->toContain("<a href='https://coolify.test/resource'>Click here</a>");

    App::setLocale('zh_CN');

    $chineseMessage = new PushoverMessage(
        title: '测试标题',
        message: '基础消息',
        buttons: [['url' => 'https://coolify.test/resource']],
    );

    $chinesePayload = $chineseMessage->toPayload('token', 'user');

    expect($chinesePayload['message'])
        ->toContain("<a href='https://coolify.test/resource'>点击这里</a>");
});
