<div wire:poll.10000ms="checkStatus" class="pb-6">
    <livewire:project.shared.configuration-checker :resource="$service" />
    <x-slide-over @startservice.window="slideOverOpen = true" closeWithX fullScreen>
        <x-slot:title>{{ __('Service Startup') }}</x-slot:title>
        <x-slot:content>
            <livewire:activity-monitor header="{{ __('Logs') }}" fullHeight />
        </x-slot:content>
    </x-slide-over>
    <h1>{{ __($title) }}</h1>
    <x-resources.breadcrumbs :resource="$service" :parameters="$parameters" />
    <div class="navbar-main" x-data">
        <nav
            class="scrollbar flex min-h-10 w-full flex-nowrap items-center gap-6 overflow-x-scroll overflow-y-hidden pb-1 whitespace-nowrap md:w-auto md:overflow-visible">
            <a class="shrink-0 {{ request()->routeIs('project.service.configuration') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('project.service.configuration', $parameters) }}">
                <button>{{ __('Configuration') }}</button>
            </a>
            <a class="shrink-0 {{ request()->routeIs('project.service.logs') ? 'dark:text-white' : '' }}"
                href="{{ route('project.service.logs', $parameters) }}">
                <button>{{ __('Logs') }}</button>
            </a>
            @can('canAccessTerminal')
                <a class="shrink-0 {{ request()->routeIs('project.service.command') ? 'dark:text-white' : '' }}"
                    href="{{ route('project.service.command', $parameters) }}">
                    <button>{{ __('Terminal') }}</button>
                </a>
            @endcan
            <div class="shrink-0">
                <x-services.links :service="$service" />
            </div>
        </nav>
        @if ($service->isDeployable)
            <div class="order-first flex flex-wrap items-center gap-2 sm:order-last">
                <div class="md:hidden">
                    <x-dropdown>
                        <x-slot:title>
                            {{ __('Actions') }}
                        </x-slot>
                        @if (str($service->status)->contains('running'))
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('restartEvent')">
                                {{ __('Restart') }}
                            </div>
                            <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                                submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                                :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
                                <x-slot:trigger>
                                    <div class="dropdown-item dropdown-item-touch text-error">
                                        {{ __('Stop') }}
                                    </div>
                                </x-slot:trigger>
                            </x-modal-confirmation>
                            <div class="mx-2 my-1 border-t border-neutral-200 dark:border-coolgray-300"></div>
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('pullAndRestartEvent')">
                                {{ __('Pull Latest Images & Restart') }}
                            </div>
                        @elseif (str($service->status)->contains('degraded'))
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('restartEvent')">
                                {{ __('Restart') }}
                            </div>
                            <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                                submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                                :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
                                <x-slot:trigger>
                                    <div class="dropdown-item dropdown-item-touch text-error">
                                        {{ __('Stop') }}
                                    </div>
                                </x-slot:trigger>
                            </x-modal-confirmation>
                            <div class="mx-2 my-1 border-t border-neutral-200 dark:border-coolgray-300"></div>
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('forceDeployEvent')">
                                {{ __('Force Restart') }}
                            </div>
                        @elseif (str($service->status)->contains('exited'))
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('startEvent')">
                                {{ __('Deploy') }}
                            </div>
                            <div class="mx-2 my-1 border-t border-neutral-200 dark:border-coolgray-300"></div>
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('forceDeployEvent')">
                                {{ __('Force Deploy') }}
                            </div>
                            <div class="dropdown-item dropdown-item-touch" wire:click='stop(true)'>
                                {{ __('Force Cleanup Containers') }}
                            </div>
                        @else
                            <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                                submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                                :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
                                <x-slot:trigger>
                                    <div class="dropdown-item dropdown-item-touch text-error">
                                        {{ __('Stop') }}
                                    </div>
                                </x-slot:trigger>
                            </x-modal-confirmation>
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('startEvent')">
                                {{ __('Deploy') }}
                            </div>
                            <div class="mx-2 my-1 border-t border-neutral-200 dark:border-coolgray-300"></div>
                            <div class="dropdown-item dropdown-item-touch" @click="$wire.dispatch('forceDeployEvent')">
                                {{ __('Force Deploy') }}
                            </div>
                            <div class="dropdown-item dropdown-item-touch" wire:click='stop(true)'>
                                {{ __('Force Cleanup Containers') }}
                            </div>
                        @endif
                    </x-dropdown>
                </div>
                <div class="hidden flex-wrap items-center gap-2 md:flex">
                    <x-services.advanced :service="$service" />
                    @if (str($service->status)->contains('running'))
                        <x-forms.button title="{{ __('Restart') }}" @click="$wire.dispatch('restartEvent')">
                            <svg class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path d="M19.933 13.041 a8 8 0 1 1-9.925-8.788c3.899-1 7.935 1.007 9.425 4.747" />
                                    <path d="M20 4v5h-5" />
                                </g>
                            </svg>
                            {{ __('Restart') }}
                        </x-forms.button>
                        <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                            submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                            :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
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
                    @elseif (str($service->status)->contains('degraded'))
                        <x-forms.button title="{{ __('Restart') }}" @click="$wire.dispatch('restartEvent')">
                            <svg class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path d="M19.933 13.041a8 8 0 1 1-9.925-8.788c3.899-1 7.935 1.007 9.425 4.747" />
                                    <path d="M20 4v5h-5" />
                                </g>
                            </svg>
                            {{ __('Restart') }}
                        </x-forms.button>
                        <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                            submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                            :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
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
                    @elseif (str($service->status)->contains('exited'))
                        <button @click="$wire.dispatch('startEvent')" class="gap-2 button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 4v16l13 -8z" />
                            </svg>
                            {{ __('Deploy') }}
                        </button>
                    @else
                        <x-modal-confirmation title="{{ __('Confirm Service Stopping?') }}" buttonTitle="{{ __('Stop') }}" :dispatchEvent="true"
                            submitAction="stop" dispatchEventType="stopEvent" :checkboxes="$checkboxes" :actions="[__('service.stop'), __('resource.non_persistent')]"
                            :confirmWithText="false" :confirmWithPassword="false" step1ButtonText="{{ __('Continue') }}" step2ButtonText="{{ __('Confirm') }}">
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
                        <button @click="$wire.dispatch('startEvent')" class="gap-2 button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 dark:text-warning" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 4v16l13 -8z" />
                            </svg>
                            {{ __('Deploy') }}
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="flex flex-wrap order-first gap-2 items-center sm:order-last">
                <div class="text-error">
                    {{ __('Unable to deploy.') }} <a class="underline font-bold cursor-pointer" {{ wireNavigate() }}
                        href="{{ route('project.service.environment-variables', $parameters) }}">
                        {{ __('Required environment variables missing.') }}</a>
                </div>
            </div>
        @endif
    </div>
    @script
        <script>
            $wire.$on('stopEvent', () => {
                $wire.$dispatch('info',
                    @js(__('Gracefully stopping service.<br/><br/>It could take a while depending on the service.')));
                $wire.$call('stop');
            });
            $wire.$on('startEvent', async () => {
                const isDeploymentProgress = await $wire.$call('checkDeployments');
                if (isDeploymentProgress) {
                    $wire.$dispatch('error',
                        @js(__('There is a deployment in progress.<br><br>You can force deploy in the "Advanced" section.'))
                    );
                    return;
                }
                window.dispatchEvent(new CustomEvent('startservice'));
                $wire.$call('start');
            });
            $wire.$on('forceDeployEvent', () => {
                window.dispatchEvent(new CustomEvent('startservice'));
                $wire.$call('forceDeploy');
            });
            $wire.$on('restartEvent', async () => {
                const isDeploymentProgress = await $wire.$call('checkDeployments');
                if (isDeploymentProgress) {
                    $wire.$dispatch('error',
                        @js(__('There is a deployment in progress.<br><br>You can force deploy in the "Advanced" section.'))
                    );
                    return;
                }
                $wire.$dispatch('info',
                    @js(__('Gracefully stopping service.<br/><br/>It could take a while depending on the service.')));
                window.dispatchEvent(new CustomEvent('startservice'));
                $wire.$call('restart');
            });
            $wire.$on('pullAndRestartEvent', () => {
                $wire.$dispatch('info', @js(__('Pulling new images and restarting service.')));
                window.dispatchEvent(new CustomEvent('startservice'));
                $wire.$call('pullAndRestartEvent');
            });
            $wire.on('imagePulled', () => {
                window.dispatchEvent(new CustomEvent('startservice'));
                $wire.$dispatch('info', @js(__('Restarting service.')));
            });
        </script>
    @endscript
</div>
