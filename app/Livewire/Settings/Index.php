<?php

namespace App\Livewire\Settings;

use App\Models\InstanceSettings;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public InstanceSettings $settings;

    public ?Server $server = null;

    #[Validate('nullable|string|max:255|url')]
    public ?string $fqdn = null;

    #[Validate('required|integer|min:1025|max:65535')]
    public int $public_port_min;

    #[Validate('required|integer|min:1025|max:65535')]
    public int $public_port_max;

    #[Validate('nullable|string|max:255')]
    public ?string $instance_name = null;

    #[Validate('nullable|ipv4')]
    public ?string $public_ipv4 = null;

    #[Validate('nullable|ipv6')]
    public ?string $public_ipv6 = null;

    #[Validate('required|string|timezone')]
    public string $instance_timezone;

    #[Validate(['nullable', 'string', 'max:128', 'regex:/^[A-Za-z0-9_][A-Za-z0-9_.-]{0,127}$/'])]
    public ?string $dev_helper_version = null;

    public array $domainConflicts = [];

    public bool $showDomainConflictModal = false;

    public bool $forceSaveDomains = false;

    public $buildActivityId = null;

    protected function messages(): array
    {
        return [
            'fqdn.url' => __('settings.index.invalid_url'),
            'fqdn.max' => __('settings.index.url_too_long'),
            'dev_helper_version.regex' => __('settings.index.build_version_invalid'),
        ];
    }

    public function render()
    {
        return view('livewire.settings.index');
    }

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('dashboard');
        }
        $this->settings = instanceSettings();
        if (! isCloud()) {
            $this->server = Server::findOrFail(0);
        }
        $this->fqdn = $this->settings->fqdn;
        $this->public_port_min = $this->settings->public_port_min;
        $this->public_port_max = $this->settings->public_port_max;
        $this->instance_name = $this->settings->instance_name;
        $this->public_ipv4 = $this->settings->public_ipv4;
        $this->public_ipv6 = $this->settings->public_ipv6;
        $this->instance_timezone = $this->settings->instance_timezone;
        $this->dev_helper_version = $this->settings->dev_helper_version;
    }

    #[Computed]
    public function timezones(): array
    {
        return collect(timezone_identifiers_list())
            ->sort()
            ->values()
            ->toArray();
    }

    public function instantSave($isSave = true)
    {
        $this->validate();
        $this->settings->fqdn = $this->fqdn ? trim($this->fqdn) : $this->fqdn;
        $this->settings->public_port_min = $this->public_port_min;
        $this->settings->public_port_max = $this->public_port_max;
        $this->settings->instance_name = $this->instance_name;
        $this->settings->public_ipv4 = $this->public_ipv4;
        $this->settings->public_ipv6 = $this->public_ipv6;
        $this->settings->instance_timezone = $this->instance_timezone;
        $this->settings->dev_helper_version = $this->dev_helper_version;
        if ($isSave) {
            $this->settings->save();
            $this->dispatch('success', __('settings.saved'));
        }
    }

    public function confirmDomainUsage()
    {
        $this->forceSaveDomains = true;
        $this->showDomainConflictModal = false;
        $this->submit();
    }

    public function submit()
    {
        try {
            $error_show = false;
            $this->resetErrorBag();

            if (! validate_timezone($this->instance_timezone)) {
                $this->instance_timezone = config('app.timezone');
                throw new \Exception(__('settings.index.invalid_timezone'));
            } else {
                $this->settings->instance_timezone = $this->instance_timezone;
            }

            if ($this->settings->public_port_min > $this->settings->public_port_max) {
                $this->addError('settings.public_port_min', __('settings.index.port_range_invalid'));

                return;
            }

            // Trim FQDN to remove leading/trailing whitespace before validation
            if ($this->fqdn) {
                $this->fqdn = trim($this->fqdn);
            }

            $this->validate();

            if ($this->settings->is_dns_validation_enabled && $this->fqdn && $this->server) {
                if (! validateDNSEntry($this->fqdn, $this->server)) {
                    $this->dispatch('error', __('settings.index.dns_validation_failed', [
                        'fqdn' => $this->fqdn,
                        'ip' => $this->server->ip,
                        'documentation_url' => 'https://coolify.io/docs/knowledge-base/dns-configuration',
                    ]));
                    $error_show = true;
                }
            }
            if ($this->fqdn) {
                if (! $this->forceSaveDomains) {
                    $result = checkDomainUsage(domain: $this->fqdn);
                    if ($result['hasConflicts']) {
                        $this->domainConflicts = $result['conflicts'];
                        $this->showDomainConflictModal = true;

                        return;
                    }
                } else {
                    // Reset the force flag after using it
                    $this->forceSaveDomains = false;
                }
            }

            $this->instantSave(isSave: false);

            $this->settings->save();
            if ($this->server) {
                $this->server->setupDynamicProxyConfiguration();
            }
            if (! $error_show) {
                $this->dispatch('success', __('settings.instance_updated'));
            }
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function buildHelperImage()
    {
        try {
            if (! isDev()) {
                $this->dispatch('error', __('settings.index.build_helper_dev_only'));

                return;
            }

            if (! $this->server) {
                $this->dispatch('error', __('settings.index.server_unavailable'));

                return;
            }

            $this->validateOnly('dev_helper_version');

            $version = $this->dev_helper_version ?: config('constants.coolify.helper_version');
            if (empty($version)) {
                $this->dispatch('error', __('settings.index.build_version_required'));

                return;
            }

            if (! preg_match('/^[A-Za-z0-9_][A-Za-z0-9_.-]{0,127}$/', (string) $version)) {
                $this->dispatch('error', __('settings.index.build_version_invalid'));

                return;
            }

            $imageRef = escapeshellarg("ghcr.io/coollabsio/coolify-helper:{$version}");
            $buildCommand = "docker build -t {$imageRef} -f docker/coolify-helper/Dockerfile .";

            $activity = remote_process(
                command: [$buildCommand],
                server: $this->server,
                type: 'build-helper-image'
            );

            $this->buildActivityId = $activity->id;
            $this->dispatch('activityMonitor', $activity->id);

            $this->dispatch('success', __('settings.index.build_started', ['version' => $version]));
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }
}
