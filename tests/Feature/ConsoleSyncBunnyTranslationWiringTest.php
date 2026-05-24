<?php

use App\Console\Commands\SyncBunny;
use Illuminate\Support\Facades\App;

it('wires sync bunny console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/SyncBunny.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.sync_bunny.description'")
        ->toContain("trans('console.sync_bunny.info.preparing_artifacts'")
        ->toContain("trans('console.sync_bunny.info.preparing_service_template_artifact'")
        ->toContain("trans('console.sync_bunny.info.preparing_versions_and_releases_artifacts'")
        ->toContain("trans('console.sync_bunny.info.preparing_releases_artifact'")
        ->toContain("trans('console.sync_bunny.info.preparing_versions_artifact'")
        ->toContain("trans('console.sync_bunny.info.no_artifact_changes_detected'")
        ->toContain("trans('console.sync_bunny.info.artifacts_pushed_successfully'")
        ->not->toContain('Preparing service template artifact.')
        ->not->toContain('Preparing versions.json and releases.json artifacts.')
        ->not->toContain('Preparing releases.json artifact.')
        ->not->toContain('Preparing versions.json artifact.')
        ->not->toContain('No artifact changes detected.')
        ->not->toContain('Artifacts pushed to ');

    expect($enTranslations['sync_bunny']['description'])->toBe('Sync release artifacts to the GitHub Pages artifact repository')
        ->and($enTranslations['sync_bunny']['info']['preparing_artifacts'])->toBe('Preparing :kind artifacts for :repository.')
        ->and($enTranslations['sync_bunny']['info']['preparing_service_template_artifact'])->toBe('Preparing service template artifact.')
        ->and($enTranslations['sync_bunny']['info']['preparing_versions_and_releases_artifacts'])->toBe('Preparing versions.json and releases.json artifacts.')
        ->and($enTranslations['sync_bunny']['info']['preparing_releases_artifact'])->toBe('Preparing releases.json artifact.')
        ->and($enTranslations['sync_bunny']['info']['preparing_versions_artifact'])->toBe('Preparing versions.json artifact.')
        ->and($enTranslations['sync_bunny']['info']['no_artifact_changes_detected'])->toBe('No artifact changes detected.')
        ->and($enTranslations['sync_bunny']['info']['artifacts_pushed_successfully'])->toBe('Artifacts pushed to :repository successfully.');

    expect($zhTranslations['sync_bunny']['description'])->toBe('将发布 artifacts 同步到 GitHub Pages artifacts 仓库')
        ->and($zhTranslations['sync_bunny']['info']['preparing_artifacts'])->toBe('正在为 :repository 准备 :kind artifacts。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_service_template_artifact'])->toBe('正在准备 service template artifact。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_versions_and_releases_artifacts'])->toBe('正在准备 versions.json 和 releases.json artifacts。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_releases_artifact'])->toBe('正在准备 releases.json artifact。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_versions_artifact'])->toBe('正在准备 versions.json artifact。')
        ->and($zhTranslations['sync_bunny']['info']['no_artifact_changes_detected'])->toBe('未检测到 artifact 变更。')
        ->and($zhTranslations['sync_bunny']['info']['artifacts_pushed_successfully'])->toBe('Artifacts 已成功推送到 :repository。');
});

it('resolves sync bunny translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.sync_bunny.description'))->toBe('Sync release artifacts to the GitHub Pages artifact repository')
        ->and(trans('console.sync_bunny.info.preparing_artifacts', ['kind' => 'nightly', 'repository' => 'loccen/coolify-zh-artifacts']))->toBe('Preparing nightly artifacts for loccen/coolify-zh-artifacts.')
        ->and(trans('console.sync_bunny.info.preparing_service_template_artifact'))->toBe('Preparing service template artifact.')
        ->and(trans('console.sync_bunny.info.preparing_versions_and_releases_artifacts'))->toBe('Preparing versions.json and releases.json artifacts.')
        ->and(trans('console.sync_bunny.info.preparing_releases_artifact'))->toBe('Preparing releases.json artifact.')
        ->and(trans('console.sync_bunny.info.preparing_versions_artifact'))->toBe('Preparing versions.json artifact.')
        ->and(trans('console.sync_bunny.info.no_artifact_changes_detected'))->toBe('No artifact changes detected.')
        ->and(trans('console.sync_bunny.info.artifacts_pushed_successfully', ['repository' => 'loccen/coolify-zh-artifacts']))->toBe('Artifacts pushed to loccen/coolify-zh-artifacts successfully.')
        ->and((new SyncBunny)->getDescription())->toBe('Sync release artifacts to the GitHub Pages artifact repository');

    App::setLocale('zh_CN');

    expect(trans('console.sync_bunny.description'))->toBe('将发布 artifacts 同步到 GitHub Pages artifacts 仓库')
        ->and(trans('console.sync_bunny.info.preparing_artifacts', ['kind' => 'nightly', 'repository' => 'loccen/coolify-zh-artifacts']))->toBe('正在为 loccen/coolify-zh-artifacts 准备 nightly artifacts。')
        ->and(trans('console.sync_bunny.info.preparing_service_template_artifact'))->toBe('正在准备 service template artifact。')
        ->and(trans('console.sync_bunny.info.preparing_versions_and_releases_artifacts'))->toBe('正在准备 versions.json 和 releases.json artifacts。')
        ->and(trans('console.sync_bunny.info.preparing_releases_artifact'))->toBe('正在准备 releases.json artifact。')
        ->and(trans('console.sync_bunny.info.preparing_versions_artifact'))->toBe('正在准备 versions.json artifact。')
        ->and(trans('console.sync_bunny.info.no_artifact_changes_detected'))->toBe('未检测到 artifact 变更。')
        ->and(trans('console.sync_bunny.info.artifacts_pushed_successfully', ['repository' => 'loccen/coolify-zh-artifacts']))->toBe('Artifacts 已成功推送到 loccen/coolify-zh-artifacts。')
        ->and((new SyncBunny)->getDescription())->toBe('将发布 artifacts 同步到 GitHub Pages artifacts 仓库');
});
