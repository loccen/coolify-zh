<div>
    <x-slot:title>
        {{ __('settings.email_page.title') }} | Coolify
    </x-slot>
    <x-settings.navbar />
    <form wire:submit='submit' class="flex flex-col gap-2 pb-4">
        <div class="flex items-center gap-2">
            <h2>{{ __('settings.email_page.title') }}</h2>
            <x-forms.button type="submit">
                {{ __('button.save') }}
            </x-forms.button>
            @if (is_transactional_emails_enabled() && auth()->user()->isAdminFromSession())
                <x-modal-input :buttonTitle="__('settings.email_page.send_test_email')" :title="__('settings.email_page.send_test_email')">
                    <form wire:submit.prevent="sendTestEmail" class="flex flex-col w-full gap-2">
                        <x-forms.input wire:model="testEmailAddress" placeholder="test@example.com" id="testEmailAddress"
                            :label="__('settings.email_page.recipient')" required />
                        <x-forms.button type="submit" @click="modalOpen=false">
                            {{ __('settings.email_page.send_email') }}
                        </x-forms.button>
                    </form>
                </x-modal-input>
            @endif
        </div>
        <div class="pb-4">{{ __('settings.email_page.subtitle') }}</div>
        <div class="flex gap-2">
            <x-forms.input required id="smtpFromName" :helper="__('settings.email_page.from_name_helper')" :label="__('settings.email_page.from_name')" />
            <x-forms.input required id="smtpFromAddress" :helper="__('settings.email_page.from_address_helper')" :label="__('settings.email_page.from_address')" />
        </div>
    </form>
    <div class="flex flex-col gap-4">
        <div class="p-4 border dark:border-coolgray-300 border-neutral-200">
            <form wire:submit.prevent="submitSmtp" class="flex flex-col">
                <div class="flex gap-2">
                    <h3>{{ __('settings.email_page.smtp_server') }}</h3>
                    <x-forms.button type="submit">
                        {{ __('button.save') }}
                    </x-forms.button>
                </div>
                <div class="w-32">
                    <x-forms.checkbox instantSave='instantSave("SMTP")' id="smtpEnabled" :label="__('settings.updates_page.enabled')" />
                </div>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col w-full gap-2 xl:flex-row">
                        <x-forms.input required id="smtpHost" placeholder="smtp.mailgun.org" :label="__('settings.email_page.host')" />
                        <x-forms.input required id="smtpPort" type="number" placeholder="587" :label="__('settings.email_page.port')" />
                        <x-forms.select required id="smtpEncryption" :label="__('settings.email_page.encryption')">
                            <option value="starttls">{{ __('settings.email_page.encryption_starttls') }}</option>
                            <option value="tls">{{ __('settings.email_page.encryption_tls') }}</option>
                            <option value="none">{{ __('settings.email_page.encryption_none') }}</option>
                        </x-forms.select>
                    </div>
                    <div class="flex flex-col w-full gap-2 xl:flex-row">
                        <x-forms.input id="smtpUsername" :label="__('settings.email_page.smtp_username')" />
                        <x-forms.input id="smtpPassword" type="password" :label="__('settings.email_page.smtp_password')"
                            autocomplete="new-password" />
                        <x-forms.input id="smtpTimeout" type="number" :helper="__('settings.email_page.timeout_helper')" :label="__('settings.email_page.timeout')" />
                    </div>
                </div>
            </form>
        </div>
        <div class="p-4 border dark:border-coolgray-300 border-neutral-200">
            <form wire:submit.prevent="submitResend" class="flex flex-col">
                <div class="flex gap-2">
                    <h3>{{ __('settings.email_page.resend') }}</h3>
                    <x-forms.button type="submit">
                        {{ __('button.save') }}
                    </x-forms.button>
                </div>
                <div class="w-32">
                    <x-forms.checkbox instantSave='instantSave("Resend")' id="resendEnabled" :label="__('settings.updates_page.enabled')" />
                </div>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col w-full gap-2 xl:flex-row">
                        <x-forms.input type="password" id="resendApiKey" placeholder="API key" required :label="__('settings.email_page.api_key')"
                            autocomplete="new-password" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
