<?php

use App\Console\Commands\NotifyDemo;
use Illuminate\Support\Facades\App;

it('wires notify demo command strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/NotifyDemo.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.notify_demo.description'")
        ->toContain("trans('console.notify_demo.intro'")
        ->toContain("trans('console.notify_demo.channels_label'")
        ->toContain("trans('console.notify_demo.prompt'");

    expect($enTranslations['notify_demo']['description'])->toBe('Send a demo notification. Run without a channel to see available options.')
        ->and($enTranslations['notify_demo']['intro'])->toBe('Demo Notify <strong class="text-coolify">=></strong> Send a demo notification to a given channel.')
        ->and($enTranslations['notify_demo']['channels_label'])->toBe('Channels:')
        ->and($enTranslations['notify_demo']['prompt'])->toBe('In which manner do you want a <strong class="text-coolify">coolify</strong> notification?');

    expect($zhTranslations['notify_demo']['description'])->toBe('发送演示通知。不带 channel 运行时会显示可用选项。')
        ->and($zhTranslations['notify_demo']['intro'])->toBe('Demo Notify <strong class="text-coolify">=></strong> 向指定 channel 发送演示通知。')
        ->and($zhTranslations['notify_demo']['channels_label'])->toBe('渠道：')
        ->and($zhTranslations['notify_demo']['prompt'])->toBe('你想以哪种方式接收 <strong class="text-coolify">coolify</strong> 通知？');
});

it('resolves notify demo translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.notify_demo.description'))->toBe('Send a demo notification. Run without a channel to see available options.')
        ->and(trans('console.notify_demo.intro'))->toBe('Demo Notify <strong class="text-coolify">=></strong> Send a demo notification to a given channel.')
        ->and(trans('console.notify_demo.channels_label'))->toBe('Channels:')
        ->and(trans('console.notify_demo.prompt'))->toBe('In which manner do you want a <strong class="text-coolify">coolify</strong> notification?')
        ->and((new NotifyDemo)->getDescription())->toBe('Send a demo notification. Run without a channel to see available options.');

    App::setLocale('zh_CN');

    expect(trans('console.notify_demo.description'))->toBe('发送演示通知。不带 channel 运行时会显示可用选项。')
        ->and(trans('console.notify_demo.intro'))->toBe('Demo Notify <strong class="text-coolify">=></strong> 向指定 channel 发送演示通知。')
        ->and(trans('console.notify_demo.channels_label'))->toBe('渠道：')
        ->and(trans('console.notify_demo.prompt'))->toBe('你想以哪种方式接收 <strong class="text-coolify">coolify</strong> 通知？')
        ->and((new NotifyDemo)->getDescription())->toBe('发送演示通知。不带 channel 运行时会显示可用选项。');
});
