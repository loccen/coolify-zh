<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Log Drains') }} | Coolify
    </x-slot>
    <livewire:server.navbar :server="$server" />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-server.sidebar :server="$server" activeMenu="log-drains" />
        <div class="w-full">
            @if ($server->isFunctional())
                <div class="flex gap-2 items-center">
                    <h2>{{ __('Log Drains') }}</h2>
                    <x-loading wire:target="instantSave" wire:loading.delay />
                </div>
                <div>{{ __('Sends service logs to 3rd party tools.') }}</div>
                <div class="flex flex-col gap-4 pt-4">
                    <div class="p-4 border dark:border-coolgray-300 border-neutral-200">
                        <form wire:submit='submit("newrelic")' class="flex flex-col">
                            <h3>{{ __('New Relic') }}</h3>
                            <div class="w-32">
                                @if ($isLogDrainAxiomEnabled || $isLogDrainCustomEnabled)
                                    <x-forms.checkbox disabled id="isLogDrainNewRelicEnabled" :label="__('Enabled')" />
                                @else
                                    <x-forms.checkbox instantSave canGate="update" :canResource="$server"
                                        id="isLogDrainNewRelicEnabled" :label="__('Enabled')" />
                                @endif
                            </div>
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-col w-full gap-2 xl:flex-row">
                                    @if ($server->isLogDrainEnabled())
                                        <x-forms.input disabled type="password" required id="logDrainNewRelicLicenseKey"
                                            :label="__('License Key')" />
                                        <x-forms.input disabled required id="logDrainNewRelicBaseUri"
                                            placeholder="https://log-api.eu.newrelic.com/log/v1"
                                            :helper="__('For EU use: https://log-api.eu.newrelic.com/log/v1<br>For US use: https://log-api.newrelic.com/log/v1')"
                                            :label="__('Endpoint')" />
                                    @else
                                        <x-forms.input canGate="update" :canResource="$server" type="password" required
                                            id="logDrainNewRelicLicenseKey" :label="__('License Key')" />
                                        <x-forms.input canGate="update" :canResource="$server" required
                                            id="logDrainNewRelicBaseUri"
                                            placeholder="https://log-api.eu.newrelic.com/log/v1"
                                            :helper="__('For EU use: https://log-api.eu.newrelic.com/log/v1<br>For US use: https://log-api.newrelic.com/log/v1')"
                                            :label="__('Endpoint')" />
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 pt-6">
                                <x-forms.button canGate="update" :canResource="$server" type="submit">
                                    {{ __('Save') }}
                                </x-forms.button>
                            </div>
                        </form>

                        <h3>{{ __('Axiom') }}</h3>
                        <div class="w-32">
                            @if ($isLogDrainNewRelicEnabled || $isLogDrainCustomEnabled)
                                <x-forms.checkbox disabled id="isLogDrainAxiomEnabled" :label="__('Enabled')" />
                            @else
                                <x-forms.checkbox instantSave canGate="update" :canResource="$server"
                                    id="isLogDrainAxiomEnabled" :label="__('Enabled')" />
                            @endif
                        </div>
                        <form wire:submit='submit("axiom")' class="flex flex-col">
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-col w-full gap-2 xl:flex-row">
                                    @if ($server->isLogDrainEnabled())
                                        <x-forms.input disabled type="password" required id="logDrainAxiomApiKey"
                                            :label="__('API Key')" />
                                        <x-forms.input disabled required id="logDrainAxiomDatasetName"
                                            :label="__('Dataset Name')" />
                                    @else
                                        <x-forms.input canGate="update" :canResource="$server" type="password" required
                                            id="logDrainAxiomApiKey" :label="__('API Key')" />
                                        <x-forms.input canGate="update" :canResource="$server" required
                                            id="logDrainAxiomDatasetName" :label="__('Dataset Name')" />
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 pt-6">
                                <x-forms.button canGate="update" :canResource="$server" type="submit">
                                    {{ __('Save') }}
                                </x-forms.button>
                            </div>
                        </form>
                        <h3>{{ __('Custom FluentBit') }}</h3>
                        <div class="w-32">
                            @if ($isLogDrainNewRelicEnabled || $isLogDrainAxiomEnabled)
                                <x-forms.checkbox disabled id="isLogDrainCustomEnabled" :label="__('Enabled')" />
                            @else
                                <x-forms.checkbox instantSave canGate="update" :canResource="$server"
                                    id="isLogDrainCustomEnabled" :label="__('Enabled')" />
                            @endif
                        </div>
                        <form wire:submit='submit("custom")' class="flex flex-col">
                            <div class="flex flex-col gap-4">
                                @if ($server->isLogDrainEnabled())
                                    <x-forms.textarea disabled rows="6" required id="logDrainCustomConfig"
                                        :label="__('Custom FluentBit Configuration')" />
                                    <x-forms.textarea disabled id="logDrainCustomConfigParser"
                                        :label="__('Custom Parser Configuration')" />
                                @else
                                    <x-forms.textarea canGate="update" :canResource="$server" rows="6" required
                                        id="logDrainCustomConfig" :label="__('Custom FluentBit Configuration')" />
                                    <x-forms.textarea canGate="update" :canResource="$server"
                                        id="logDrainCustomConfigParser" :label="__('Custom Parser Configuration')" />
                                @endif

                            </div>
                            <div class="flex justify-end gap-4 pt-6">
                                <x-forms.button canGate="update" :canResource="$server" type="submit">
                                    {{ __('Save') }}
                                </x-forms.button>
                            </div>
                        </form>

                    </div>
                </div>
            @else
                <div>{{ __('Server is not validated. Validate first.') }}</div>
            @endif
        </div>
    </div>
</div>
