<div>
    <x-slot:title>
        {{ __('navigation.terminal') }} | Coolify
        </x-slot>
        <h1>{{ __('navigation.terminal') }}</h1>
        <div class="flex gap-2 items-end subtitle">
            <div>{{ __('terminal.subtitle') }}</div>
            <x-helper
                :helper="__('terminal.help.connection', ['url' => 'https://coolify.io/docs/knowledge-base/server/firewall/#terminal'])"></x-helper>
        </div>
        <div x-init="$wire.loadContainers()">
            @if ($isLoadingContainers)
                <div class="pt-1">
                    <x-loading :text="__('terminal.loading_containers')" />
                </div>
            @else
                @if ($servers->count() > 0)
                    <form class="flex flex-col gap-2 justify-center xl:items-end xl:flex-row"
                        wire:submit="$dispatchSelf('connectToContainer')">
                        <x-forms.select id="selected_uuid" required wire:model.live="selected_uuid">
                            <option value="default">{{ __('terminal.select_target') }}</option>
                            @foreach ($servers as $server)
                                <option value="{{ $server->uuid }}">{{ $server->name }}</option>
                                @foreach ($containers as $container)
                                    @if ($container['server_uuid'] == $server->uuid)
                                        <option value="{{ $container['uuid'] }}">
                                            {{ $server->name }} -> {{ $container['name'] }}
                                        </option>
                                    @endif
                                @endforeach
                            @endforeach
                        </x-forms.select>
                        <x-forms.button type="submit">{{ __('Connect') }}</x-forms.button>
                    </form>
                @else
                    <div>{{ __('terminal.no_servers') }}</div>
                @endif
            @endif
            <livewire:project.shared.terminal />
        </div>
</div>
