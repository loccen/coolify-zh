<?php

use App\Console\Commands\Cloud\SyncStripeSubscriptions;
use Illuminate\Support\Facades\App;

it('wires cloud sync stripe subscriptions console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/Cloud/SyncStripeSubscriptions.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.sync_stripe_subscriptions.description'")
        ->toContain("trans('console.sync_stripe_subscriptions.error.cloud_only'")
        ->toContain("trans('console.sync_stripe_subscriptions.error.stripe_not_configured'")
        ->toContain("trans('console.sync_stripe_subscriptions.warn.running_with_fix'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.running_in_check_mode'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.fetching_subscriptions'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.total_subscriptions_checked'")
        ->toContain("trans('console.sync_stripe_subscriptions.warn.discrepancies_found'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.subscription_id'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.team_id'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.stripe_id'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.stripe_status'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.all_discrepancies_fixed'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.run_with_fix'")
        ->toContain("trans('console.sync_stripe_subscriptions.info.no_discrepancies_found'")
        ->toContain("trans('console.sync_stripe_subscriptions.warn.resubscribed_users'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.team_id_with_email'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.old'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.new'")
        ->toContain("trans('console.sync_stripe_subscriptions.error.errors_encountered'")
        ->toContain("trans('console.sync_stripe_subscriptions.labels.subscription_error'");

    expect($enTranslations['sync_stripe_subscriptions']['description'])->toBe('Sync subscription status with Stripe. By default only checks, use --fix to apply changes.')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['running_in_check_mode'])->toBe('Running in check mode (no changes will be made). Use --fix to apply corrections.')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['fetching_subscriptions'])->toBe('Fetching subscriptions from Stripe... :count')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['total_subscriptions_checked'])->toBe('Total subscriptions checked: :count')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['all_discrepancies_fixed'])->toBe('All discrepancies have been fixed.')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['run_with_fix'])->toBe('Run with --fix to correct these discrepancies.')
        ->and($enTranslations['sync_stripe_subscriptions']['info']['no_discrepancies_found'])->toBe('No discrepancies found. All subscriptions are in sync.')
        ->and($enTranslations['sync_stripe_subscriptions']['warn']['running_with_fix'])->toBe('Running with --fix: discrepancies will be corrected.')
        ->and($enTranslations['sync_stripe_subscriptions']['warn']['discrepancies_found'])->toBe('Discrepancies found: :count')
        ->and($enTranslations['sync_stripe_subscriptions']['warn']['resubscribed_users'])->toBe('Resubscribed users (same email, different customer): :count')
        ->and($enTranslations['sync_stripe_subscriptions']['error']['cloud_only'])->toBe('This command can only be run on Coolify Cloud.')
        ->and($enTranslations['sync_stripe_subscriptions']['error']['stripe_not_configured'])->toBe('Stripe is not configured.')
        ->and($enTranslations['sync_stripe_subscriptions']['error']['errors_encountered'])->toBe('Errors encountered: :count')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['subscription_id'])->toBe('Subscription ID: :value')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['team_id'])->toBe('Team ID: :value')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['stripe_id'])->toBe('Stripe ID: :value')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['stripe_status'])->toBe('Stripe Status: :value')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['team_id_with_email'])->toBe('Team ID: :team_id | Email: :email')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['old'])->toBe('Old: :subscription_id (cus: :customer_id)')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['new'])->toBe('New: :subscription_id (cus: :customer_id) [:status]')
        ->and($enTranslations['sync_stripe_subscriptions']['labels']['subscription_error'])->toBe('Subscription :subscription_id: :error');

    expect($zhTranslations['sync_stripe_subscriptions']['description'])->toBe('同步 Stripe 订阅状态。默认只检查，使用 --fix 才会应用变更。')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['running_in_check_mode'])->toBe('当前以检查模式运行（不会做任何更改）。使用 --fix 可应用修正。')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['fetching_subscriptions'])->toBe('正在从 Stripe 获取订阅... :count')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['total_subscriptions_checked'])->toBe('已检查订阅总数：:count')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['all_discrepancies_fixed'])->toBe('所有差异都已修复。')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['run_with_fix'])->toBe('使用 --fix 可修正这些差异。')
        ->and($zhTranslations['sync_stripe_subscriptions']['info']['no_discrepancies_found'])->toBe('未发现差异。所有订阅都已同步。')
        ->and($zhTranslations['sync_stripe_subscriptions']['warn']['running_with_fix'])->toBe('当前使用 --fix 运行：系统会修正这些差异。')
        ->and($zhTranslations['sync_stripe_subscriptions']['warn']['discrepancies_found'])->toBe('发现差异：:count')
        ->and($zhTranslations['sync_stripe_subscriptions']['warn']['resubscribed_users'])->toBe('重新订阅的用户（同一邮箱，不同 customer）：:count')
        ->and($zhTranslations['sync_stripe_subscriptions']['error']['cloud_only'])->toBe('此命令只能在 Coolify Cloud 上运行。')
        ->and($zhTranslations['sync_stripe_subscriptions']['error']['stripe_not_configured'])->toBe('Stripe 未配置。')
        ->and($zhTranslations['sync_stripe_subscriptions']['error']['errors_encountered'])->toBe('遇到错误：:count')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['subscription_id'])->toBe('订阅 ID：:value')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['team_id'])->toBe('团队 ID：:value')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['stripe_id'])->toBe('Stripe ID：:value')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['stripe_status'])->toBe('Stripe 状态：:value')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['team_id_with_email'])->toBe('团队 ID：:team_id | 邮箱：:email')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['old'])->toBe('旧订阅：:subscription_id (cus: :customer_id)')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['new'])->toBe('新订阅：:subscription_id (cus: :customer_id) [:status]')
        ->and($zhTranslations['sync_stripe_subscriptions']['labels']['subscription_error'])->toBe('订阅 :subscription_id：:error');
});

