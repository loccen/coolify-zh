<?php

namespace App\Livewire\Server;

use App\Actions\Server\DeleteServer;
use App\Jobs\DeleteResourceJob;
use App\Models\Server;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Delete extends Component
{
    use AuthorizesRequests;

    public Server $server;

    public bool $delete_from_hetzner = false;

    public bool $force_delete_resources = false;

    public function mount(string $server_uuid)
    {
        try {
            $this->server = Server::ownedByCurrentTeam()->whereUuid($server_uuid)->firstOrFail();
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function delete($password, $selectedActions = [])
    {
        if (! verifyPasswordConfirmation($password, $this)) {
            return __('The provided password is incorrect.');
        }

        if (! empty($selectedActions)) {
            $this->delete_from_hetzner = in_array('delete_from_hetzner', $selectedActions);
            $this->force_delete_resources = in_array('force_delete_resources', $selectedActions);
        }
        try {
            $this->authorize('delete', $this->server);
            if ($this->server->hasDefinedResources() && ! $this->force_delete_resources) {
                $this->dispatch('error', __('server.toasts.delete_resources_first'));

                return;
            }

            if ($this->force_delete_resources) {
                foreach ($this->server->definedResources() as $resource) {
                    DeleteResourceJob::dispatch($resource);
                }
            }

            $this->server->delete();
            DeleteServer::dispatch(
                $this->server->id,
                $this->delete_from_hetzner,
                $this->server->hetzner_server_id,
                $this->server->cloud_provider_token_id,
                $this->server->team_id
            );

            return redirectRoute($this, 'server.index');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        $checkboxes = [];

        if ($this->server->hasDefinedResources()) {
            $resourceCount = $this->server->definedResources()->count();
            $checkboxes[] = [
                'id' => 'force_delete_resources',
                'label' => __('server.delete.force_delete_resources_label', ['count' => $resourceCount]),
                'default_warning' => __('server.delete.force_delete_resources_warning'),
            ];
        }

        if ($this->server->hetzner_server_id) {
            $checkboxes[] = [
                'id' => 'delete_from_hetzner',
                'label' => __('server.delete.delete_from_hetzner_label'),
                'default_warning' => __('server.delete.delete_from_hetzner_warning'),
            ];
        }

        return view('livewire.server.delete', [
            'checkboxes' => $checkboxes,
        ]);
    }
}
