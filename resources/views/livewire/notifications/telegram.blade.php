<div>
    <x-slot:title>
        {{ __('Notifications') }} | Coolify
    </x-slot>
    <x-notification.navbar />
    <form wire:submit='submit' class="flex flex-col gap-4 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('Telegram') }}</h2>
            <x-forms.button canGate="update" :canResource="$settings" type="submit">
                {{ __('Save') }}
            </x-forms.button>
            @if ($telegramEnabled)
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
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSaveTelegramEnabled" id="telegramEnabled" :label="__('Enabled')" />
        </div>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$settings" type="password" autocomplete="new-password"
                :helper="__('Get it from the <a class=\'inline-block underline dark:text-white\' href=\'https://t.me/botfather\' target=\'_blank\'>BotFather Bot</a> on Telegram.')"
                required id="telegramToken" :label="__('Bot API Token')" />
            <x-forms.input canGate="update" :canResource="$settings" type="password" autocomplete="new-password"
                :helper="__('Add your bot to a group chat and add its Chat ID here.')" required id="telegramChatId"
                :label="__('Chat ID')" />
        </div>
    </form>
    <h2 class="mt-4">{{ __('Notification Settings') }}</h2>
    <p class="mb-4">
        {{ __('Select events for which you would like to receive Telegram notifications.') }}
    </p>
    <div class="flex flex-col gap-4 ">
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="text-lg font-medium mb-3">{{ __('Deployments') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentSuccessTelegramNotifications"
                            :label="__('Deployment Success')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsDeploymentSuccessThreadId" />
                </div>
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentFailureTelegramNotifications"
                            :label="__('Deployment Failure')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsDeploymentFailureThreadId" />
                </div>
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="statusChangeTelegramNotifications"
                            :label="__('Container Status Changes')"
                            :helper="__('Send a notification when a container status changes. It will send a notification for Stopped and Restarted events of a container.')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" id="telegramNotificationsStatusChangeThreadId"
                        :placeholder="__('Custom Telegram Thread ID')" />
                </div>
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="text-lg font-medium mb-3">{{ __('Backups') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupSuccessTelegramNotifications"
                            :label="__('Backup Success')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsBackupSuccessThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupFailureTelegramNotifications"
                            :label="__('Backup Failure')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsBackupFailureThreadId" />
                </div>
            </div>
        </div>

        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="text-lg font-medium mb-3">{{ __('Scheduled Tasks') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskSuccessTelegramNotifications"
                            :label="__('Scheduled Task Success')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsScheduledTaskSuccessThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskFailureTelegramNotifications"
                            :label="__('Scheduled Task Failure')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsScheduledTaskFailureThreadId" />
                </div>
            </div>
        </div>

        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="text-lg font-medium mb-3">{{ __('Server') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupSuccessTelegramNotifications"
                            :label="__('Docker Cleanup Success')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsDockerCleanupSuccessThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupFailureTelegramNotifications"
                            :label="__('Docker Cleanup Failure')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsDockerCleanupFailureThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverDiskUsageTelegramNotifications"
                            :label="__('Server Disk Usage')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsServerDiskUsageThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverReachableTelegramNotifications"
                            :label="__('Server Reachable')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsServerReachableThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverUnreachableTelegramNotifications"
                            :label="__('Server Unreachable')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsServerUnreachableThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverPatchTelegramNotifications"
                            :label="__('Server Patching')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsServerPatchThreadId" />
                </div>

                <div class="pl-1 flex gap-2">
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="traefikOutdatedTelegramNotifications"
                            :label="__('Traefik Proxy Outdated')" />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" type="password" :placeholder="__('Custom Telegram Thread ID')"
                        id="telegramNotificationsTraefikOutdatedThreadId" />
                </div>
            </div>
        </div>
    </div>
</div>
