<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in pricing plan views', function () {
    $componentView = file_get_contents(base_path('resources/views/components/pricing-plans.blade.php'));
    $subscriptionView = file_get_contents(base_path('resources/views/livewire/subscription/pricing-plans.blade.php'));

    expect($componentView)
        ->toContain("__('Payment frequency')")
        ->toContain("__('Basic plan')")
        ->toContain("__('Pro plan')")
        ->toContain("__('Ultimate plan')")
        ->toContain("__('Unlimited Trial')")
        ->toContain("__('Custom pricing')")
        ->toContain("__('Included Email System')")
        ->toContain("__('Priority Email Support')")
        ->toContain("__('Priority (Email/Chat) Support')")
        ->toContain("__('pay-as-you-go')")
        ->toContain("__('Connect :count servers'")
        ->and($subscriptionView)
        ->toContain("__('Pay-as-you-go')")
        ->toContain("__('Dynamic pricing based on the number of servers you connect.')")
        ->toContain("__('Connect unlimited servers')")
        ->toContain("__('Need official support for your self-hosted instance? <a class=\"underline dark:text-white\" href=\"https://coolify.io/docs/contact\" target=\"_blank\">Contact Us</a>')");
});

it('resolves pricing plan translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Payment frequency'))->toBe('付费周期')
        ->and(__('Monthly'))->toBe('月付')
        ->and(__('Annually'))->toBe('年付')
        ->and(__('Unlimited Trial'))->toBe('无限试用')
        ->and(__('Get Started'))->toBe('开始使用')
        ->and(__('Basic plan'))->toBe('基础版')
        ->and(__('Pro plan'))->toBe('专业版')
        ->and(__('Ultimate plan'))->toBe('旗舰版')
        ->and(__('Custom pricing'))->toBe('定制定价')
        ->and(__('billed monthly'))->toBe('按月计费')
        ->and(__('billed annually'))->toBe('按年计费')
        ->and(__('Included Email System'))->toBe('内置邮件系统')
        ->and(__('Email Support'))->toBe('邮件支持')
        ->and(__('Priority Email Support'))->toBe('优先邮件支持')
        ->and(__('Priority (Email/Chat) Support'))->toBe('优先支持（邮件 / 聊天）')
        ->and(__('Connect :count servers', ['count' => 10]))->toBe('可连接 10 台服务器')
        ->and(__('pay-as-you-go'))->toBe('按量付费')
        ->and(__('Dynamic pricing based on the number of servers you connect.'))->toBe('价格会根据你连接的服务器数量动态计算。')
        ->and(__('Need official support for your self-hosted instance? <a class="underline dark:text-white" href="https://coolify.io/docs/contact" target="_blank">Contact Us</a>'))->toBe('如果你的自托管实例需要官方支持，<a class="underline dark:text-white" href="https://coolify.io/docs/contact" target="_blank">请联系我们</a>')
        ->and(__('Need official support for your self-hosted instance?'))->toBe('你的自托管实例需要官方支持吗？');
});
