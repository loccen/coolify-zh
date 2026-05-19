<?php

namespace App\Livewire;

use App\Models\OauthSetting;
use Livewire\Component;

class SettingsOauth extends Component
{
    public $oauth_settings_map;

    protected function rules()
    {
        return OauthSetting::all()->reduce(function ($carry, $setting) {
            $carry["oauth_settings_map.$setting->provider.enabled"] = 'required';
            $carry["oauth_settings_map.$setting->provider.client_id"] = 'nullable';
            $carry["oauth_settings_map.$setting->provider.client_secret"] = 'nullable';
            $carry["oauth_settings_map.$setting->provider.redirect_uri"] = 'nullable';
            $carry["oauth_settings_map.$setting->provider.tenant"] = 'nullable';
            $carry["oauth_settings_map.$setting->provider.base_url"] = 'nullable';

            return $carry;
        }, []);
    }

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('home');
        }
        $this->oauth_settings_map = OauthSetting::all()->sortBy('provider')->reduce(function ($carry, $setting) {
            $carry[$setting->provider] = [
                'id' => $setting->id,
                'provider' => $setting->provider,
                'enabled' => $setting->enabled,
                'client_id' => $setting->client_id,
                'client_secret' => $setting->client_secret,
                'redirect_uri' => $setting->redirect_uri,
                'tenant' => $setting->tenant,
                'base_url' => $setting->base_url,
            ];

            return $carry;
        }, []);
    }

    private function updateOauthSettings(?string $provider = null)
    {
        if ($provider) {
            $oauthData = $this->oauth_settings_map[$provider];
            $oauth = OauthSetting::find($oauthData['id']);

            if (! $oauth) {
                throw new \Exception(__('settings.oauth_page.provider_not_found', ['provider' => $provider]));
            }

            $oauth->fill([
                'enabled' => $oauthData['enabled'],
                'client_id' => $oauthData['client_id'],
                'client_secret' => $oauthData['client_secret'],
                'redirect_uri' => $oauthData['redirect_uri'],
                'tenant' => $oauthData['tenant'],
                'base_url' => $oauthData['base_url'],
            ]);

            if (! $oauth->couldBeEnabled()) {
                $oauth->update(['enabled' => false]);
                throw new \Exception(__('settings.oauth_page.provider_incomplete', ['provider' => $oauth->provider]));
            }
            $oauth->save();

            // Update the array with fresh data
            $this->oauth_settings_map[$provider] = [
                'id' => $oauth->id,
                'provider' => $oauth->provider,
                'enabled' => $oauth->enabled,
                'client_id' => $oauth->client_id,
                'client_secret' => $oauth->client_secret,
                'redirect_uri' => $oauth->redirect_uri,
                'tenant' => $oauth->tenant,
                'base_url' => $oauth->base_url,
            ];

            $this->dispatch('success', __('settings.oauth_page.provider_updated', ['provider' => $oauth->provider]));
        } else {
            $errors = [];
            foreach (array_values($this->oauth_settings_map) as $settingData) {
                $oauth = OauthSetting::find($settingData['id']);

                if (! $oauth) {
                    $errors[] = __('settings.oauth_page.provider_not_found', ['provider' => $settingData['provider']]);

                    continue;
                }

                $oauth->fill([
                    'enabled' => $settingData['enabled'],
                    'client_id' => $settingData['client_id'],
                    'client_secret' => $settingData['client_secret'],
                    'redirect_uri' => $settingData['redirect_uri'],
                    'tenant' => $settingData['tenant'],
                    'base_url' => $settingData['base_url'],
                ]);

                if ($settingData['enabled'] && ! $oauth->couldBeEnabled()) {
                    $oauth->enabled = false;
                    $errors[] = __('settings.oauth_page.provider_incomplete_disabled', ['provider' => $oauth->provider]);
                }

                $oauth->save();

                // Update the array with fresh data
                $this->oauth_settings_map[$oauth->provider] = [
                    'id' => $oauth->id,
                    'provider' => $oauth->provider,
                    'enabled' => $oauth->enabled,
                    'client_id' => $oauth->client_id,
                    'client_secret' => $oauth->client_secret,
                    'redirect_uri' => $oauth->redirect_uri,
                    'tenant' => $oauth->tenant,
                    'base_url' => $oauth->base_url,
                ];
            }

            if (! empty($errors)) {
                $this->dispatch('error', implode('<br/>', $errors));
            }
        }
    }

    public function instantSave(string $provider)
    {
        try {
            $this->updateOauthSettings($provider);
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function submit()
    {
        $this->updateOauthSettings();
        $this->dispatch('success', __('settings.instance_updated'));
    }
}
