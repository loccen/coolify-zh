<?php

use App\Console\Commands\UpdateServiceVersions;
use Illuminate\Support\Facades\App;

it('wires update service versions phase 1 and phase 2 console strings through console translations', function () {
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
        ->toContain("trans('console.update_service_versions.info.manual_review'")
        ->toContain("trans('console.update_service_versions.info.using_cached_tags'")
        ->toContain("trans('console.update_service_versions.info.latest_points_to'")
        ->toContain("trans('console.update_service_versions.warn.dry_run_would_update_file'")
        ->toContain("trans('console.update_service_versions.warn.using_latest_tag'")
        ->toContain("trans('console.update_service_versions.warn.skipping_custom_registry'")
        ->toContain("trans('console.update_service_versions.warn.registry_api_error'")
        ->toContain("trans('console.update_service_versions.warn.ghcr_requires_authentication'")
        ->toContain("trans('console.update_service_versions.error.failed'");

    expect($enTranslations['update_service_versions']['description'])->toBe('Update service template files with latest Docker image versions from registries')
        ->and($enTranslations['update_service_versions']['info']['starting'])->toBe('Starting service version update...')
        ->and($enTranslations['update_service_versions']['info']['processing'])->toBe('Processing: :filename')
        ->and($enTranslations['update_service_versions']['warn']['no_services_found'])->toBe('No services found in :filename')
        ->and($enTranslations['update_service_versions']['info']['image_updated'])->toBe(':service_name: :current_image -> :latest_version')
        ->and($enTranslations['update_service_versions']['info']['image_up_to_date'])->toBe(':service_name: :current_image (up to date)')
        ->and($enTranslations['update_service_versions']['info']['manual_review'])->toBe('Manual review: :registry_url')
        ->and($enTranslations['update_service_versions']['info']['using_cached_tags'])->toBe('[cached] Using cached tags for :repository')
        ->and($enTranslations['update_service_versions']['info']['latest_points_to'])->toBe("Found 'latest' points to: :best_version")
        ->and($enTranslations['update_service_versions']['warn']['dry_run_would_update_file'])->toBe('[DRY RUN] Would update this file')
        ->and($enTranslations['update_service_versions']['warn']['using_latest_tag'])->toBe(":service_name: :current_image (using 'latest' tag)")
        ->and($enTranslations['update_service_versions']['warn']['skipping_custom_registry'])->toBe('Skipping custom registry: :repository')
        ->and($enTranslations['update_service_versions']['warn']['registry_api_error'])->toBe(':registry API error for :repository: :message')
        ->and($enTranslations['update_service_versions']['warn']['ghcr_requires_authentication'])->toBe('GHCR requires authentication - manual review needed')
        ->and($enTranslations['update_service_versions']['error']['failed'])->toBe('Failed: :message');

    expect($zhTranslations['update_service_versions']['description'])->toBe('使用镜像仓库中的最新 Docker 镜像版本更新服务模板文件')
        ->and($zhTranslations['update_service_versions']['info']['starting'])->toBe('正在开始更新服务版本...')
        ->and($zhTranslations['update_service_versions']['info']['processing'])->toBe('正在处理：:filename')
        ->and($zhTranslations['update_service_versions']['warn']['no_services_found'])->toBe(':filename 中未找到服务')
        ->and($zhTranslations['update_service_versions']['info']['image_updated'])->toBe(':service_name：:current_image -> :latest_version')
        ->and($zhTranslations['update_service_versions']['info']['image_up_to_date'])->toBe(':service_name：:current_image（已是最新）')
        ->and($zhTranslations['update_service_versions']['info']['manual_review'])->toBe('手动检查：:registry_url')
        ->and($zhTranslations['update_service_versions']['info']['using_cached_tags'])->toBe('[cached] 正在使用 :repository 的缓存标签')
        ->and($zhTranslations['update_service_versions']['info']['latest_points_to'])->toBe("发现 'latest' 指向：:best_version")
        ->and($zhTranslations['update_service_versions']['warn']['dry_run_would_update_file'])->toBe('[DRY RUN] 将更新此文件')
        ->and($zhTranslations['update_service_versions']['warn']['using_latest_tag'])->toBe(":service_name：:current_image（使用 'latest' 标签）")
        ->and($zhTranslations['update_service_versions']['warn']['skipping_custom_registry'])->toBe('跳过自定义镜像仓库：:repository')
        ->and($zhTranslations['update_service_versions']['warn']['registry_api_error'])->toBe(':registry API 错误，仓库 :repository：:message')
        ->and($zhTranslations['update_service_versions']['warn']['ghcr_requires_authentication'])->toBe('GHCR 需要认证，需要手动检查')
        ->and($zhTranslations['update_service_versions']['error']['failed'])->toBe('失败：:message');
});

