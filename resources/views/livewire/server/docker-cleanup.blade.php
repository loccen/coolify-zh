<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Docker Cleanup') }} | Coolify
    </x-slot>
    <livewire:server.navbar :server="$server" />
    <div x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'general' }" class="flex flex-col h-full gap-8 sm:flex-row">
        <x-server.sidebar :server="$server" activeMenu="docker-cleanup" />
        <div class="w-full">
            <form wire:submit='submit'>
                <div>
                    <div class="flex items-center gap-2">
                        <h2>{{ __('Docker Cleanup') }}</h2>
                        <x-forms.button type="submit" canGate="update" :canResource="$server">{{ __('Save') }}</x-forms.button>
                        @can('update', $server)
                            <x-modal-confirmation :title="__('Confirm Docker Cleanup?')" :buttonTitle="__('Trigger Manual Cleanup')"
                                isHighlightedButton submitAction="manualCleanup" :actions="[
                                    __('Permanently deletes all stopped containers managed by Coolify (as containers are non-persistent, no data will be lost)'),
                                    __('Permanently deletes all unused images'),
                                    __('Clears build cache'),
                                    __('Removes old versions of the Coolify helper image'),
                                    __('Optionally permanently deletes all unused volumes (if enabled in advanced options).'),
                                    __('Optionally permanently deletes all unused networks (if enabled in advanced options).'),
                                ]" :confirmWithText="false"
                                :confirmWithPassword="false" :step2ButtonText="__('Trigger Docker Cleanup')" />
                        @endcan
                    </div>
                    <div class="mt-1 mb-6">{{ __('Configure Docker cleanup settings for your server.') }}</div>
                </div>

                @if (!isCloud() && $this->isCleanupStale)
                    <div class="mb-4">
                        <x-callout type="warning" :title="__('Docker Cleanup May Be Stalled')">
                            <p>{{ __('The last Docker cleanup ran :time ago, which is longer than expected for the configured frequency.', ['time' => $this->lastExecutionTime ?? __('unknown time')]) }}</p>
                            @if (!$this->isSchedulerHealthy)
                                <p class="mt-1">{{ __('The scheduled job manager appears to be inactive. This may indicate a stale Redis lock is blocking all scheduled jobs.') }}</p>
                            @endif
                            <p class="mt-2">{{ __('To resolve, run on your Coolify instance:') }}
                                <code class="bg-black/10 dark:bg-white/10 px-1 rounded">php artisan cleanup:redis --clear-locks</code>
                            </p>
                        </x-callout>
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <div class="flex gap-4">
                        <h3>{{ __('Cleanup Configuration') }}</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <x-forms.input canGate="update" :canResource="$server" placeholder="*/10 * * * *"
                            id="dockerCleanupFrequency" :label="__('Docker cleanup frequency')" required
                            :helper="__('Cron expression for Docker Cleanup.<br>You can use every_minute, hourly, daily, weekly, monthly, yearly.<br><br>Default is every night at midnight.')" />
                        @if (!$forceDockerCleanup)
                            <x-forms.input canGate="update" :canResource="$server" id="dockerCleanupThreshold"
                                :label="__('Docker cleanup threshold (%)')" required
                                :helper="__('The Docker cleanup tasks will run when the disk usage exceeds this threshold.')" />
                        @endif
                    </div>
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$server"
                            :helper="__('Enabling Force Docker Cleanup or manually triggering a cleanup will perform the following actions:
                            <ul class='list-disc pl-4 mt-2'>
                                <li>Removes stopped containers managed by Coolify (as containers are non-persistent, no data will be lost).</li>
                                <li>Deletes unused images.</li>
                                <li>Clears build cache.</li>
                                <li>Removes old versions of the Coolify helper image.</li>
                                <li>Optionally delete unused volumes (if enabled in advanced options).</li>
                                <li>Optionally remove unused networks (if enabled in advanced options).</li>
                            </ul>')"
                            instantSave id="forceDockerCleanup" :label="__('Force Docker Cleanup')" />
                    </div>

                </div>

                <div class="flex flex-col gap-2 mt-6">
                    <h3>{{ __('Advanced') }}</h3>
                    <x-callout type="warning" :title="__('Caution')">
                        <p>{{ __('These options can cause permanent data loss and functional issues. Only enable if you fully understand the consequences.') }}</p>
                    </x-callout>
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$server" instantSave id="deleteUnusedVolumes"
                            :label="__('Delete Unused Volumes')"
                            :helper="__('This option will remove all unused Docker volumes during cleanup.<br><br><strong>Warning: Data from stopped containers will be lost!</strong><br><br>Consequences include:<br>
                            <ul class='list-disc pl-4 mt-2'>
                                <li>Volumes not attached to running containers will be permanently deleted (volumes from stopped containers are affected).</li>
                                <li>Data stored in deleted volumes cannot be recovered.</li>
                            </ul>')" />
                        <x-forms.checkbox canGate="update" :canResource="$server" instantSave id="deleteUnusedNetworks"
                            :label="__('Delete Unused Networks')"
                            :helper="__('This option will remove all unused Docker networks during cleanup.<br><br><strong>Warning: Functionality may be lost and containers may not be able to communicate with each other!</strong><br><br>Consequences include:<br>
                            <ul class='list-disc pl-4 mt-2'>
                                <li>Networks not attached to running containers will be permanently deleted (networks used by stopped containers are affected).</li>
                                <li>Containers may lose connectivity if required networks are removed.</li>
                            </ul>')" />
                        <x-forms.checkbox canGate="update" :canResource="$server" instantSave
                            id="disableApplicationImageRetention"
                            :label="__('Disable Application Image Retention')"
                            :helper="__('When enabled, Docker cleanup will delete all old application images regardless of per-application retention settings. Only the currently running image will be kept.<br><br><strong>Warning: This disables rollback capabilities for all applications on this server.</strong>')" />
                    </div>
                </div>
            </form>

            <div class="mt-8">
                <h3 class="mb-4">{{ __('Recent executions') }} <span class="text-xs text-neutral-500">({{ __('click to check output') }})</span></h3>
                <livewire:server.docker-cleanup-executions :server="$server" />
            </div>
        </div>
    </div>
</div>