it('resolves cloud sync stripe subscriptions translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.sync_stripe_subscriptions.description'))->toBe('Sync subscription status with Stripe. By default only checks, use --fix to apply changes.')
        ->and(trans('console.sync_stripe_subscriptions.info.running_in_check_mode'))->toBe('Running in check mode (no changes will be made). Use --fix to apply corrections.')
        ->and(trans('console.sync_stripe_subscriptions.info.fetching_subscriptions', ['count' => 12]))->toBe('Fetching subscriptions from Stripe... 12')
        ->and(trans('console.sync_stripe_subscriptions.info.total_subscriptions_checked', ['count' => 5]))->toBe('Total subscriptions checked: 5')
        ->and(trans('console.sync_stripe_subscriptions.info.all_discrepancies_fixed'))->toBe('All discrepancies have been fixed.')
        ->and(trans('console.sync_stripe_subscriptions.info.run_with_fix'))->toBe('Run with --fix to correct these discrepancies.')
        ->and(trans('console.sync_stripe_subscriptions.info.no_discrepancies_found'))->toBe('No discrepancies found. All subscriptions are in sync.')
        ->and(trans('console.sync_stripe_subscriptions.warn.running_with_fix'))->toBe('Running with --fix: discrepancies will be corrected.')
        ->and(trans('console.sync_stripe_subscriptions.warn.discrepancies_found', ['count' => 3]))->toBe('Discrepancies found: 3')
        ->and(trans('console.sync_stripe_subscriptions.warn.resubscribed_users', ['count' => 2]))->toBe('Resubscribed users (same email, different customer): 2')
        ->and(trans('console.sync_stripe_subscriptions.error.cloud_only'))->toBe('This command can only be run on Coolify Cloud.')
        ->and(trans('console.sync_stripe_subscriptions.error.stripe_not_configured'))->toBe('Stripe is not configured.')
        ->and(trans('console.sync_stripe_subscriptions.error.errors_encountered', ['count' => 4]))->toBe('Errors encountered: 4')
        ->and(trans('console.sync_stripe_subscriptions.labels.subscription_id', ['value' => 'sub_123']))->toBe('Subscription ID: sub_123')
        ->and(trans('console.sync_stripe_subscriptions.labels.team_id', ['value' => 9]))->toBe('Team ID: 9')
        ->and(trans('console.sync_stripe_subscriptions.labels.stripe_id', ['value' => 'sub_456']))->toBe('Stripe ID: sub_456')
        ->and(trans('console.sync_stripe_subscriptions.labels.stripe_status', ['value' => 'active']))->toBe('Stripe Status: active')
        ->and(trans('console.sync_stripe_subscriptions.labels.team_id_with_email', ['team_id' => 1, 'email' => 'a@example.com']))->toBe('Team ID: 1 | Email: a@example.com')
        ->and(trans('console.sync_stripe_subscriptions.labels.old', ['subscription_id' => 'sub_old', 'customer_id' => 'cus_old']))->toBe('Old: sub_old (cus: cus_old)')
        ->and(trans('console.sync_stripe_subscriptions.labels.new', ['subscription_id' => 'sub_new', 'customer_id' => 'cus_new', 'status' => 'trialing']))->toBe('New: sub_new (cus: cus_new) [trialing]')
        ->and(trans('console.sync_stripe_subscriptions.labels.subscription_error', ['subscription_id' => 'sub_err', 'error' => 'boom']))->toBe('Subscription sub_err: boom')
        ->and((new SyncStripeSubscriptions)->getDescription())->toBe('Sync subscription status with Stripe. By default only checks, use --fix to apply changes.');

    App::setLocale('zh_CN');

    expect(trans('console.sync_stripe_subscriptions.description'))->toBe('同步 Stripe 订阅状态。默认只检查，使用 --fix 才会应用变更。')
        ->and(trans('console.sync_stripe_subscriptions.info.running_in_check_mode'))->toBe('当前以检查模式运行（不会做任何更改）。使用 --fix 可应用修正。')
        ->and(trans('console.sync_stripe_subscriptions.info.fetching_subscriptions', ['count' => 12]))->toBe('正在从 Stripe 获取订阅... 12')
        ->and(trans('console.sync_stripe_subscriptions.info.total_subscriptions_checked', ['count' => 5]))->toBe('已检查订阅总数：5')
        ->and(trans('console.sync_stripe_subscriptions.info.all_discrepancies_fixed'))->toBe('所有差异都已修复。')
        ->and(trans('console.sync_stripe_subscriptions.info.run_with_fix'))->toBe('使用 --fix 可修正这些差异。')
        ->and(trans('console.sync_stripe_subscriptions.info.no_discrepancies_found'))->toBe('未发现差异。所有订阅都已同步。')
        ->and(trans('console.sync_stripe_subscriptions.warn.running_with_fix'))->toBe('当前使用 --fix 运行：系统会修正这些差异。')
        ->and(trans('console.sync_stripe_subscriptions.warn.discrepancies_found', ['count' => 3]))->toBe('发现差异：3')
        ->and(trans('console.sync_stripe_subscriptions.warn.resubscribed_users', ['count' => 2]))->toBe('重新订阅的用户（同一邮箱，不同 customer）：2')
        ->and(trans('console.sync_stripe_subscriptions.error.cloud_only'))->toBe('此命令只能在 Coolify Cloud 上运行。')
        ->and(trans('console.sync_stripe_subscriptions.error.stripe_not_configured'))->toBe('Stripe 未配置。')
        ->and(trans('console.sync_stripe_subscriptions.error.errors_encountered', ['count' => 4]))->toBe('遇到错误：4')
        ->and(trans('console.sync_stripe_subscriptions.labels.subscription_id', ['value' => 'sub_123']))->toBe('订阅 ID：sub_123')
        ->and(trans('console.sync_stripe_subscriptions.labels.team_id', ['value' => 9]))->toBe('团队 ID：9')
        ->and(trans('console.sync_stripe_subscriptions.labels.stripe_id', ['value' => 'sub_456']))->toBe('Stripe ID：sub_456')
        ->and(trans('console.sync_stripe_subscriptions.labels.stripe_status', ['value' => 'active']))->toBe('Stripe 状态：active')
        ->and(trans('console.sync_stripe_subscriptions.labels.team_id_with_email', ['team_id' => 1, 'email' => 'a@example.com']))->toBe('团队 ID：1 | 邮箱：a@example.com')
        ->and(trans('console.sync_stripe_subscriptions.labels.old', ['subscription_id' => 'sub_old', 'customer_id' => 'cus_old']))->toBe('旧订阅：sub_old (cus: cus_old)')
        ->and(trans('console.sync_stripe_subscriptions.labels.new', ['subscription_id' => 'sub_new', 'customer_id' => 'cus_new', 'status' => 'trialing']))->toBe('新订阅：sub_new (cus: cus_new) [trialing]')
        ->and(trans('console.sync_stripe_subscriptions.labels.subscription_error', ['subscription_id' => 'sub_err', 'error' => 'boom']))->toBe('订阅 sub_err：boom')
        ->and((new SyncStripeSubscriptions)->getDescription())->toBe('同步 Stripe 订阅状态。默认只检查，使用 --fix 才会应用变更。');
});