it('resolves update service versions phase 1 and phase 2 translations in en and zh_CN', function () {
    App::setLocale('en');

    expect((new UpdateServiceVersions)->getDescription())->toBe('Update service template files with latest Docker image versions from registries')
        ->and(trans('console.update_service_versions.info.starting'))->toBe('Starting service version update...')
        ->and(trans('console.update_service_versions.info.processing', ['filename' => 'redis.yaml']))->toBe('Processing: redis.yaml')
        ->and(trans('console.update_service_versions.warn.no_services_found', ['filename' => 'redis.yaml']))->toBe('No services found in redis.yaml')
        ->and(trans('console.update_service_versions.info.image_updated', ['service_name' => 'app', 'current_image' => 'redis:7', 'latest_version' => 'redis:8']))->toBe('app: redis:7 -> redis:8')
        ->and(trans('console.update_service_versions.info.image_up_to_date', ['service_name' => 'app', 'current_image' => 'redis:8']))->toBe('app: redis:8 (up to date)')
        ->and(trans('console.update_service_versions.info.manual_review', ['registry_url' => 'https://example.test/tags']))->toBe('Manual review: https://example.test/tags')
        ->and(trans('console.update_service_versions.info.using_cached_tags', ['repository' => 'ghcr.io/demo/image']))->toBe('[cached] Using cached tags for ghcr.io/demo/image')
        ->and(trans('console.update_service_versions.info.latest_points_to', ['best_version' => '1.2']))->toBe("Found 'latest' points to: 1.2")
        ->and(trans('console.update_service_versions.warn.dry_run_would_update_file'))->toBe('[DRY RUN] Would update this file')
        ->and(trans('console.update_service_versions.warn.using_latest_tag', ['service_name' => 'app', 'current_image' => 'redis:latest']))->toBe("app: redis:latest (using 'latest' tag)")
        ->and(trans('console.update_service_versions.warn.skipping_custom_registry', ['repository' => 'gcr.io/demo/image']))->toBe('Skipping custom registry: gcr.io/demo/image')
        ->and(trans('console.update_service_versions.warn.registry_api_error', ['registry' => 'DockerHub', 'repository' => 'redis', 'message' => 'boom']))->toBe('DockerHub API error for redis: boom')
        ->and(trans('console.update_service_versions.warn.ghcr_requires_authentication'))->toBe('GHCR requires authentication - manual review needed')
        ->and(trans('console.update_service_versions.error.failed', ['message' => 'boom']))->toBe('Failed: boom');

    App::setLocale('zh_CN');

    expect((new UpdateServiceVersions)->getDescription())->toBe('使用镜像仓库中的最新 Docker 镜像版本更新服务模板文件')
        ->and(trans('console.update_service_versions.info.starting'))->toBe('正在开始更新服务版本...')
        ->and(trans('console.update_service_versions.info.processing', ['filename' => 'redis.yaml']))->toBe('正在处理：redis.yaml')
        ->and(trans('console.update_service_versions.warn.no_services_found', ['filename' => 'redis.yaml']))->toBe('redis.yaml 中未找到服务')
        ->and(trans('console.update_service_versions.info.image_updated', ['service_name' => 'app', 'current_image' => 'redis:7', 'latest_version' => 'redis:8']))->toBe('app：redis:7 -> redis:8')
        ->and(trans('console.update_service_versions.info.image_up_to_date', ['service_name' => 'app', 'current_image' => 'redis:8']))->toBe('app：redis:8（已是最新）')
        ->and(trans('console.update_service_versions.info.manual_review', ['registry_url' => 'https://example.test/tags']))->toBe('手动检查：https://example.test/tags')
        ->and(trans('console.update_service_versions.info.using_cached_tags', ['repository' => 'ghcr.io/demo/image']))->toBe('[cached] 正在使用 ghcr.io/demo/image 的缓存标签')
        ->and(trans('console.update_service_versions.info.latest_points_to', ['best_version' => '1.2']))->toBe("发现 'latest' 指向：1.2")
        ->and(trans('console.update_service_versions.warn.dry_run_would_update_file'))->toBe('[DRY RUN] 将更新此文件')
        ->and(trans('console.update_service_versions.warn.using_latest_tag', ['service_name' => 'app', 'current_image' => 'redis:latest']))->toBe("app：redis:latest（使用 'latest' 标签）")
        ->and(trans('console.update_service_versions.warn.skipping_custom_registry', ['repository' => 'gcr.io/demo/image']))->toBe('跳过自定义镜像仓库：gcr.io/demo/image')
        ->and(trans('console.update_service_versions.warn.registry_api_error', ['registry' => 'DockerHub', 'repository' => 'redis', 'message' => 'boom']))->toBe('DockerHub API 错误，仓库 redis：boom')
        ->and(trans('console.update_service_versions.warn.ghcr_requires_authentication'))->toBe('GHCR 需要认证，需要手动检查')
        ->and(trans('console.update_service_versions.error.failed', ['message' => 'boom']))->toBe('失败：boom');
});
