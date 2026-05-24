<?php

namespace App\Livewire\Server;

use App\Models\Server;
use App\Support\ValidationPatterns;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Resources extends Component
{
    use AuthorizesRequests;

    public ?Server $server = null;

    public $parameters = [];

    public array $unmanagedContainers = [];

    public $activeTab = 'managed';

    public function getListeners()
    {
        $teamId = auth()->user()->currentTeam()->id;

        return [
            "echo-private:team.{$teamId},ApplicationStatusChanged" => 'refreshStatus',
        ];
    }

    public function startUnmanaged($id)
    {
        if (! ValidationPatterns::isValidContainerName($id)) {
            $this->dispatch('error', __('server.toasts.invalid_container_identifier'));

            return;
        }
        $this->server->startUnmanaged($id);
        $this->dispatch('success', __('server.toasts.container_started'));
        $this->loadUnmanagedContainers();
    }

    public function restartUnmanaged($id)
    {
        if (! ValidationPatterns::isValidContainerName($id)) {
            $this->dispatch('error', __('server.toasts.invalid_container_identifier'));

            return;
        }
        $this->server->restartUnmanaged($id);
        $this->dispatch('success', __('server.toasts.container_restarted'));
        $this->loadUnmanagedContainers();
    }

    public function stopUnmanaged($id)
    {
        if (! ValidationPatterns::isValidContainerName($id)) {
            $this->dispatch('error', __('server.toasts.invalid_container_identifier'));

            return;
        }
        $this->server->stopUnmanaged($id);
        $this->dispatch('success', __('server.toasts.container_stopped'));
        $this->loadUnmanagedContainers();
    }

    public function refreshStatus()
    {
        $this->server->refresh();
        if ($this->activeTab === 'managed') {
            $this->loadManagedContainers();
        } else {
            $this->loadUnmanagedContainers();
        }
        $this->dispatch('success', __('server.toasts.resource_statuses_refreshed'));
    }

    public function loadManagedContainers()
    {
        try {
            $this->activeTab = 'managed';
            $this->server->refresh();
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function loadUnmanagedContainers()
    {
        $this->activeTab = 'unmanaged';
        try {
            $this->unmanagedContainers = $this->server->loadUnmanagedContainers()->toArray();
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function mount()
    {
        $this->parameters = get_route_parameters();
        try {
            $this->server = Server::ownedByCurrentTeam()->whereUuid(request()->server_uuid)->first();
            if (is_null($this->server)) {
                return redirect()->route('server.index');
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.server.resources');
    }
}
