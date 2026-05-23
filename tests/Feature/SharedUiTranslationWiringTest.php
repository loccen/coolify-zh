<?php

use Illuminate\Support\Facades\App;

it('wires translation calls in shared ui follow-up files', function () {
    $componentsRoot = __DIR__.'/../../resources/views/components';
    $livewireRoot = __DIR__.'/../../resources/views/livewire';

    $modalConfirmation = file_get_contents($componentsRoot.'/modal-confirmation.blade.php');
    $domainConflictModal = file_get_contents($componentsRoot.'/domain-conflict-modal.blade.php');
    $statusRunning = file_get_contents($componentsRoot.'/status/running.blade.php');
    $storageResources = file_get_contents($livewireRoot.'/storage/resources.blade.php');
    $destinationNewDocker = file_get_contents($livewireRoot.'/destination/new/docker.blade.php');
    $globalSearch = file_get_contents($livewireRoot.'/global-search.blade.php');
    $serverProxy = file_get_contents($livewireRoot.'/server/proxy.blade.php');
    $serverSwarm = file_get_contents($livewireRoot.'/server/swarm.blade.php');

    expect($modalConfirmation)
        ->toContain("__('Password is required.')")
        ->toContain("__('Your Password')")
        ->and($domainConflictModal)
        ->toContain("__('I understand, proceed anyway')")
        ->and($statusRunning)
        ->toContain("__('No health check configured.")
        ->toContain("__('Unhealthy state.")
        ->and($storageResources)
        ->toContain("__('Deleted database')")
        ->toContain("__('Disable S3')")
        ->and($destinationNewDocker)
        ->toContain("__('Destinations are used to segregate resources by network.')")
        ->toContain("__('Select a server')")
        ->and($globalSearch)
        ->toContain("__('New Team')")
        ->toContain("__('New Private Key')")
        ->toContain("__('New GitHub App')")
        ->and($serverProxy)
        ->toContain("__('Custom proxy configurations may be reset to their default settings.')")
        ->toContain("__('Test the upgrade in a non-production environment first.')")
        ->and($serverSwarm)
        ->toContain("__('Read the docs <a class=\\'underline dark:text-white\\' href=\\'https://coolify.io/docs/knowledge-base/docker/swarm\\' target=\\'_blank\\'>here</a>.')");
});

it('resolves representative shared ui follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Deleted database'))->toBe('已删除的数据库')
        ->and(__('Disable S3'))->toBe('禁用 S3')
        ->and(__('Password is required.'))->toBe('必须输入密码。')
        ->and(__('Your Password'))->toBe('你的密码')
        ->and(__('Select a server'))->toBe('选择服务器')
        ->and(__('No deployments running.'))->toBe('没有正在运行的部署。')
        ->and(__('configuration changes'))->toBe('项配置变更')
        ->and(__('Rebuild'))->toBe('重新构建')
        ->and(__('Custom (None)'))->toBe('自定义（无）')
        ->and(__('Read the docs <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/swarm\' target=\'_blank\'>here</a>.'))->toBe('请在<a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/swarm\' target=\'_blank\'>这里</a>查看文档。');
});
