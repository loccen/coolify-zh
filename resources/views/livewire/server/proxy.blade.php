@php use App\Enums\ProxyTypes; @endphp
<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Proxy Configuration') }} | Coolify
    </x-slot>
    @php
        $proxySwitchWarningMessage = __("This operation may cause issues. Please refer to the guide <a href='https://coolify.io/docs/knowledge-base/server/proxies#switch-between-proxies' target='_blank' class='underline text-white'>switching between proxies</a> before proceeding!");
    @endphp
    @if ($server->proxyType())
        <div>
            @if ($selectedProxy !== 'NONE')
                <form wire:submit='submit'>
                    <div class="flex items-center gap-2">
                        <h2>{{ __('Configuration') }}</h2>
                        @if ($server->proxy->status === 'exited' || $server->proxy->status === 'removing')
                            @can('update', $server)
                                <x-modal-confirmation :title="__('Confirm Proxy Switching?')" :buttonTitle="__('Switch Proxy')"
                                    submitAction="changeProxy" :actions="[__('Custom proxy configurations may be reset to their default settings.')]"
                                    :warningMessage="$proxySwitchWarningMessage"
                                    :step2ButtonText="__('Switch Proxy')" :confirmWithText="false" :confirmWithPassword="false">
                                </x-modal-confirmation>
                            @endcan
                        @else
                            <x-forms.button canGate="update" :canResource="$server"
                                wire:click="$dispatch('error', @js(__('Currently running proxy must be stopped before switching proxy')))">{{ __('Switch Proxy') }}</x-forms.button>
                        @endif
                        <x-forms.button canGate="update" :canResource="$server" type="submit">{{ __('Save') }}</x-forms.button>
                    </div>
                    <div class="pb-4">{{ __('Configure your proxy settings and advanced options.') }}</div>
                    @if (
                        $server->proxy->last_applied_settings &&
                            $server->proxy->last_saved_settings !== $server->proxy->last_applied_settings)
                        <x-callout type="warning" :title="__('Configuration Out of Sync')" class="my-4">
                            {{ __('The saved proxy configuration differs from the currently running configuration. Restart the proxy to apply your changes.') }}
                        </x-callout>
                    @endif
                    <h3>{{ __('Advanced') }}</h3>
                    <div class="pb-6 w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$server"
                            :helper="__('If set, all resources will only have docker container labels for :proxy.<br>For applications, labels needs to be regenerated manually. <br>Resources needs to be restarted.', ['proxy' => str($server->proxyType())->title()])"
                            id="generateExactLabels"
                            :label="__('Generate labels only for :proxy', ['proxy' => str($server->proxyType())->title()])" instantSave />
                        <x-forms.checkbox canGate="update" :canResource="$server" instantSave="instantSaveRedirect"
                            id="redirectEnabled" :label="__('Override default request handler')"
                            :helper="__('Requests to unknown hosts or stopped services will receive a 503 response or be redirected to the URL you set below (need to enable this first).')" />
                        @if ($redirectEnabled)
                            <x-forms.input canGate="update" :canResource="$server" placeholder="https://app.coolify.io"
                                id="redirectUrl" :label="__('Redirect to (optional)')" />
                        @endif
                    </div>
                    @php
                        $proxyTitle =
                            $server->proxyType() === ProxyTypes::TRAEFIK->value
                                ? 'Traefik (Coolify Proxy)'
                                : 'Caddy (Coolify Proxy)';
                    @endphp
                    @if ($server->proxyType() === ProxyTypes::TRAEFIK->value || $server->proxyType() === 'CADDY')
                        <div @if($server->proxyType() === ProxyTypes::TRAEFIK->value) x-data="{ traefikWarningsDismissed: localStorage.getItem('callout-dismissed-traefik-warnings-{{ $server->id }}') === 'true' }" @endif>
                            <div class="flex items-center gap-2">
                                <h3>{{ $proxyTitle }}</h3>
                                @can('update', $server)
                                    @if ($proxySettings)
                                        <x-modal-confirmation :title="__('Reset Proxy Configuration?')"
                                            :buttonTitle="__('Reset Configuration')" submitAction="resetProxyConfiguration"
                                            :actions="[
                                                __('Reset proxy configuration to default settings'),
                                                __('All custom configurations will be lost'),
                                                __('Custom ports and entrypoints will be removed'),
                                            ]" confirmationText="{{ $server->name }}"
                                            :confirmationLabel="__('Please confirm by entering the server name below')"
                                            :shortConfirmationLabel="__('Server Name')" :step2ButtonText="__('Reset Configuration')"
                                            :confirmWithPassword="false" :confirmWithText="true">
                                        </x-modal-confirmation>
                                    @endif
                                @endcan
                                @if ($server->proxyType() === ProxyTypes::TRAEFIK->value)
                                    <button type="button" x-show="traefikWarningsDismissed"
                                            @click="traefikWarningsDismissed = false; localStorage.removeItem('callout-dismissed-traefik-warnings-{{ $server->id }}')"
                                            class="p-1.5 rounded hover:bg-warning-100 dark:hover:bg-warning-900/30 transition-colors"
                                            title="{{ __('Show Traefik warnings') }}">
                                        <svg class="w-4 h-4 text-warning-600 dark:text-warning-400" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="currentColor" d="M240.26 186.1L152.81 34.23a28.74 28.74 0 0 0-49.62 0L15.74 186.1a27.45 27.45 0 0 0 0 27.71A28.31 28.31 0 0 0 40.55 228h174.9a28.31 28.31 0 0 0 24.79-14.19a27.45 27.45 0 0 0 .02-27.71m-20.8 15.7a4.46 4.46 0 0 1-4 2.2H40.55a4.46 4.46 0 0 1-4-2.2a3.56 3.56 0 0 1 0-3.73L124 46.2a4.77 4.77 0 0 1 8 0l87.44 151.87a3.56 3.56 0 0 1 .02 3.73M116 136v-32a12 12 0 0 1 24 0v32a12 12 0 0 1-24 0m28 40a16 16 0 1 1-16-16a16 16 0 0 1 16 16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if ($server->proxyType() === ProxyTypes::TRAEFIK->value)
                                <div x-show="!traefikWarningsDismissed"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2">
                                    @if ($server->detected_traefik_version === 'latest')
                                        <x-callout dismissible onDismiss="traefikWarningsDismissed = true; localStorage.setItem('callout-dismissed-traefik-warnings-{{ $server->id }}', 'true')" type="warning" :title="__('Using \'latest\' Traefik Tag')" class="my-4">
                                            {!! __('Your proxy container is running the <span class="font-mono">latest</span> tag. While this ensures you always have the newest version, it may introduce unexpected breaking changes.') !!}
                                            <br><br>
                                            <strong>{{ __('Recommendation:') }}</strong>
                                            {!! __('Pin to a specific version (e.g., <span class="font-mono">traefik::version</span>) to ensure stability and predictable updates.', ['version' => $this->latestTraefikVersion]) !!}
                                        </x-callout>
                                    @elseif($this->isTraefikOutdated)
                                        <x-callout dismissible onDismiss="traefikWarningsDismissed = true; localStorage.setItem('callout-dismissed-traefik-warnings-{{ $server->id }}', 'true')" type="warning" :title="__('Traefik Patch Update Available')" class="my-4">
                                            {!! __('Your Traefik proxy container is running version <span class="font-mono">v:current</span>, but version <span class="font-mono">:latest</span> is available.', ['current' => $server->detected_traefik_version, 'latest' => $this->latestTraefikVersion]) !!}
                                            <br><br>
                                            <strong>{{ __('Recommendation:') }}</strong>
                                            {{ __('Update to the latest patch version for security fixes and bug fixes. Please test in a non-production environment first.') }}
                                        </x-callout>
                                    @endif
                                    @if ($this->newerTraefikBranchAvailable)
                                        <x-callout dismissible onDismiss="traefikWarningsDismissed = true; localStorage.setItem('callout-dismissed-traefik-warnings-{{ $server->id }}', 'true')" type="info" :title="__('New Minor Traefik Version Available')" class="my-4">
                                            {!! __('A new minor version of Traefik is available: <span class="font-mono">:version</span>', ['version' => $this->newerTraefikBranchAvailable]) !!}
                                            <br><br>
                                            {!! __('You are currently running <span class="font-mono">v:current</span>.', ['current' => $server->detected_traefik_version]) !!}
                                            {!! __('Upgrading to <span class="font-mono">:version</span> will give you access to new features and improvements.', ['version' => $this->newerTraefikBranchAvailable]) !!}
                                            <br><br>
                                            <strong>{{ __('Important:') }}</strong>
                                            {!! __('Before upgrading to a new minor version, please read the <a href="https://github.com/traefik/traefik/releases" target="_blank" class="underline text-white">Traefik changelog</a> to understand breaking changes and new features.') !!}
                                            <br><br>
                                            <strong>{{ __('Recommendation:') }}</strong>
                                            {{ __('Test the upgrade in a non-production environment first.') }}
                                        </x-callout>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    @if ($proxySettings)
                        <div class="flex flex-col gap-2 pt-2">
                            <x-forms.textarea canGate="update" :canResource="$server" useMonacoEditor
                                monacoEditorLanguage="yaml"
                                :label="__('Configuration file ( :path )', ['path' => $this->configurationFilePath])"
                                name="proxySettings" id="proxySettings" rows="30" />
                        </div>
                    @endif
                </form>
            @elseif($selectedProxy === 'NONE')
                <div class="flex items-center gap-2">
                    <h2>{{ __('Configuration') }}</h2>
                    @can('update', $server)
                        <x-forms.button wire:click.prevent="changeProxy">{{ __('Switch Proxy') }}</x-forms.button>
                    @endcan
                </div>
                <div class="pt-2 pb-4">{{ __('Custom (None) Proxy Selected') }}</div>
            @else
                <div class="flex items-center gap-2">
                    <h2>{{ __('Configuration') }}</h2>
                    @can('update', $server)
                        <x-forms.button wire:click.prevent="changeProxy">{{ __('Switch Proxy') }}</x-forms.button>
                    @endcan
                </div>
            @endif
        @else
            <div>
                <h2>{{ __('Configuration') }}</h2>
                <div class="subtitle">{{ __('Select a proxy you would like to use on this server.') }}</div>
                @can('update', $server)
                    <div class="grid gap-4">
                        <x-forms.button class="coolbox" wire:click="selectProxy('NONE')">
                            {{ __('Custom (None)') }}
                        </x-forms.button>
                        <x-forms.button class="coolbox" wire:click="selectProxy('TRAEFIK')">
                            Traefik
                        </x-forms.button>
                        <x-forms.button class="coolbox" wire:click="selectProxy('CADDY')">
                            Caddy
                        </x-forms.button>
                        {{-- <x-forms.button disabled class="box">
                            Nginx
                        </x-forms.button> --}}
                    </div>
                @else
                    <x-callout type="warning" :title="__('Permission Required')" class="mb-4">
                        {{ __("You don't have permission to configure proxy settings for this server.") }}
                    </x-callout>
                @endcan
            </div>
    @endif
</div>
