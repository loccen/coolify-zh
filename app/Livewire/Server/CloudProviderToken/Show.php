<?php

namespace App\Livewire\Server\CloudProviderToken;

use App\Models\CloudProviderToken;
use App\Models\Server;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public Server $server;

    public $cloudProviderTokens = [];

    public $parameters = [];

    public function mount(string $server_uuid)
    {
        try {
            $this->server = Server::ownedByCurrentTeam()->whereUuid($server_uuid)->firstOrFail();
            $this->loadTokens();
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function getListeners()
    {
        return [
            'tokenAdded' => 'handleTokenAdded',
        ];
    }

    public function loadTokens()
    {
        $this->cloudProviderTokens = CloudProviderToken::ownedByCurrentTeam()
            ->where('provider', 'hetzner')
            ->get();
    }

    public function handleTokenAdded($tokenId)
    {
        $this->loadTokens();
    }

    public function setCloudProviderToken($tokenId)
    {
        $ownedToken = CloudProviderToken::ownedByCurrentTeam()->find($tokenId);
        if (is_null($ownedToken)) {
            $this->dispatch('error', __('server.toasts.hetzner_token_unauthorized'));

            return;
        }
        try {
            $this->authorize('update', $this->server);

            // Validate the token works and can access this specific server
            $validationResult = $this->validateTokenForServer($ownedToken);
            if (! $validationResult['valid']) {
                $this->dispatch('error', $validationResult['error']);

                return;
            }

            $this->server->cloudProviderToken()->associate($ownedToken);
            $this->server->save();
            $this->dispatch('success', __('server.toasts.hetzner_token_updated'));
            $this->dispatch('refreshServerShow');
        } catch (\Exception $e) {
            $this->server->refresh();
            $this->dispatch('error', $e->getMessage());
        }
    }

    private function validateTokenForServer(CloudProviderToken $token): array
    {
        try {
            // First, validate the token itself
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.$token->token,
            ])->timeout(10)->get('https://api.hetzner.cloud/v1/servers');

            if (! $response->successful()) {
                return [
                    'valid' => false,
                    'error' => __('server.toasts.hetzner_token_invalid_permissions'),
                ];
            }

            // Check if this token can access the specific Hetzner server
            if ($this->server->hetzner_server_id) {
                $serverResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer '.$token->token,
                ])->timeout(10)->get("https://api.hetzner.cloud/v1/servers/{$this->server->hetzner_server_id}");

                if (! $serverResponse->successful()) {
                    return [
                        'valid' => false,
                        'error' => __('server.toasts.hetzner_token_cannot_access_server'),
                    ];
                }
            }

            return ['valid' => true];
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'error' => __('server.toasts.hetzner_token_validation_failed', ['error' => $e->getMessage()]),
            ];
        }
    }

    public function validateToken()
    {
        try {
            $token = $this->server->cloudProviderToken;
            if (! $token) {
                $this->dispatch('error', __('server.toasts.hetzner_token_missing'));

                return;
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.$token->token,
            ])->timeout(10)->get('https://api.hetzner.cloud/v1/servers');

            if ($response->successful()) {
                $this->dispatch('success', __('server.toasts.hetzner_token_valid'));
            } else {
                $this->dispatch('error', __('server.toasts.hetzner_token_invalid_permissions'));
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.server.cloud-provider-token.show');
    }
}
