<?php

use Illuminate\Support\Facades\App;

it('uses explicit translation lookups in follow-up i18n views', function () {
    $viewsRoot = base_path('resources/views');

    $sentinel = file_get_contents($viewsRoot.'/livewire/server/sentinel.blade.php');
    $caCertificate = file_get_contents($viewsRoot.'/livewire/server/ca-certificate/show.blade.php');
    $dockerCleanup = file_get_contents($viewsRoot.'/livewire/server/docker-cleanup.blade.php');
    $proxy = file_get_contents($viewsRoot.'/livewire/server/proxy.blade.php');
    $proxyDynamicConfigurations = file_get_contents($viewsRoot.'/livewire/server/proxy/dynamic-configurations.blade.php');
    $proxyDynamicConfigurationNavbar = file_get_contents($viewsRoot.'/livewire/server/proxy/dynamic-configuration-navbar.blade.php');
    $proxyNewDynamicConfiguration = file_get_contents($viewsRoot.'/livewire/server/proxy/new-dynamic-configuration.blade.php');
    $executeContainerCommand = file_get_contents($viewsRoot.'/livewire/project/shared/execute-container-command.blade.php');
    $environmentVariableAdd = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/add.blade.php');
    $environmentVariableAll = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/all.blade.php');
    $backupEdit = file_get_contents($viewsRoot.'/livewire/project/database/backup-edit.blade.php');
    $backupNow = file_get_contents($viewsRoot.'/livewire/project/database/backup-now.blade.php');
    $dockerImage = file_get_contents($viewsRoot.'/livewire/project/new/docker-image.blade.php');
    $simpleDockerfile = file_get_contents($viewsRoot.'/livewire/project/new/simple-dockerfile.blade.php');
    $publicGitRepository = file_get_contents($viewsRoot.'/livewire/project/new/public-git-repository.blade.php');
    $githubPrivateRepository = file_get_contents($viewsRoot.'/livewire/project/new/github-private-repository.blade.php');
    $deployKeyRepository = file_get_contents($viewsRoot.'/livewire/project/new/github-private-repository-deploy-key.blade.php');
    $dockerCompose = file_get_contents($viewsRoot.'/livewire/project/new/docker-compose.blade.php');
    $newResourceSelect = file_get_contents($viewsRoot.'/livewire/project/new/select.blade.php');
    $projectAddEmpty = file_get_contents($viewsRoot.'/livewire/project/add-empty.blade.php');
    $deleteEnvironment = file_get_contents($viewsRoot.'/livewire/project/delete-environment.blade.php');
    $applicationAdvanced = file_get_contents($viewsRoot.'/livewire/project/application/advanced.blade.php');
    $environmentVariableShow = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/show.blade.php');
    $environmentVariableShowHardcoded = file_get_contents($viewsRoot.'/livewire/project/shared/environment-variable/show-hardcoded.blade.php');

    expect($sentinel)
        ->toContain("{{ __('Sentinel') }}")
        ->toContain("__('Enable Sentinel')")
        ->toContain("__('Metrics rate (seconds)')")
        ->and($caCertificate)
        ->toContain("{{ __('CA Certificate') }}")
        ->toContain("__('Recommended Configuration:')")
        ->toContain("__('CERTIFICATE CONTENT')")
        ->and($dockerCleanup)
        ->toContain("{{ __('Docker Cleanup') }}")
        ->toContain("__('Force Docker Cleanup')")
        ->toContain("__('Recent executions')")
        ->and($proxy)
        ->toContain("{{ __('Configuration') }}")
        ->toContain("__('Switch Proxy')")
        ->toContain("__('Configuration file ( :path )'")
        ->and($proxyDynamicConfigurations)
        ->toContain("{{ __('Proxy Dynamic Configuration') }}")
        ->toContain("__('Loading dynamic configurations...')")
        ->and($proxyDynamicConfigurationNavbar)
        ->toContain("{{ __('File:') }}")
        ->toContain("__('Edit')")
        ->and($proxyNewDynamicConfiguration)
        ->toContain("__('Filename')")
        ->and($executeContainerCommand)
        ->toContain("{{ __('Terminal') }}")
        ->toContain("__('Container')")
        ->and($environmentVariableAdd)
        ->toContain("__('Comment')")
        ->toContain("__('Is Multiline?')")
        ->toContain("__('Tip: Type')")
        ->toContain('&#123;&#123;')
        ->and($environmentVariableAll)
        ->toContain("__('Environment Variables')")
        ->toContain("__('Production Environment Variables')")
        ->toContain("__('Save All Environment Variables')")
        ->and($backupEdit)
        ->toContain("{{ __('Scheduled Backup') }}")
        ->toContain("__('Backup Retention Settings')")
        ->toContain("__('S3 Enabled')")
        ->and($backupNow)
        ->toContain("{{ __('Backup Now') }}")
        ->and($dockerImage)
        ->toContain("{{ __('Create a new Application') }}")
        ->toContain("__('Image Name')")
        ->toContain("__('SHA256 Digest (optional)')")
        ->and($simpleDockerfile)
        ->toContain("{{ __('Create a new Application') }}")
        ->toContain("{{ __('Dockerfile') }}")
        ->and($publicGitRepository)
        ->toContain("{{ __('Deploy any public Git repositories.') }}")
        ->toContain("{{ __('Check repository') }}")
        ->and($githubPrivateRepository)
        ->toContain("{{ __('Deploy any public or private Git repositories through a GitHub App.') }}")
        ->toContain("{{ __('Refresh Repository List') }}")
        ->and($deployKeyRepository)
        ->toContain("{{ __('Deploy any public or private Git repositories through a Deploy Key.') }}")
        ->toContain("{{ __('Create a new private key') }}")
        ->and($dockerCompose)
        ->toContain("{{ __('Create a new Service') }}")
        ->toContain("label=\"{{ __('Docker Compose file') }}\"")
        ->and($newResourceSelect)
        ->toContain("{{ __('Select a server') }}")
        ->toContain("{{ __('Select a destination') }}")
        ->and($projectAddEmpty)
        ->toContain("{{ __('Continue') }}")
        ->toContain("__('New project will have a default :environment environment.'")
        ->and($deleteEnvironment)
        ->toContain("title=\"{{ __('Confirm Environment Deletion?') }}\"")
        ->toContain("buttonTitle=\"{{ __('Delete Environment') }}\"")
        ->and($applicationAdvanced)
        ->toContain("{{ __('Advanced') }}")
        ->toContain("__('Disable Build Cache')")
        ->toContain("__('Operations')")
        ->and($environmentVariableShow)
        ->toContain("__('Comment')")
        ->toContain("__('Environment Variable Name')")
        ->toContain("__('Is Literal?')")
        ->and($environmentVariableShowHardcoded)
        ->toContain("__('Hardcoded env')")
        ->toContain("__('Service:')")
        ->toContain("__('Documentation for this environment variable.')");
});

