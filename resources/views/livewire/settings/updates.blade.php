<div>
    <x-slot:title>
        {{ __('settings.updates_page.page_title') }} | Coolify
    </x-slot>
    <x-settings.navbar />
    <div x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'general' }" class="flex flex-col h-full gap-8 sm:flex-row">
        <x-settings.sidebar activeMenu="updates" />
        <form wire:submit='submit' class="flex flex-col w-full">
            <div class="flex items-center gap-2">
                <h2>{{ __('settings.updates_page.title') }}</h2>
                <x-forms.button type="submit">
                    {{ __('button.save') }}
                </x-forms.button>
            </div>
            <div class="pb-4">{{ __('settings.updates_page.subtitle') }}</div>

            <div class="flex flex-col gap-2">
                <div class="flex items-end gap-2">
                    <x-forms.input required id="update_check_frequency" :label="__('settings.updates_page.update_check_frequency')"
                        placeholder="0 * * * *"
                        :helper="__('settings.updates_page.update_check_frequency_helper')" />
                    <x-forms.button wire:click='checkManually'>{{ __('settings.updates_page.check_manually') }}</x-forms.button>
                </div>

                <h4 class="pt-4">{{ __('settings.updates_page.auto_update') }}</h4>

                <div class="text-right md:w-64">
                    @if (!is_null(config('constants.coolify.autoupdate', null)))
                        <div class="text-right">
                            <x-forms.checkbox instantSave
                                :helper="__('settings.updates_page.autoupdate_env_helper')" disabled
                                checked="{{ config('constants.coolify.autoupdate') }}" :label="__('settings.updates_page.enabled')" />
                        </div>
                    @else
                        <x-forms.checkbox instantSave id="is_auto_update_enabled" :label="__('settings.updates_page.enabled')" />
                    @endif
                </div>
                @if (is_null(config('constants.coolify.autoupdate', null)) && $is_auto_update_enabled)
                    <x-forms.input required id="auto_update_frequency" :label="__('settings.updates_page.auto_update_frequency')"
                        placeholder="0 0 * * *"
                        :helper="__('settings.updates_page.auto_update_frequency_helper')" />
                @else
                    <x-forms.input required :label="__('settings.updates_page.auto_update_frequency')" disabled :placeholder="__('settings.updates_page.disabled_placeholder')"
                        :helper="__('settings.updates_page.auto_update_frequency_helper')" />
                @endif
            </div>
        </form>
    </div>
</div>
