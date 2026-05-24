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
        ->toContain("trans('console.sync_bunny.confirm.sync_artifacts'")
        ->toContain("trans('console.sync_bunny.confirm.sync_service_template'")
        ->toContain("trans('console.sync_bunny.confirm.sync_versions_and_releases'")
        ->toContain("trans('console.sync_bunny.confirm.sync_releases_json'")
        ->toContain("trans('console.sync_bunny.confirm.sync_versions_json'")
        ->toContain("trans('console.sync_bunny.kinds.'")
        ->not->toContain('Preparing service template artifact.')
        ->not->toContain('Preparing versions.json and releases.json artifacts.')
        ->not->toContain('Preparing releases.json artifact.')
        ->not->toContain('Preparing versions.json artifact.')
        ->not->toContain('No artifact changes detected.')
        ->not->toContain('Artifacts pushed to ')
        ->not->toContain('确认要同步这些 artifacts 吗？')
        ->not->toContain('确认要同步 service template 吗？')
        ->not->toContain('确认要同步版本与发布记录吗？')
        ->not->toContain('确认要同步 releases.json 吗？')
        ->not->toContain('确认要同步 versions.json 吗？');

    expect($enTranslations['sync_bunny']['description'])->toBe('Sync release artifacts to the GitHub Pages artifact repository')
        ->and($enTranslations['sync_bunny']['info']['preparing_artifacts'])->toBe('Preparing :kind artifacts for :repository.')
        ->and($enTranslations['sync_bunny']['info']['preparing_service_template_artifact'])->toBe('Preparing service template artifact.')
        ->and($enTranslations['sync_bunny']['info']['preparing_versions_and_releases_artifacts'])->toBe('Preparing versions.json and releases.json artifacts.')
        ->and($enTranslations['sync_bunny']['info']['preparing_releases_artifact'])->toBe('Preparing releases.json artifact.')
        ->and($enTranslations['sync_bunny']['info']['preparing_versions_artifact'])->toBe('Preparing versions.json artifact.')
        ->and($enTranslations['sync_bunny']['info']['no_artifact_changes_detected'])->toBe('No artifact changes detected.')
        ->and($enTranslations['sync_bunny']['info']['artifacts_pushed_successfully'])->toBe('Artifacts pushed to :repository successfully.')
        ->and($enTranslations['sync_bunny']['confirm']['sync_artifacts'])->toBe('Do you want to sync these artifacts?')
        ->and($enTranslations['sync_bunny']['confirm']['sync_service_template'])->toBe('Do you want to sync the service template artifact?')
        ->and($enTranslations['sync_bunny']['confirm']['sync_versions_and_releases'])->toBe('Do you want to sync versions and release metadata?')
        ->and($enTranslations['sync_bunny']['confirm']['sync_releases_json'])->toBe('Do you want to sync releases.json?')
        ->and($enTranslations['sync_bunny']['confirm']['sync_versions_json'])->toBe('Do you want to sync versions.json?')
        ->and($enTranslations['sync_bunny']['kinds']['nightly'])->toBe('nightly')
        ->and($enTranslations['sync_bunny']['kinds']['production'])->toBe('production');

    expect($zhTranslations['sync_bunny']['description'])->toBe('将发布产物同步到 GitHub Pages 产物仓库')
        ->and($zhTranslations['sync_bunny']['info']['preparing_artifacts'])->toBe('正在为 :repository 准备:kind发布产物。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_service_template_artifact'])->toBe('正在准备服务模板产物。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_versions_and_releases_artifacts'])->toBe('正在准备 versions.json 和 releases.json 产物。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_releases_artifact'])->toBe('正在准备 releases.json 产物。')
        ->and($zhTranslations['sync_bunny']['info']['preparing_versions_artifact'])->toBe('正在准备 versions.json 产物。')
        ->and($zhTranslations['sync_bunny']['info']['no_artifact_changes_detected'])->toBe('未检测到产物变更。')
        ->and($zhTranslations['sync_bunny']['info']['artifacts_pushed_successfully'])->toBe('已成功将产物推送到 :repository。')
        ->and($zhTranslations['sync_bunny']['confirm']['sync_artifacts'])->toBe('确定要同步这些发布产物吗？')
        ->and($zhTranslations['sync_bunny']['confirm']['sync_service_template'])->toBe('确定要同步服务模板产物吗？')
        ->and($zhTranslations['sync_bunny']['confirm']['sync_versions_and_releases'])->toBe('确定要同步版本和发布元数据吗？')
        ->and($zhTranslations['sync_bunny']['confirm']['sync_releases_json'])->toBe('确定要同步 releases.json 吗？')
        ->and($zhTranslations['sync_bunny']['confirm']['sync_versions_json'])->toBe('确定要同步 versions.json 吗？')
        ->and($zhTranslations['sync_bunny']['kinds']['nightly'])->toBe('夜间版')
        ->and($zhTranslations['sync_bunny']['kinds']['production'])->toBe('正式版');
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
        ->and(trans('console.sync_bunny.confirm.sync_artifacts'))->toBe('Do you want to sync these artifacts?')
        ->and(trans('console.sync_bunny.confirm.sync_service_template'))->toBe('Do you want to sync the service template artifact?')
        ->and(trans('console.sync_bunny.confirm.sync_versions_and_releases'))->toBe('Do you want to sync versions and release metadata?')
        ->and(trans('console.sync_bunny.confirm.sync_releases_json'))->toBe('Do you want to sync releases.json?')
        ->and(trans('console.sync_bunny.confirm.sync_versions_json'))->toBe('Do you want to sync versions.json?')
        ->and(trans('console.sync_bunny.kinds.nightly'))->toBe('nightly')
        ->and(trans('console.sync_bunny.kinds.production'))->toBe('production')
        ->and((new SyncBunny)->getDescription())->toBe('Sync release artifacts to the GitHub Pages artifact repository');

    App::setLocale('zh_CN');

    expect(trans('console.sync_bunny.description'))->toBe('将发布产物同步到 GitHub Pages 产物仓库')
        ->and(trans('console.sync_bunny.info.preparing_artifacts', ['kind' => trans('console.sync_bunny.kinds.nightly'), 'repository' => 'loccen/coolify-zh-artifacts']))->toBe('正在为 loccen/coolify-zh-artifacts 准备夜间版发布产物。')
        ->and(trans('console.sync_bunny.info.preparing_service_template_artifact'))->toBe('正在准备服务模板产物。')
        ->and(trans('console.sync_bunny.info.preparing_versions_and_releases_artifacts'))->toBe('正在准备 versions.json 和 releases.json 产物。')
        ->and(trans('console.sync_bunny.info.preparing_releases_artifact'))->toBe('正在准备 releases.json 产物。')
        ->and(trans('console.sync_bunny.info.preparing_versions_artifact'))->toBe('正在准备 versions.json 产物。')
        ->and(trans('console.sync_bunny.info.no_artifact_changes_detected'))->toBe('未检测到产物变更。')
        ->and(trans('console.sync_bunny.info.artifacts_pushed_successfully', ['repository' => 'loccen/coolify-zh-artifacts']))->toBe('已成功将产物推送到 loccen/coolify-zh-artifacts。')
        ->and(trans('console.sync_bunny.confirm.sync_artifacts'))->toBe('确定要同步这些发布产物吗？')
        ->and(trans('console.sync_bunny.confirm.sync_service_template'))->toBe('确定要同步服务模板产物吗？')
        ->and(trans('console.sync_bunny.confirm.sync_versions_and_releases'))->toBe('确定要同步版本和发布元数据吗？')
        ->and(trans('console.sync_bunny.confirm.sync_releases_json'))->toBe('确定要同步 releases.json 吗？')
        ->and(trans('console.sync_bunny.confirm.sync_versions_json'))->toBe('确定要同步 versions.json 吗？')
        ->and(trans('console.sync_bunny.kinds.nightly'))->toBe('夜间版')
        ->and(trans('console.sync_bunny.kinds.production'))->toBe('正式版')
        ->and((new SyncBunny)->getDescription())->toBe('将发布产物同步到 GitHub Pages 产物仓库');
});
