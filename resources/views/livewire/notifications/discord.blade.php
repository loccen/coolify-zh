<div>
    <x-slot:title>
        {{ __('Notifications') }} | Coolify
    </x-slot>
    <x-notification.navbar />
    <form wire:submit='submit' class="flex flex-col gap-4 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('Discord') }}</h2>
            <x-forms.button canGate="update" :canResource="$settings" type="submit">
                {{ __('Save') }}
            </x-forms.button>
            @if ($discordEnabled)
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
        <div class="w-48">
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSaveDiscordEnabled" id="discordEnabled" :label="__('Enabled')" />
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSaveDiscordPingEnabled" id="discordPingEnabled"
                :helper="__('If enabled, a ping (@here) will be sent to the notification when a critical event happens.')"
                :label="__('Ping Enabled')" />
        </div>
        <x-forms.input canGate="update" :canResource="$settings" type="password"
            :helper="__('Create a Discord server and generate a Webhook URL. <br><a class=\'inline-block underline dark:text-white\' href=\'https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks\' target=\'_blank\'>Webhook Documentation</a>')"
            required id="discordWebhookUrl" :label="__('Webhook')" />
    </form>
    <h2 class="mt-4">{{ __('Notification Settings') }}</h2>
    <p class="mb-4">
        {{ __('Select events for which you would like to receive Discord notifications.') }}
    </p>
    <div class="flex flex-col gap-4 max-w-2xl">
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Deployments') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentSuccessDiscordNotifications"
                    :label="__('Deployment Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentFailureDiscordNotifications"
                    :label="__('Deployment Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    :helper="__('Send a notification when a container status changes. It will notify for Stopped and Restarted events of a container.')"
                    id="statusChangeDiscordNotifications" :label="__('Container Status Changes')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Backups') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupSuccessDiscordNotifications"
                    :label="__('Backup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupFailureDiscordNotifications"
                    :label="__('Backup Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Scheduled Tasks') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskSuccessDiscordNotifications"
                    :label="__('Scheduled Task Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskFailureDiscordNotifications"
                    :label="__('Scheduled Task Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Server') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupSuccessDiscordNotifications"
                    :label="__('Docker Cleanup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupFailureDiscordNotifications"
                    :label="__('Docker Cleanup Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverDiskUsageDiscordNotifications"
                    :label="__('Server Disk Usage')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverReachableDiscordNotifications"
                    :label="__('Server Reachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverUnreachableDiscordNotifications"
                    :label="__('Server Unreachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverPatchDiscordNotifications"
                    :label="__('Server Patching')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="traefikOutdatedDiscordNotifications"
                    :label="__('Traefik Proxy Outdated')" />
    </div>
</div>
    </div>
</div>
