<div>
    <x-slot:title>
        {{ __('settings.advanced_page.page_title') }} | Coolify
    </x-slot>
    <x-settings.navbar />
    <div x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'general' }"
        class="flex flex-col h-full gap-8 sm:flex-row">
        <x-settings.sidebar activeMenu="advanced" />
        <form wire:submit='submit' class="flex flex-col w-full">
            <div class="flex items-center gap-2">
                <h2>{{ __('settings.advanced_page.title') }}</h2>
                <x-forms.button type="submit">
                    {{ __('button.save') }}
                </x-forms.button>
            </div>
            <div class="pb-4">{{ __('settings.advanced_page.subtitle') }}</div>

            <div class="flex flex-col gap-1">
                @if ($is_registration_enabled)
                    <div class="md:w-96" wire:key="registration-enabled">
                        <x-forms.checkbox instantSave id="is_registration_enabled"
                            :helper="__('settings.advanced_page.registration_helper')"
                            :label="__('settings.advanced_page.registration_allowed')" />
                    </div>
                @else
                    <div class="flex items-center justify-between gap-2 md:w-96"
                        wire:key="registration-disabled">
                        <label class="flex items-center gap-2">
                            {{ __('settings.advanced_page.registration_allowed') }}
                            <x-helper :helper="__('settings.advanced_page.registration_helper')" />
                        </label>
                        <x-modal-confirmation :title="__('settings.advanced_page.enable_registration_title')" :buttonTitle="__('settings.advanced_page.enable_registration_button')" isErrorButton
                            submitAction="toggleRegistration" :actions="__('settings.advanced_page.enable_registration_action') ? [__('settings.advanced_page.enable_registration_action')] : []"
                            :warningMessage="__('settings.advanced_page.enable_registration_warning')"
                            :confirmationText="__('settings.advanced_page.enable_registration_confirmation')"
                            :confirmationLabel="__('settings.advanced_page.enable_registration_confirmation_label')"
                            :shortConfirmationLabel="__('settings.advanced_page.enable_registration_confirmation_short')" />
                    </div>
                @endif
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="do_not_track"
                        :helper="__('settings.advanced_page.do_not_track_helper')"
                        :label="__('settings.advanced_page.do_not_track')" />
                </div>
                <h4 class="pt-4">{{ __('settings.advanced_page.dns_settings') }}</h4>
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="is_dns_validation_enabled"
                        :helper="__('settings.advanced_page.dns_validation_helper')"
                        :label="__('settings.advanced_page.dns_validation')" />
                </div>

                <x-forms.input id="custom_dns_servers" :label="__('settings.advanced_page.custom_dns_servers')"
                    :helper="__('settings.advanced_page.custom_dns_servers_helper')"
                    placeholder="1.1.1.1,8.8.8.8" />
                <h4 class="pt-4">{{ __('settings.advanced_page.api_settings') }}</h4>
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="is_api_enabled" :label="__('settings.advanced_page.api_access')"
                        :helper="__('settings.advanced_page.api_access_helper')" />
                </div>
                <x-forms.input id="allowed_ips" :label="__('settings.advanced_page.allowed_ips')"
                    :helper="__('settings.advanced_page.allowed_ips_helper')"
                    placeholder="192.168.1.100,10.0.0.0/8,203.0.113.0/24" />
                @if (empty($allowed_ips) || in_array('0.0.0.0', array_map('trim', explode(',', $allowed_ips ?? ''))))
                    <x-callout type="warning" :title="__('settings.advanced_page.allowed_ips_warning_title')" class="mt-2">
                        {{ __('settings.advanced_page.allowed_ips_warning') }}
                    </x-callout>
                @endif
                <h4 class="pt-4">{{ __('settings.advanced_page.mcp_server') }}</h4>
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="is_mcp_server_enabled" :label="__('settings.advanced_page.enable_mcp_server')"
                        :helper="__('settings.advanced_page.enable_mcp_server_helper')" />
                </div>
                @if ($is_mcp_server_enabled)
                    <x-callout type="info" :title="__('settings.advanced_page.mcp_endpoint_title')" class="mt-2">
                        {!! __('settings.advanced_page.mcp_endpoint_body', ['endpoint' => url('/mcp')]) !!}
                    </x-callout>
                @endif
                <h4 class="pt-4">{{ __('settings.advanced_page.ui_settings') }}</h4>
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="is_wire_navigate_enabled" :label="__('settings.advanced_page.spa_navigation')"
                        :helper="__('settings.advanced_page.spa_navigation_helper')" />
                </div>
                <h4 class="pt-4">{{ __('settings.advanced_page.confirmation_settings') }}</h4>
                <div class="md:w-96">
                    <x-forms.checkbox instantSave id="is_sponsorship_popup_enabled" :label="__('settings.advanced_page.show_sponsorship_popup')"
                        :helper="__('settings.advanced_page.show_sponsorship_popup_helper')" />
                </div>
            </div>
            <div class="flex flex-col gap-1">
                @if ($disable_two_step_confirmation)
                    <div class="pb-4 md:w-96" wire:key="two-step-confirmation-enabled">
                        <x-forms.checkbox instantSave id="disable_two_step_confirmation"
                            :label="__('settings.advanced_page.disable_two_step')"
                            :helper="__('settings.advanced_page.disable_two_step_helper')" />
                    </div>
                @else
                    <div class="pb-4 flex items-center justify-between gap-2 md:w-96"
                        wire:key="two-step-confirmation-disabled">
                        <label class="flex items-center gap-2">
                            {{ __('settings.advanced_page.disable_two_step') }}
                            <x-helper :helper="__('settings.advanced_page.disable_two_step_helper')" />
                        </label>
                        <x-modal-confirmation :title="__('settings.advanced_page.disable_two_step_title')" :buttonTitle="__('settings.advanced_page.disable_two_step_button')" isErrorButton
                            submitAction="toggleTwoStepConfirmation" :actions="__('settings.advanced_page.disable_two_step_actions')"
                            :confirmationText="__('settings.advanced_page.disable_two_step_confirmation')"
                            :confirmationLabel="__('settings.advanced_page.disable_two_step_confirmation_label')"
                            :shortConfirmationLabel="__('settings.advanced_page.disable_two_step_confirmation_short')" />
                    </div>
                    <x-callout type="danger" :title="__('settings.advanced_page.disable_two_step_warning_title')" class="mb-4">
                        {{ __('settings.advanced_page.disable_two_step_warning') }}
                    </x-callout>
                @endif
            </div>
        </form>
    </div>
</div>
