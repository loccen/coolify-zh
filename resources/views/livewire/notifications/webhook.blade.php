<div>
    <x-slot:title>
        {{ __('Notifications') }} | Coolify
    </x-slot>
    <x-notification.navbar />
    <form wire:submit='submit' class="flex flex-col gap-4 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('Webhook') }}</h2>
            <x-forms.button canGate="update" :canResource="$settings" type="submit">
                {{ __('Save') }}
            </x-forms.button>
            @if ($webhookEnabled)
                <x-forms.button canGate="sendTest" :canResource="$settings"
                    class="normal-case dark:text-white btn btn-xs no-animation btn-primary"
                    wire:click="sendTestNotification">
                    {{ __('Send Test Notification') }}
                </x-forms.button>
            @else
                <x-forms.button canGate="sendTest" :canResource="$settings" disabled
                    class="normal-case dark:text-white btn btn-xs no-animation btn-primary">
                    {{ __('Send Test Notification') }}
                </x-forms.button>
            @endif
        </div>
        <div class="w-48">
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSaveWebhookEnabled"
                id="webhookEnabled" :label="__('Enabled')" />
        </div>
        <div class="flex items-end gap-2">

            <x-forms.input canGate="update" :canResource="$settings" type="password"
                :helper="__('Enter a valid HTTP or HTTPS URL. Coolify will send POST requests to this endpoint when events occur.')"
                required id="webhookUrl" :label="__('Webhook URL (POST)')" />
        </div>
    </form>
    <h2 class="mt-4">{{ __('Notification Settings') }}</h2>
    <p class="mb-4">
        {{ __('Select events for which you would like to receive webhook notifications.') }}
    </p>
    <div class="flex flex-col gap-4 max-w-2xl">
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Deployments') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="deploymentSuccessWebhookNotifications" :label="__('Deployment Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="deploymentFailureWebhookNotifications" :label="__('Deployment Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    :helper="__('Send a notification when a container status changes. It will notify for Stopped and Restarted events of a container.')"
                    id="statusChangeWebhookNotifications" :label="__('Container Status Changes')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Backups') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="backupSuccessWebhookNotifications" :label="__('Backup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="backupFailureWebhookNotifications" :label="__('Backup Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Scheduled Tasks') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="scheduledTaskSuccessWebhookNotifications" :label="__('Scheduled Task Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="scheduledTaskFailureWebhookNotifications" :label="__('Scheduled Task Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Server') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="dockerCleanupSuccessWebhookNotifications" :label="__('Docker Cleanup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="dockerCleanupFailureWebhookNotifications" :label="__('Docker Cleanup Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="serverDiskUsageWebhookNotifications" :label="__('Server Disk Usage')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="serverReachableWebhookNotifications" :label="__('Server Reachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="serverUnreachableWebhookNotifications" :label="__('Server Unreachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="serverPatchWebhookNotifications" :label="__('Server Patching')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    id="traefikOutdatedWebhookNotifications" :label="__('Traefik Proxy Outdated')" />
            </div>
        </div>
    </div>
</div>
