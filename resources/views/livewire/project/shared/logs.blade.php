<div>
    <x-slot:title>
        {{ data_get_str($resource, 'name')->limit(10) }} > {{ __('Logs') }} | Coolify
    </x-slot>
    <livewire:project.shared.configuration-checker :resource="$resource" />
    @if ($type === 'application')
        <h1>{{ __('Logs') }}</h1>
        <livewire:project.application.heading :application="$resource" />
        <div>
            <h2>{{ __('Logs') }}</h2>
            <div class="pt-2" wire:loading wire:target="loadAllContainers">
                {{ __('Loading containers...') }}
            </div>
            <div x-init="$wire.loadAllContainers()" wire:loading.remove wire:target="loadAllContainers">
                @forelse ($servers as $server)
                    <div class="py-2">
                        <h4>{{ __('Server: :name', ['name' => $server->name]) }}</h4>
                        @if ($server->isFunctional())
                            @if (isset($serverContainers[$server->id]) && count($serverContainers[$server->id]) > 0)
                                @php
                                    $totalContainers = collect($serverContainers)->flatten(1)->count();
                                @endphp
                                @foreach ($serverContainers[$server->id] as $container)
                                    <livewire:project.shared.get-logs
                                        wire:key="{{ data_get($container, 'ID', uniqid()) }}" :server="$server"
                                        :resource="$resource" :container="data_get($container, 'Names')"
                                        :expandByDefault="$totalContainers === 1" />
                                @endforeach
                            @else
                                <div class="pt-2">{{ __('No containers are running on server: :name', ['name' => $server->name]) }}</div>
                            @endif
                        @else
                            <div class="pt-2">{{ __('Server :name is not functional.', ['name' => $server->name]) }}</div>
                        @endif
                    </div>
                @empty
                    <div>{{ __('No functional server found for the application.') }}</div>
                @endforelse
            </div>
        </div>
    @elseif ($type === 'database')
        <h1>{{ __('Logs') }}</h1>
        <livewire:project.database.heading :database="$resource" />
        <div>
            <h2>{{ __('Logs') }}</h2>
            @if (str($status)->contains('exited'))
                <div class="pt-4">{{ __('The resource is not running.') }}</div>
            @else
                <div class="pt-2" wire:loading wire:target="loadAllContainers">
                    {{ __('Loading containers...') }}
                </div>
                <div x-init="$wire.loadAllContainers()" wire:loading.remove wire:target="loadAllContainers">
                    @forelse ($containers as $container)
                        @if (data_get($servers, '0'))
                            <livewire:project.shared.get-logs wire:key='{{ $container }}' :server="data_get($servers, '0')"
                                :resource="$resource" :container="$container"
                                :expandByDefault="count($containers) === 1" />
                        @else
                            <div>{{ __('No functional server found for the database.') }}</div>
                        @endif
                    @empty
                        <div class="pt-2">{{ __('No containers are running.') }}</div>
                    @endforelse
                </div>
            @endif
        </div>
    @elseif ($type === 'service')
        <livewire:project.service.heading :service="$resource" :parameters="$parameters" :query="$query" title="Logs" />
        <div>
            <h2>{{ __('Logs') }}</h2>
            @if (str($status)->contains('exited'))
                <div class="pt-4">{{ __('The resource is not running.') }}</div>
            @else
                <div class="pt-2" wire:loading wire:target="loadAllContainers">
                    {{ __('Loading containers...') }}
                </div>
                <div x-init="$wire.loadAllContainers()" wire:loading.remove wire:target="loadAllContainers">
                    @forelse ($containers as $container)
                        @if (data_get($servers, '0'))
                            <livewire:project.shared.get-logs wire:key='{{ $container }}' :server="data_get($servers, '0')"
                                :resource="$resource" :container="$container"
                                :expandByDefault="count($containers) === 1" />
                        @else
                            <div>{{ __('No functional server found for the service.') }}</div>
                        @endif
                    @empty
                        <div class="pt-2">{{ __('No containers are running.') }}</div>
                    @endforelse
                </div>
            @endif
        </div>
    @endif
</div>
