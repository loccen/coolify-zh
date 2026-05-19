<div>
    <x-slot:title>
        {{ __('navigation.dashboard') }} | Coolify
    </x-slot>
    @if (session('error'))
        <span x-data x-init="$wire.emit('error', '{{ session('error') }}')" />
    @endif
    <h1>{{ __('navigation.dashboard') }}</h1>
    <div class="subtitle">{{ __('dashboard.subtitle') }}</div>

    <section class="-mt-2">
        <div class="flex items-center gap-2 pb-2">
            <h3>{{ __('navigation.projects') }}</h3>
            @if ($projects->count() > 0)
                <x-modal-input :buttonTitle="__('Add')" :title="__('New Project')">
                    <x-slot:content>
                        <button
                            class="flex items-center justify-center size-4 text-black dark:text-white rounded hover:bg-coolgray-400 dark:hover:bg-coolgray-300 cursor-pointer">
                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </x-slot:content>
                    <livewire:project.add-empty />
                </x-modal-input>
            @endif
        </div>
        @if ($projects->count() > 0)
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach ($projects as $project)
                    <div class="relative gap-2 cursor-pointer coolbox group">
                        <a href="{{ $project->navigateTo() }}" {{ wireNavigate() }} class="absolute inset-0"></a>
                        <div class="flex flex-1 mx-6">
                            <div class="flex flex-col justify-center flex-1">
                                <div class="box-title">{{ $project->name }}</div>
                                <div class="box-description">
                                    {{ $project->description }}
                                </div>
                            </div>
                            <div class="relative z-10 flex items-center justify-center gap-4 text-xs font-bold">
                                @if ($project->environments->first())
                                    @can('createAnyResource')
                                        <a class="hover:underline" {{ wireNavigate() }}
                                            href="{{ route('project.resource.create', [
                                                'project_uuid' => $project->uuid,
                                                'environment_uuid' => $project->environments->first()->uuid,
                                            ]) }}">
                                            {{ __('+ Add Resource') }}
                                        </a>
                                    @endcan
                                @endif
                                @can('update', $project)
                                    <a class="hover:underline" {{ wireNavigate() }}
                                        href="{{ route('project.edit', ['project_uuid' => $project->uuid]) }}">
                                        {{ __('Settings') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col gap-1">
                <div class='font-bold dark:text-warning'>{{ __('dashboard.projects.empty.title') }}</div>
                <div class="flex items-center gap-1">
                    <x-modal-input :buttonTitle="__('Add')" :title="__('New Project')">
                        <livewire:project.add-empty />
                    </x-modal-input> {{ __('dashboard.projects.empty.after_add') }}
                    <a class="underline dark:text-white" href="{{ route('onboarding') }}" {{ wireNavigate() }}>{{ __('dashboard.onboarding_page') }}</a>.
                </div>
            </div>
        @endif
    </section>

    <section>
        <div class="flex items-center gap-2 pb-2">
            <h3>{{ __('navigation.servers') }}</h3>
            @if ($servers->count() > 0 && $privateKeys->count() > 0)
                <x-modal-input :buttonTitle="__('Add')" :title="__('New Server')" :closeOutside="false">
                    <x-slot:content>
                        <button
                            class="flex items-center justify-center size-4 text-black dark:text-white rounded hover:bg-coolgray-400 dark:hover:bg-coolgray-300 cursor-pointer">
                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </x-slot:content>
                    <livewire:server.create />
                </x-modal-input>
            @endif
        </div>
        @if ($servers->count() > 0)
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach ($servers as $server)
                    <a href="{{ route('server.show', ['server_uuid' => data_get($server, 'uuid')]) }}" {{ wireNavigate() }}
                        @class([
                            'gap-2 border cursor-pointer coolbox group',
                            'border-red-500' =>
                                !$server->settings->is_reachable || $server->settings->force_disabled,
                        ])>
                        <div class="flex flex-col justify-center mx-6">
                            <div class="box-title">
                                {{ $server->name }}
                            </div>
                            <div class="box-description">
                                {{ $server->description }}</div>
                            <div class="flex gap-1 text-xs text-error">
                                @if (!$server->settings->is_reachable)
                                    {{ __('dashboard.servers.not_reachable') }}
                                @endif
                                @if (!$server->settings->is_reachable && !$server->settings->is_usable)
                                    &
                                @endif
                                @if (!$server->settings->is_usable)
                                    {{ __('dashboard.servers.not_usable') }}
                                @endif
                            </div>
                        </div>
                        <div class="flex-1"></div>
                    </a>
                @endforeach
            </div>
        @else
            @if ($privateKeys->count() === 0)
                <div class="flex flex-col gap-1">
                    <div class='font-bold dark:text-warning'>{{ __('dashboard.private_keys.empty.title') }}</div>
                    <div class="flex items-center gap-1"><x-modal-input
                            :buttonTitle="__('Add')" :title="__('New Private Key')">
                            <livewire:security.private-key.create from="server" />
                        </x-modal-input> {{ __('dashboard.private_keys.empty.after_add') }}
                        <a class="underline dark:text-white" href="{{ route('onboarding') }}" {{ wireNavigate() }}>{{ __('dashboard.onboarding_page') }}</a>.
                    </div>
                </div>
            @else
                <div class="flex flex-col gap-1">
                    <div class='font-bold dark:text-warning'>{{ __('dashboard.servers.empty.title') }}</div>
                    <div class="flex items-center gap-1">
                        <x-modal-input :buttonTitle="__('Add')" :title="__('New Server')" :closeOutside="false">
                            <livewire:server.create />
                        </x-modal-input> {{ __('dashboard.servers.empty.after_add') }}
                        <a class="underline dark:text-white" href="{{ route('onboarding') }}" {{ wireNavigate() }}>{{ __('dashboard.onboarding_page') }}</a>.
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
