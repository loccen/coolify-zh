<div>
    <x-slot:title>
        {{ __('Notifications') }} | Coolify
    </x-slot>
    <x-notification.navbar />
    <form wire:submit='submit' class="flex flex-col gap-4 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('Pushover') }}</h2>
            <x-forms.button canGate="update" :canResource="$settings" type="submit">
                {{ __('Save') }}
            </x-forms.button>
            @if ($pushoverEnabled)
                <x-forms.button canGate="sendTest" :canResource="$settings" class="normal-case dark:text-white btn btn-xs no-animation btn-primary"
                    wire:click="sendTestNotification">
                    {{ __('Send Test Notification') }}
                </x-forms.button>
            @else
                <x-forms.button canGate="sendTest" :canResource="$settings" disabled class="normal-case dark:text-white btn btn-xs no-animation btn-primary">
                    {{ __('Send Test Notification') }}
                </x-forms.button>
            @endif
        </div>
        <div class="w-32">
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSavePushoverEnabled" id="pushoverEnabled" :label="__('Enabled')" />
        </div>
        <div class="flex  gap-2">
            <x-forms.input canGate="update" :canResource="$settings" type="password"
                :helper="__('Get your User Key in Pushover. You need to be logged in to Pushover to see your user key in the top right corner. <br><a class=\'inline-block underline dark:text-white\' href=\'https://pushover.net/\' target=\'_blank\'>Pushover Dashboard</a>')"
                required id="pushoverUserKey" :label="__('User Key')" />
            <x-forms.input canGate="update" :canResource="$settings" type="password"
                :helper="__('Generate an API Token or Key in Pushover by creating a new application. <br><a class=\'inline-block underline dark:text-white\' href=\'https://pushover.net/apps/build\' target=\'_blank\'>Create Pushover Application</a>')"
                required id="pushoverApiToken" :label="__('API Token')" />
        </div>
    </form>
    <h2 class="mt-4">{{ __('Notification Settings') }}</h2>
    <p class="mb-4">
        {{ __('Select events for which you would like to receive Pushover notifications.') }}
    </p>
    <div class="flex flex-col gap-4 max-w-2xl">
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Deployments') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentSuccessPushoverNotifications"
                    :label="__('Deployment Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentFailurePushoverNotifications"
                    :label="__('Deployment Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    :helper="__('Send a notification when a container status changes. It will notify for Stopped and Restarted events of a container.')"
                    id="statusChangePushoverNotifications" :label="__('Container Status Changes')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Backups') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupSuccessPushoverNotifications"
                    :label="__('Backup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupFailurePushoverNotifications"
                    :label="__('Backup Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Scheduled Tasks') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskSuccessPushoverNotifications"
                    :label="__('Scheduled Task Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskFailurePushoverNotifications"
                    :label="__('Scheduled Task Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Server') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupSuccessPushoverNotifications"
                    :label="__('Docker Cleanup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupFailurePushoverNotifications"
                    :label="__('Docker Cleanup Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverDiskUsagePushoverNotifications"
                    :label="__('Server Disk Usage')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverReachablePushoverNotifications"
                    :label="__('Server Reachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverUnreachablePushoverNotifications"
                    :label="__('Server Unreachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverPatchPushoverNotifications"
                    :label="__('Server Patching')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="traefikOutdatedPushoverNotifications"
                    :label="__('Traefik Proxy Outdated')" />
    </div>
</div>
    </div>
</div>
