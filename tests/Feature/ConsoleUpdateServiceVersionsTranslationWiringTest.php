<?php

use App\Console\Commands\UpdateServiceVersions;
use Illuminate\Support\Facades\App;

it('wires update service versions phase 1 console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/UpdateServiceVersions.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.update_service_versions.description'")
        ->toContain("trans('console.update_service_versions.info.starting'")
        ->toContain("trans('console.update_service_versions.info.processing'")
        ->toContain("trans('console.update_service_versions.warn.no_services_found'")
        ->toContain("trans('console.update_service_versions.info.image_updated'")
        ->toContain("trans('console.update_service_versions.info.image_up_to_date'")
        ->toContain("trans('console.update_service_versions.warn.dry_run_would_update_file'")
        ->toContain("trans('console.update_service_versions.error.failed'");

    expect($enTranslations['update_service_versions']['description'])->toBe('Update service template files with latest Docker image versions from registries')
        ->and($enTranslations['update_service_versions']['info']['starting'])->toBe('Starting service version update...')
        ->and($enTranslations['update_service_versions']['info']['processing'])->toBe('Processing: :filename')
        ->and($enTranslations['update_service_versions']['warn']['no_services_found'])->toBe('No services found in :filename')
        ->and($enTranslations['update_service_versions']['info']['image_updated'])->toBe(':service_name: :current_image -> :latest_version')
        ->and($enTranslations['update_service_versions']['info']['image_up_to_date'])->toBe(':service_name: :current_image (up to date)')
        ->and($enTranslations['update_service_versions']['warn']['dry_run_would_update_file'])->toBe('[DRY RUN] Would update this file')
        ->and($enTranslations['update_service_versions']['error']['failed'])->toBe('Failed: :message');

    expect($zhTranslations['update_service_versions']['description'])->toBe('使用镜像仓库中的最新 Docker 镜像版本更新服务模板文件')
        ->and($zhTranslations['update_service_versions']['info']['starting'])->toBe('正在开始更新服务版本...')
        ->and($zhTranslations['update_service_versions']['info']['processing'])->toBe('正在处理：:filename')
        ->and($zhTranslations['update_service_versions']['warn']['no_services_found'])->toBe(':filename 中未找到服务')
        ->and($zhTranslations['update_service_versions']['info']['image_updated'])->toBe(':service_name：:current_image -> :latest_version')
        ->and($zhTranslations['update_service_versions']['info']['image_up_to_date'])->toBe(':service_name：:current_image（已是最新）')
        ->and($zhTranslations['update_service_versions']['warn']['dry_run_would_update_file'])->toBe('[DRY RUN] 将更新此文件')
        ->and($zhTranslations['update_service_versions']['error']['failed'])->toBe('失败：:message');
});

it('resolves update service versions phase 1 translations in en and zh_CN', function () {
    App::setLocale('en');

    expect((new UpdateServiceVersions)->getDescription())->toBe('Update service template files with latest Docker image versions from registries')
        ->and(trans('console.update_service_versions.info.starting'))->toBe('Starting service version update...')
        ->and(trans('console.update_service_versions.info.processing', ['filename' => 'redis.yaml']))->toBe('Processing: redis.yaml')
        ->and(trans('console.update_service_versions.warn.no_services_found', ['filename' => 'redis.yaml']))->toBe('No services found in redis.yaml')
        ->and(trans('console.update_service_versions.info.image_updated', ['service_name' => 'app', 'current_image' => 'redis:7', 'latest_version' => 'redis:8']))->toBe('app: redis:7 -> redis:8')
        ->and(trans('console.update_service_versions.info.image_up_to_date', ['service_name' => 'app', 'current_image' => 'redis:8']))->toBe('app: redis:8 (up to date)')
        ->and(trans('console.update_service_versions.warn.dry_run_would_update_file'))->toBe('[DRY RUN] Would update this file')
        ->and(trans('console.update_service_versions.error.failed', ['message' => 'boom']))->toBe('Failed: boom');

    App::setLocale('zh_CN');

    expect((new UpdateServiceVersions)->getDescription())->toBe('使用镜像仓库中的最新 Docker 镜像版本更新服务模板文件')
        ->and(trans('console.update_service_versions.info.starting'))->toBe('正在开始更新服务版本...')
        ->and(trans('console.update_service_versions.info.processing', ['filename' => 'redis.yaml']))->toBe('正在处理：redis.yaml')
        ->and(trans('console.update_service_versions.warn.no_services_found', ['filename' => 'redis.yaml']))->toBe('redis.yaml 中未找到服务')
        ->and(trans('console.update_service_versions.info.image_updated', ['service_name' => 'app', 'current_image' => 'redis:7', 'latest_version' => 'redis:8']))->toBe('app：redis:7 -> redis:8')
        ->and(trans('console.update_service_versions.info.image_up_to_date', ['service_name' => 'app', 'current_image' => 'redis:8']))->toBe('app：redis:8（已是最新）')
        ->and(trans('console.update_service_versions.warn.dry_run_would_update_file'))->toBe('[DRY RUN] 将更新此文件')
        ->and(trans('console.update_service_versions.error.failed', ['message' => 'boom']))->toBe('失败：boom');
});
