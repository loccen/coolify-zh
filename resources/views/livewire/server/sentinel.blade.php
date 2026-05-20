<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Sentinel') }} | Coolify
    </x-slot>
    <livewire:server.navbar :server="$server" />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-server.sidebar :server="$server" activeMenu="sentinel" />
        <div class="w-full">
            <form wire:submit.prevent='submit'>
                <div class="flex gap-2 items-center pb-2">
                    <h2>{{ __('Sentinel') }}</h2>
                    <x-helper :helper="__('Sentinel reports your server\\'s & container\\'s health and collects metrics.')" />
                    @if ($server->isSentinelEnabled())
                        <div class="flex gap-2 items-center">
                            @if ($server->isSentinelLive())
                                <x-status.running :status="__('In sync')" noLoading title="{{ $sentinelUpdatedAt }}" />
                                <x-forms.button type="submit" canGate="update" :canResource="$server">{{ __('Save') }}</x-forms.button>
                                <x-forms.button wire:click='restartSentinel' canGate="update" :canResource="$server">{{ __('Restart') }}</x-forms.button>
                                <x-slide-over fullScreen>
                                    <x-slot:title>{{ __('Sentinel Logs') }}</x-slot:title>
                                    <x-slot:content>
                                        <livewire:project.shared.get-logs :server="$server"
                                            container="coolify-sentinel" displayName="Sentinel" :collapsible="false"
                                            lazy />
                                    </x-slot:content>
                                    <x-forms.button @click="slideOverOpen=true">{{ __('Logs') }}</x-forms.button>
                                </x-slide-over>
                            @else
                                <x-status.stopped :status="__('Out of sync')" noLoading
                                    title="{{ $sentinelUpdatedAt }}" />
                                <x-forms.button type="submit" canGate="update" :canResource="$server">{{ __('Save') }}</x-forms.button>
                                <x-forms.button wire:click='restartSentinel' canGate="update" :canResource="$server">{{ __('Sync') }}</x-forms.button>
                                <x-slide-over fullScreen>
                                    <x-slot:title>{{ __('Sentinel Logs') }}</x-slot:title>
                                    <x-slot:content>
                                        <livewire:project.shared.get-logs :server="$server"
                                            container="coolify-sentinel" displayName="Sentinel" :collapsible="false"
                                            lazy />
                                    </x-slot:content>
                                    <x-forms.button @click="slideOverOpen=true">{{ __('Logs') }}</x-forms.button>
                                </x-slide-over>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex flex-col gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$server" wire:model.live="isSentinelEnabled"
                            :label="__('Enable Sentinel')" />
                        @if ($server->isSentinelEnabled())
                            @if (isDev())
                                <x-forms.checkbox canGate="update" :canResource="$server" id="isSentinelDebugEnabled"
                                    :label="__('Enable Sentinel (with debug)')" instantSave />
                            @endif
                            <x-forms.checkbox canGate="update" :canResource="$server" instantSave
                                id="isMetricsEnabled" :label="__('Enable Metrics')" />
                        @else
                            @if (isDev())
                                <x-forms.checkbox id="isSentinelDebugEnabled" label="Enable Sentinel (with debug)"
                                    :label="__('Enable Sentinel (with debug)')" disabled instantSave />
                            @endif
                            <x-forms.checkbox instantSave disabled id="isMetricsEnabled"
                                :label="__('Enable Metrics (enable Sentinel first)')" />
                        @endif
                    </div>
                    @if (isDev() && $server->isSentinelEnabled())
                        <div class="pt-4" x-data="{
                            customImage: localStorage.getItem('sentinel_custom_docker_image_{{ $server->uuid }}') || '',
                            saveCustomImage() {
                                localStorage.setItem('sentinel_custom_docker_image_{{ $server->uuid }}', this.customImage);
                                $wire.set('sentinelCustomDockerImage', this.customImage);
                            }
                        }" x-init="$wire.set('sentinelCustomDockerImage', customImage)">
                            <x-forms.input x-model="customImage" @input.debounce.500ms="saveCustomImage()"
                                placeholder="e.g., sentinel:latest or myregistry/sentinel:dev"
                                :label="__('Custom Sentinel Docker Image (Dev Only)')"
                                :helper="__('Override the default Sentinel Docker image for testing. Leave empty to use the default.')" />
                        </div>
                    @endif
                    @if ($server->isSentinelEnabled())
                        <div class="flex flex-wrap gap-2 sm:flex-nowrap items-end">
                            <x-forms.input canGate="update" :canResource="$server" type="password" id="sentinelToken"
                                :label="__('Sentinel token')" required :helper="__('Token for Sentinel.')" />
                            <x-forms.button canGate="update" :canResource="$server"
                                wire:click="regenerateSentinelToken">{{ __('Regenerate') }}</x-forms.button>
                        </div>

                        <x-forms.input canGate="update" :canResource="$server" id="sentinelCustomUrl" required
                            :label="__('Coolify URL')"
                            :helper="__('URL to your Coolify instance. If it is empty that means you do not have a FQDN set for your Coolify instance.')" />

                        <div class="flex flex-col gap-2">
                            <div class="flex flex-wrap gap-2 sm:flex-nowrap">
                                <x-forms.input canGate="update" :canResource="$server" type="number" min="1"
                                    id="sentinelMetricsRefreshRateSeconds" :label="__('Metrics rate (seconds)')" required
                                    :helper="__('Interval used for gathering metrics. Lower values result in more disk space usage.')" />
                                <x-forms.input canGate="update" :canResource="$server" type="number" min="1"
                                    id="sentinelMetricsHistoryDays"
                                    :label="__('Metrics history (days)')" required
                                    :helper="__('Number of days to retain metrics data for.')" />
                                <x-forms.input canGate="update" :canResource="$server" type="number" min="10"
                                    id="sentinelPushIntervalSeconds" :label="__('Push interval (seconds)')" required
                                    :helper="__('Interval at which metrics data is sent to the collector.')" />
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
