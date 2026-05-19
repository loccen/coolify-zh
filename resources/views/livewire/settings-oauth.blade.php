<div>
    <x-slot:title>
        {{ __('settings.oauth_page.title') }} | Coolify
    </x-slot>
    <x-settings.navbar />
    <form wire:submit='submit' class="flex flex-col">
        <div class="flex flex-col">
            <div class="flex items-center gap-2 pb-2">
                <h2>{{ __('settings.oauth_page.heading') }}</h2>
                <x-forms.button type="submit">
                    {{ __('button.save') }}
                </x-forms.button>
            </div>
            <div class="pb-4 ">{{ __('settings.oauth_page.subtitle') }}</div>
        </div>
        <div class="flex flex-col gap-2 pt-4">
            @foreach ($oauth_settings_map as $oauth_setting)
                <div class="p-4 border dark:border-coolgray-300 border-neutral-200">
                    <h3>{{ ucfirst($oauth_setting['provider']) }}</h3>
                    <div class="w-32">
                        <x-forms.checkbox instantSave="instantSave('{{ $oauth_setting['provider'] }}')"
                            id="oauth_settings_map.{{ $oauth_setting['provider'] }}.enabled" :label="__('settings.oauth_page.enabled')" />
                    </div>
                    <div class="flex flex-col w-full gap-2 xl:flex-row">
                        <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.client_id"
                            :label="__('settings.oauth_page.client_id')" />
                        <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.client_secret"
                            type="password" :label="__('settings.oauth_page.client_secret')" autocomplete="new-password" />
                        <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.redirect_uri"
                            placeholder="{{ route('auth.callback', $oauth_setting['provider']) }}" :label="__('settings.oauth_page.redirect_uri')" />
                        @if ($oauth_setting['provider'] == 'azure')
                            <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.tenant"
                                :label="__('settings.oauth_page.tenant')" />
                        @endif
                        @if ($oauth_setting['provider'] == 'google')
                            <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.tenant"
                                :helper="__('settings.oauth_page.tenant_google_helper')"
                                :label="__('settings.oauth_page.tenant')" />
                        @endif
                        @if (
                            $oauth_setting['provider'] == 'authentik' ||
                                $oauth_setting['provider'] == 'clerk' ||
                                $oauth_setting['provider'] == 'zitadel' ||
                                $oauth_setting['provider'] == 'gitlab')
                            <x-forms.input id="oauth_settings_map.{{ $oauth_setting['provider'] }}.base_url"
                                :label="__('settings.oauth_page.base_url')" />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