it('resolves representative follow-up translations in zh_CN', function () {
    App::setLocale('zh_CN');

    expect(__('Comment'))->toBe('备注')
        ->and(__('Image Name'))->toBe('镜像名称')
        ->and(__('Public Repository'))->toBe('公开仓库')
        ->and(__('Private Repository (with GitHub App)'))->toBe('私有仓库（使用 GitHub App）')
        ->and(__('Private Repository (with Deploy Key)'))->toBe('私有仓库（使用 Deploy Key）')
        ->and(__('Docker Compose Empty'))->toBe('空白 Docker Compose')
        ->and(__('Check repository'))->toBe('检查仓库')
        ->and(__('Refresh Repository List'))->toBe('刷新仓库列表')
        ->and(__('Delete Environment'))->toBe('删除环境')
        ->and(__('Permanently Delete'))->toBe('永久删除')
        ->and(__('Is Multiline?'))->toBe('多行值？')
        ->and(__('Tip: Type'))->toBe('提示：输入')
        ->and(__('to reference a shared environment variable'))->toBe('可引用共享环境变量')
        ->and(__('Commands'))->toBe('命令')
        ->and(__('Backup Now'))->toBe('立即备份')
        ->and(__('Proxy Dynamic Configuration'))->toBe('代理动态配置')
        ->and(__('File:'))->toBe('文件：')
        ->and(__('In sync'))->toBe('已同步')
        ->and(__('Out of sync'))->toBe('未同步')
        ->and(__('Build'))->toBe('构建')
        ->and(__('Operations'))->toBe('操作')
        ->and(__('Environment Variable Name'))->toBe('环境变量名称')
        ->and(__('Environment Variables'))->toBe('环境变量')
        ->and(__('Preview Deployments Environment Variables'))->toBe('Preview Deployments 环境变量')
        ->and(__('Production Environment Variables'))->toBe('生产环境变量')
        ->and(__('Save All Environment Variables'))->toBe('保存所有环境变量')
        ->and(__('Hardcoded env'))->toBe('硬编码环境变量')
        ->and(__('Service:'))->toBe('服务：')
        ->and(__('(inherited from host)'))->toBe('（继承自主机）')
        ->and(__('Documentation for this environment variable.'))->toBe('这个环境变量的说明。')
        ->and(__('settings.instance_updated'))->toBe('设置已更新。')
        ->and(trans('settings.instance_updated'))->toBe('设置已更新。');
});
