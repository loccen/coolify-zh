<?php

use App\Models\InstanceSettings;
use App\Models\S3Storage;
use App\Models\Server;
use App\Models\StandaloneDocker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::forceCreate(['id' => 0]);

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag);
    view()->share('errors', $errors);
});

it('uses explicit translation lookups in the static UI views under scope', function () {
    $viewsRoot = base_path('resources/views/livewire');

    $dashboard = file_get_contents($viewsRoot.'/dashboard.blade.php');
    $admin = file_get_contents($viewsRoot.'/admin/index.blade.php');
    $tags = file_get_contents($viewsRoot.'/tags/show.blade.php');
    $terminal = file_get_contents($viewsRoot.'/terminal/index.blade.php');
    $storageShow = file_get_contents($viewsRoot.'/storage/show.blade.php');
    $storageForm = file_get_contents($viewsRoot.'/storage/form.blade.php');
    $storageResources = file_get_contents($viewsRoot.'/storage/resources.blade.php');
    $destination = file_get_contents($viewsRoot.'/destination/show.blade.php');
    $tagDeployments = file_get_contents($viewsRoot.'/tags/deployments.blade.php');

    expect($dashboard)
        ->toContain("__('dashboard.subtitle')")
        ->toContain("__('dashboard.projects.empty.title')")
        ->toContain("__('dashboard.private_keys.empty.title')")
        ->toContain("__('dashboard.servers.not_usable')")
        ->and($admin)
        ->toContain("__('admin.title')")
        ->toContain("__('admin.search_placeholder')")
        ->toContain("__('admin.no_users_found'")
        ->and($tags)
        ->toContain("__('tags.subtitle')")
        ->toContain("__('tags.redeploy_all.title')")
        ->toContain("__('tags.redeploy_all.short_confirmation_label')")
        ->and($terminal)
        ->toContain("__('terminal.subtitle')")
        ->toContain("__('terminal.help.connection'")
        ->toContain("__('terminal.loading_containers')")
        ->and($storageShow)
        ->toContain("__('storage.title')")
        ->toContain("__('storage.delete_confirmation.title')")
        ->and($storageForm)
        ->toContain("__('storage.fields.access_key')")
        ->toContain("__('storage.fields.secret_key')")
        ->toContain("__('storage.validate_connection')")
        ->and($storageResources)
        ->toContain("__('Enabled')")
        ->toContain("__('Disable S3')")
        ->and($tagDeployments)
        ->toContain("__('In Progress')")
        ->toContain("__('Queued')")
        ->toContain("__('Success')")
        ->toContain("__('Failed')")
        ->toContain("__('Cancelled')")
        ->and($destination)
        ->toContain("__('destination.subtitle.simple')")
        ->toContain("__('destination.fields.server_ip')")
        ->toContain("__('destination.fields.docker_network')");
});

it('resolves representative zh CN translations for the static UI scope', function () {
    App::setLocale('zh_CN');

    expect(__('dashboard.subtitle'))->toBe('你的自托管基础设施。')
        ->and(__('admin.search_placeholder'))->toBe('搜索用户')
        ->and(__('tags.details'))->toBe('标签详情')
        ->and(__('terminal.loading_containers'))->toBe('正在加载服务器和容器...')
        ->and(__('storage.fields.name'))->toBe('名称')
        ->and(__('storage.fields.endpoint'))->toBe('端点')
        ->and(__('storage.fields.secret_key'))->toBe('Secret Key')
        ->and(__('Secret Key'))->toBe('Secret Key')
        ->and(__('destination.fields.docker_network'))->toBe('Docker 网络')
        ->and(__('Docker Network'))->toBe('Docker 网络');
});

it('renders representative translated static UI fragments in zh CN', function () {
    App::setLocale('zh_CN');

    $user = User::factory()->create([
        'name' => '翻译用户',
        'email' => 'translation@example.com',
    ]);
    $this->actingAs($user);

    $adminHtml = view('livewire.admin.index', [
        'activeSubscribers' => 3,
        'inactiveSubscribers' => 1,
        'foundUsers' => collect(),
        'search' => '',
    ])->render();

    $tagsHtml = view('livewire.tags.show', [
        'tags' => collect(),
        'tag' => null,
    ])->render();

    $destination = StandaloneDocker::make([
        'name' => '生产网络',
        'network' => 'coolify-prod',
    ]);
    $destination->setRelation('server', Server::factory()->make([
        'team_id' => $user->teams->firstOrFail()->id,
    ]));

    $storageFormHtml = view('livewire.storage.form', [
        'storage' => S3Storage::make([
            'name' => '对象存储',
            'description' => 'desc',
            'endpoint' => 'https://s3.example.com',
            'bucket' => 'coolify',
            'region' => 'ap-east-1',
            'key' => 'test-key',
            'secret' => 'test-secret',
        ]),
    ])->render();

    $tagDeploymentsHtml = view('livewire.tags.deployments', [
        'deploymentsPerTagPerServer' => [
            'server-a' => [
                [
                    'application_name' => 'app-1',
                    'deployment_url' => '/deployments/1',
                    'status' => 'queued',
                ],
                [
                    'application_name' => 'app-2',
                    'deployment_url' => '/deployments/2',
                    'status' => 'in_progress',
                ],
                [
                    'application_name' => 'app-3',
                    'deployment_url' => '/deployments/3',
                    'status' => 'finished',
                ],
                [
                    'application_name' => 'app-4',
                    'deployment_url' => '/deployments/4',
                    'status' => 'failed',
                ],
                [
                    'application_name' => 'app-5',
                    'deployment_url' => '/deployments/5',
                    'status' => 'cancelled-by-user',
                ],
            ],
        ],
    ])->render();

    $destinationHtml = view('livewire.destination.show', [
        'destination' => $destination,
        'network' => 'coolify-prod',
    ])->render();

    expect($adminHtml)
        ->toContain('管理后台')
        ->toContain('搜索用户')
        ->toContain('有效订阅数')
        ->and($tagsHtml)
        ->toContain('标签可用于对多个资源执行操作。')
        ->toContain('还没有定义标签。请先到某个资源里添加标签。')
        ->and($storageFormHtml)
        ->toContain('名称')
        ->toContain('端点')
        ->toContain('访问密钥')
        ->toContain('Secret Key')
        ->and($tagDeploymentsHtml)
        ->toContain('排队中')
        ->toContain('进行中')
        ->toContain('成功')
        ->toContain('失败')
        ->toContain('已取消')
        ->not->toContain('Queued')
        ->not->toContain('In Progress')
        ->and($destinationHtml)
        ->toContain('一个简单的 Docker 网络。')
        ->toContain('服务器 IP')
        ->toContain('Docker 网络');
});
