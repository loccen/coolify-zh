<div>
    <h2>{{ __('Servers') }}</h2>
    <div class="">{{ __('Server related configurations.') }}</div>
    <div class="grid grid-cols-1 gap-4 py-4">
        <div class="flex flex-col gap-2">
            <h3>{{ __('Primary Server') }}</h3>
            <div
                class="relative flex flex-col bg-white border cursor-default dark:text-white box-without-bg dark:bg-coolgray-100 dark:border-coolgray-300">
                @if (str($resource->realStatus())->startsWith('running'))
                    <div title="{{ $resource->realStatus() }}" class="absolute bg-success -top-1 -left-1 badge ">
                    </div>
                @elseif (str($resource->realStatus())->startsWith('exited'))
                    <div title="{{ $resource->realStatus() }}" class="absolute bg-error -top-1 -left-1 badge ">
                    </div>
                @endif
                <div class="box-title">
                    {{ __('Server') }}: {{ data_get($resource, 'destination.server.name') }}
                </div>
                <div class="box-description">
                    {{ __('Network') }}: {{ data_get($resource, 'destination.network') }}
                </div>
            </div>
            @if ($resource?->additional_networks?->count() > 0)
                <div class="flex gap-2">
                    <x-forms.button
                        wire:click="redeploy('{{ data_get($resource, 'destination.id') }}','{{ data_get($resource, 'destination.server.id') }}')">{{ __('Deploy') }}</x-forms.button>
                    @if (str($resource->realStatus())->startsWith('running'))
                        <x-forms.button isError
                            wire:click="stop('{{ data_get($resource, 'destination.server.id') }}')">{{ __('Stop') }}</x-forms.button>
                    @endif
                </div>
            @endif
        </div>
        @if ($resource?->additional_networks?->count() > 0 && data_get($resource, 'build_pack') !== 'dockercompose')
            <h3>{{ __('Additional Server(s)') }}</h3>
            @foreach ($resource->additional_networks as $destination)
                <div class="flex flex-col gap-2" wire:key="destination-{{ $destination->id }}">
                    <div
                        class="relative flex flex-col bg-white border cursor-default dark:text-white box-without-bg dark:bg-coolgray-100 dark:border-coolgray-300">
                        @if (str(data_get($destination, 'pivot.status'))->startsWith('running'))
                            <div title="{{ data_get($destination, 'pivot.status') }}"
                                class="absolute bg-success -top-1 -left-1 badge "></div>
                        @elseif (str(data_get($destination, 'pivot.status'))->startsWith('exited'))
                            <div title="{{ data_get($destination, 'pivot.status') }}"
                                class="absolute bg-error -top-1 -left-1 badge "></div>
                        @endif
                        <div>
                            <div class="box-title">
                                {{ __('Server') }}: {{ data_get($destination, 'server.name') }}
                            </div>
                            <div class="box-description">
                                {{ __('Network') }}: {{ data_get($destination, 'network') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-forms.button
                            wire:click="redeploy('{{ data_get($destination, 'id') }}','{{ data_get($destination, 'server.id') }}')">{{ __('Deploy') }}</x-forms.button>
                        <x-forms.button
                            wire:click="promote('{{ data_get($destination, 'id') }}','{{ data_get($destination, 'server.id') }}')">{{ __('Promote to Primary') }}</x-forms.button>
                        @if (data_get_str($destination, 'pivot.status')->startsWith('running'))
                            <x-forms.button isError
                                wire:click="stop('{{ data_get($destination, 'server.id') }}')">{{ __('Stop') }}</x-forms.button>
                        @endif
                        <x-modal-confirmation :title="__('Confirm removing application from server?')" isErrorButton
                            :buttonTitle="__('Remove from server')"
                            submitAction="removeServer({{ data_get($destination, 'id') }},{{ data_get($destination, 'server.id') }})"
                            :actions="[
                                __('This will stop all running applications on this server and remove it as a deployment destination.'),
                            ]" confirmationText="{{ data_get($destination, 'server.name') }}"
                            :confirmationLabel="__('Please confirm the execution of the actions by entering the Server Name below')"
                            :shortConfirmationLabel="__('Server Name')" />
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    @if ($resource->getMorphClass() === 'App\Models\Application' && data_get($resource, 'build_pack') !== 'dockercompose')
        <div class="flex flex-col gap-2">
            @if ($resource->persistentStorages()->count() > 0)
                <h3>{{ __('Add another server') }}</h3>
                <x-callout type="warning" :title="__('Cannot add additional servers')">
                    {{ __('This application has persistent storage volumes configured. Applications with persistent storage cannot be deployed to multiple servers as the storage would not be accessible across different servers.') }}
                </x-callout>
            @elseif (count($networks) > 0)
                <h3>{{ __('Add another server') }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($networks as $network)
                        <div wire:click="addServer('{{ $network->id }}','{{ data_get($network, 'server.id') }}')"
                            class="relative flex flex-col dark:text-white coolbox group">
                            <div>
                                <div class="box-title">
                                    {{ __('Server') }}: {{ data_get($network, 'server.name') }}
                                </div>
                                <div class="box-description">
                                    {{ __('Network') }}: {{ data_get($network, 'name') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div>{{ __('No additional servers available to attach.') }}</div>
            @endif
        </div>
    @endif
</div>
