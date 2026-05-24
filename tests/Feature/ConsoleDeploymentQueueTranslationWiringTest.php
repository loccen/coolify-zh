<?php

it('wires deployment queue console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/CheckApplicationDeploymentQueue.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.application_deployment_queue.description'")
        ->toContain("trans('console.application_deployment_queue.info.no_deployments_found'")
        ->toContain("trans('console.application_deployment_queue.info.deployments_found'")
        ->toContain("trans('console.application_deployment_queue.info.deployment_is_stale'")
        ->toContain("trans('console.application_deployment_queue.confirm.cancel_deployment'");

    expect($enTranslations['application_deployment_queue']['description'])->toBe('Check application deployment queue')
        ->and($enTranslations['application_deployment_queue']['info']['no_deployments_found'])->toBe('No deployments found in the last :seconds seconds.')
        ->and($enTranslations['application_deployment_queue']['info']['deployments_found'])->toBe('Found :count deployments created in the last :seconds seconds.')
        ->and($enTranslations['application_deployment_queue']['info']['deployment_is_stale'])->toBe('Deployment :deployment_id created at :created_at is older than :seconds seconds. Setting status to failed.')
        ->and($enTranslations['application_deployment_queue']['confirm']['cancel_deployment'])->toBe('Do you want to cancel deployment :deployment_id created at :created_at?');

    expect($zhTranslations['application_deployment_queue']['description'])->toBe('检查应用部署队列')
        ->and($zhTranslations['application_deployment_queue']['info']['no_deployments_found'])->toBe('最近 :seconds 秒内未找到部署。')
        ->and($zhTranslations['application_deployment_queue']['info']['deployments_found'])->toBe('找到 :count 个在最近 :seconds 秒内创建的部署。')
        ->and($zhTranslations['application_deployment_queue']['info']['deployment_is_stale'])->toBe('部署 :deployment_id 创建于 :created_at，已超过 :seconds 秒。正在将状态设为失败。')
        ->and($zhTranslations['application_deployment_queue']['confirm']['cancel_deployment'])->toBe('要取消部署 :deployment_id（创建于 :created_at）吗？');
});
