<div>
    <x-slot:title>
        {{ __('Destinations') }} | Coolify
    </x-slot>
    <div class="flex items-center gap-2">
        <h1>{{ __('Destinations') }}</h1>
        @if ($servers->count() > 0)
            @can('createAnyResource')
                <x-modal-input buttonTitle="{{ __('+ Add') }}" title="{{ __('New Destination') }}">
                    <livewire:destination.new.docker />
                </x-modal-input>
            @endcan
        @endif
    </div>
    <div class="subtitle">{{ __('Network endpoints to deploy your resources.') }}</div>
    <div class="grid gap-4 lg:grid-cols-2 -mt-1">
        @forelse ($servers as $server)
            @forelse ($server->destinations() as $destination)
                @if ($destination->getMorphClass() === 'App\Models\StandaloneDocker')
                    <a class="coolbox group" {{ wireNavigate() }}
                        href="{{ route('destination.show', ['destination_uuid' => data_get($destination, 'uuid')]) }}">
                        <div class="flex flex-col justify-center mx-6">
                            <div class="box-title">{{ $destination->name }}</div>
                            <div class="box-description">{{ __('Server') }}: {{ $destination->server->name }}</div>
                        </div>
                    </a>
                @endif
                @if ($destination->getMorphClass() === 'App\Models\SwarmDocker')
                    <a class="coolbox group" {{ wireNavigate() }}
                        href="{{ route('destination.show', ['destination_uuid' => data_get($destination, 'uuid')]) }}">
                        <div class="flex flex-col mx-6">
                            <div class="box-title">
                                {{ $destination->name }}
                                <x-deprecated-badge />
                            </div>
                            <div class="box-description">{{ __('Server') }}: {{ $destination->server->name }}</div>
                        </div>
                    </a>
                @endif
            @empty
                <div>{{ __('No destinations found.') }}</div>
            @endforelse
        @empty
            <div>{{ __('No servers found.') }}</div>
        @endforelse
    </div>
</div>
