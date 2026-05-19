<div>
    <x-slot:title>
        {{ __('Notifications') }} | Coolify
    </x-slot>
    <x-notification.navbar />
    <form wire:submit='submit' class="flex flex-col gap-4 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('Email') }}</h2>
            <x-forms.button canGate="update" :canResource="$settings" type="submit">
                {{ __('Save') }}
            </x-forms.button>
            @if (auth()->user()->isAdminFromSession())
                @can('sendTest', $settings)
                    @if ($team->isNotificationEnabled('email'))
                        <x-modal-input :buttonTitle="__('Send Test Email')" :title="__('Send Test Email')">
                            <form wire:submit.prevent="sendTestEmail" class="flex flex-col w-full gap-2">
                                <x-forms.input wire:model="testEmailAddress" placeholder="test@example.com"
                                    id="testEmailAddress" :label="__('Recipient')" required />
                                <x-forms.button type="submit" @click="modalOpen=false">
                                    {{ __('Send Email') }}
                                </x-forms.button>
                            </form>
                        </x-modal-input>
                    @else
                        <x-forms.button disabled class="normal-case dark:text-white btn btn-xs no-animation btn-primary">
                            {{ __('Send Test Email') }}
                        </x-forms.button>
                    @endif
                @endcan
            @endif
        </div>
        @if (!isCloud())
            <div class="w-full sm:w-96">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSave()" id="useInstanceEmailSettings"
                    :label="__('Use system-wide transactional email settings')" />
            </div>
        @endif
        @if (!$useInstanceEmailSettings)
            <div class="flex gap-2">
                <x-forms.input canGate="update" :canResource="$settings" required id="smtpFromName" :helper="__('Name used in emails.')" :label="__('From Name')" />
                <x-forms.input canGate="update" :canResource="$settings" required id="smtpFromAddress" :helper="__('Email address used in emails.')"
                    :label="__('From Address')" />
            </div>
            @if (isInstanceAdmin() && !$useInstanceEmailSettings)
                <x-forms.button canGate="update" :canResource="$settings" wire:click='copyFromInstanceSettings'>
                    {{ __('Copy from Instance Settings') }}
                </x-forms.button>
            @endif
        @endif
    </form>
    @if (isCloud())
        <div class="w-64 py-4">
            <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="instantSave()" id="useInstanceEmailSettings"
                :label="__('Use Hosted Email Service')" />
        </div>
    @endif
    @if (!$useInstanceEmailSettings)
        <div class="flex flex-col gap-4">
            <form wire:submit='submitSmtp'
                class="p-4 border dark:border-coolgray-300 border-neutral-200 rounded-lg flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <h3>{{ __('SMTP Server') }}</h3>
                    <x-forms.button canGate="update" :canResource="$settings" type="submit">
                        {{ __('Save') }}
                    </x-forms.button>
                </div>
                <div class="w-32">
                    <x-forms.checkbox canGate="update" :canResource="$settings" wire:model="smtpEnabled" instantSave="instantSave('SMTP')" id="smtpEnabled"
                        :label="__('Enabled')" />
                </div>
                <div class="flex flex-col">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col w-full gap-2 xl:flex-row">
                            <x-forms.input canGate="update" :canResource="$settings" required id="smtpHost" placeholder="smtp.mailgun.org" :label="__('Host')" />
                            <x-forms.input canGate="update" :canResource="$settings" required id="smtpPort" type="number" placeholder="587" :label="__('Port')" />
                            <x-forms.select canGate="update" :canResource="$settings" required id="smtpEncryption" :label="__('Encryption')">
                                <option value="starttls">StartTLS</option>
                                <option value="tls">TLS/SSL</option>
                                <option value="none">{{ __('None') }}</option>
                            </x-forms.select>
                        </div>
                        <div class="flex flex-col w-full gap-2 xl:flex-row">
                            <x-forms.input canGate="update" :canResource="$settings" id="smtpUsername" :label="__('SMTP Username')" />
                            <x-forms.input canGate="update" :canResource="$settings" id="smtpPassword" type="password" :label="__('SMTP Password')" />
                            <x-forms.input canGate="update" :canResource="$settings" id="smtpTimeout" type="number"  :helper="__('Timeout value for sending emails.')"
                                :label="__('Timeout')" />
                        </div>
                    </div>
                </div>
            </form>
            <form wire:submit='submitResend'
                class="p-4 border dark:border-coolgray-300 border-neutral-200 rounded-lg flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <h3>{{ __('Resend') }}</h3>
                    <x-forms.button canGate="update" :canResource="$settings" type="submit">
                        {{ __('Save') }}
                    </x-forms.button>
                </div>
                <div class="w-32">
                    <x-forms.checkbox canGate="update" :canResource="$settings" wire:model="resendEnabled" instantSave="instantSave('Resend')" id="resendEnabled"
                        :label="__('Enabled')" />
                </div>
                <div class="flex flex-col">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col w-full gap-2 xl:flex-row">
                            <x-forms.input canGate="update" :canResource="$settings" required type="password" id="resendApiKey" placeholder="API key"
                                :label="__('API Key')" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
    <h2 class="mt-4">{{ __('Notification Settings') }}</h2>
    <p class="mb-4">
        {{ __('Select events for which you would like to receive email notifications.') }}
    </p>
    <div class="flex flex-col gap-4 max-w-2xl">
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Deployments') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentSuccessEmailNotifications"
                    :label="__('Deployment Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="deploymentFailureEmailNotifications"
                    :label="__('Deployment Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel"
                    :helper="__('Send an email when a container status changes. It will send an email for Stopped and Restarted events of a container.')"
                    id="statusChangeEmailNotifications" :label="__('Container Status Changes')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Backups') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupSuccessEmailNotifications"
                    :label="__('Backup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="backupFailureEmailNotifications"
                    :label="__('Backup Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Scheduled Tasks') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskSuccessEmailNotifications"
                    :label="__('Scheduled Task Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="scheduledTaskFailureEmailNotifications"
                    :label="__('Scheduled Task Failure')" />
            </div>
        </div>
        <div class="border dark:border-coolgray-300 border-neutral-200 p-4 rounded-lg">
            <h3 class="font-medium mb-3">{{ __('Server') }}</h3>
            <div class="flex flex-col gap-1.5 pl-1">
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupSuccessEmailNotifications"
                    :label="__('Docker Cleanup Success')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="dockerCleanupFailureEmailNotifications"
                    :label="__('Docker Cleanup Failure')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverDiskUsageEmailNotifications"
                    :label="__('Server Disk Usage')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverReachableEmailNotifications"
                    :label="__('Server Reachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverUnreachableEmailNotifications"
                    :label="__('Server Unreachable')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="serverPatchEmailNotifications"
                    :label="__('Server Patching')" />
                <x-forms.checkbox canGate="update" :canResource="$settings" instantSave="saveModel" id="traefikOutdatedEmailNotifications"
                    :label="__('Traefik Proxy Outdated')" />
            </div>
        </div>
    </div>
</div>
