<nav wire:poll.10000ms="checkStatus" class="pb-6">
    <x-resources.breadcrumbs :resource="$database" :parameters="$parameters" />
    <x-slide-over @startdatabase.window="slideOverOpen = true" closeWithX fullScreen>
        <x-slot:title>{{ __('Database Startup') }}</x-slot:title>
        <x-slot:content>
            <div wire:ignore>
                <livewire:activity-monitor header="{{ __('Logs') }}" fullHeight />
            </div>
        </x-slot:content>
    </x-slide-over>
    <div class="navbar-main">
        <nav
            class="scrollbar flex min-h-10 w-full flex-nowrap items-center gap-6 overflow-x-scroll overflow-y-hidden pb-1 whitespace-nowrap md:w-auto md:overflow-visible">
            <a class="shrink-0 {{ request()->routeIs('project.database.configuration') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('project.database.configuration', $parameters) }}">
                {{ __('Configuration') }}
            </a>

            <a class="shrink-0 {{ request()->routeIs('project.database.logs') ? 'dark:text-white' : '' }}"
                href="{{ route('project.database.logs', $parameters) }}">
                {{ __('Logs') }}
            </a>
            @can('canAccessTerminal')
                <a class="shrink-0 {{ request()->routeIs('project.database.command') ? 'dark:text-white' : '' }}"
                    href="{{ route('project.database.command', $parameters) }}">
                    {{ __('Terminal') }}
                </a>
            @endcan
            @if (
                $database->getMorphClass() === 'App\Models\StandalonePostgresql' ||
                    $database->getMorphClass() === 'App\Models\StandaloneMongodb' ||
                    $database->getMorphClass() === 'App\Models\StandaloneMysql' ||
                    $database->getMorphClass() === 'App\Models\StandaloneMariadb')
                <a class="shrink-0 {{ request()->routeIs('project.database.backup.index') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                    href="{{ route('project.database.backup.index', $parameters) }}">
                    {{ __('Backups') }}
                </a>
            @endif
        </nav>
        @if ($database->destination->server->isFunctional())
            <div class="flex flex-wrap gap-2 items-center">
                <div class="md:hidden">
                    <x-dropdown>
                        <x-slot:title>
                            {{ __('Actions') }}
                        </x-slot>
                        @if (!str($database->status)->startsWith('exited'))
                            <x-modal-confirmation title="{{ __('Confirm Database Restart?') }}" buttonTitle="{{ __('Restart') }}" submitAction="restart"
                                :actions="[
                                    __('This database will be unavailable during the restart.'),
                                    __('If the database is currently in use data could be lost.'),
                                ]" :confirmWithText="false" :confirmWithPassword="false" step2ButtonText="{{ __('Restart Database') }}"
                                :dispatchEvent="true" dispatchEventType="restartEvent">
                                <x-slot:trigger>
                                    <div class="dropdown-item dropdown-item-touch">
                                        {{ __('Restart') }}
                                    </div>
                                </x-slot:trigger>
                            </x-modal-confirmation>
                            <x-modal-confirmation title="{{ __('Confirm Database Stopping?') }}" buttonTitle="{{ __('Stop') }}" submitAction="stop"
                                :checkboxes="$checkboxes" :actions="[
                                    __('This database will be stopped.'),
                                    __('If the database is currently in use data could be lost.'),
                                    __('All non-persistent data of this database (containers, networks, unused images) will be deleted (don\'t worry, no data is lost and you can start the database again).'),
                                ]" :confirmWithText="false" :confirmWithPassword="false"
                                step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
                                <x-slot:trigger>
                                    <div class="dropdown-item dropdown-item-touch text-error">
                                        {{ __('Stop') }}
                                    </div>
                                </x-slot:trigger>
                            </x-modal-confirmation>
                        @else
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('startEvent')">
                                {{ __('Start') }}
                            </div>
                        @endif
                    </x-dropdown>
                </div>
                <div class="hidden flex-wrap items-center gap-2 md:flex">
                    @if (!str($database->status)->startsWith('exited'))
                        <x-modal-confirmation title="{{ __('Confirm Database Restart?') }}" buttonTitle="{{ __('Restart') }}" submitAction="restart"
                            :actions="[
                                __('This database will be unavailable during the restart.'),
                                __('If the database is currently in use data could be lost.'),
                            ]" :confirmWithText="false" :confirmWithPassword="false" step2ButtonText="{{ __('Restart Database') }}"
                            :dispatchEvent="true" dispatchEventType="restartEvent">
                            <x-slot:button-title>
                                <svg class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2">
                                        <path d="M19.933 13.041a8 8 0 1 1-9.925-8.788c3.899-1 7.935 1.007 9.425 4.747" />
                                        <path d="M20 4v5h-5" />
                                    </g>
                                </svg>
                                {{ __('Restart') }}
                            </x-slot:button-title>
                        </x-modal-confirmation>
                        <x-modal-confirmation title="{{ __('Confirm Database Stopping?') }}" buttonTitle="{{ __('Stop') }}" submitAction="stop"
                            :checkboxes="$checkboxes" :actions="[
                                __('This database will be stopped.'),
                                __('If the database is currently in use data could be lost.'),
                                __('All non-persistent data of this database (containers, networks, unused images) will be deleted (don\'t worry, no data is lost and you can start the database again).'),
                            ]" :confirmWithText="false" :confirmWithPassword="false"
                            step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
                            <x-slot:button-title>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-error" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M6 5m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z">
                                    </path>
                                    <path
                                        d="M14 5m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z">
                                    </path>
                                </svg>
                                {{ __('Stop') }}
                            </x-slot:button-title>
                        </x-modal-confirmation>
                    @else
                        <button @click="$wire.dispatch('startEvent')" class="gap-2 button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 4v16l13 -8z" />
                            </svg>
                            {{ __('Start') }}
                        </button>
                    @endif
                </div>
                @script
                    <script>
                        $wire.$on('startEvent', () => {
                            window.dispatchEvent(new CustomEvent('startdatabase'));
                            $wire.$call('start');
                        });
                        $wire.$on('restartEvent', () => {
                            $wire.$dispatch('info', @js(__('Restarting database.')));
                            window.dispatchEvent(new CustomEvent('startdatabase'));
                            $wire.$call('restart');
                        });
                    </script>
                @endscript
            </div>
        @else
            <div class="text-error">{{ __('Underlying server is not functional.') }}</div>
        @endif
    </div>
</nav>
