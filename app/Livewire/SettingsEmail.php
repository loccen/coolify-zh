<?php

namespace App\Livewire;

use App\Models\InstanceSettings;
use App\Models\Team;
use App\Notifications\TransactionalEmails\Test;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SettingsEmail extends Component
{
    public InstanceSettings $settings;

    #[Locked]
    public Team $team;

    #[Validate(['boolean'])]
    public bool $smtpEnabled = false;

    #[Validate(['nullable', 'email'])]
    public ?string $smtpFromAddress = null;

    #[Validate(['nullable', 'string'])]
    public ?string $smtpFromName = null;

    #[Validate(['nullable', 'string'])]
    public ?string $smtpRecipients = null;

    #[Validate(['nullable', 'string'])]
    public ?string $smtpHost = null;

    #[Validate(['nullable', 'numeric', 'min:1', 'max:65535'])]
    public ?string $smtpPort = null;

    #[Validate(['nullable', 'string', 'in:starttls,tls,none'])]
    public ?string $smtpEncryption = 'starttls';

    #[Validate(['nullable', 'string'])]
    public ?string $smtpUsername = null;

    #[Validate(['nullable', 'string'])]
    public ?string $smtpPassword = null;

    #[Validate(['nullable', 'numeric'])]
    public ?string $smtpTimeout = null;

    #[Validate(['boolean'])]
    public bool $resendEnabled = false;

    #[Validate(['nullable', 'string'])]
    public ?string $resendApiKey = null;

    #[Validate(['nullable', 'email'])]
    public ?string $testEmailAddress = null;

    public function mount()
    {
        if (isInstanceAdmin() === false) {
            return redirect()->route('dashboard');
        }
        $this->settings = instanceSettings();
        $this->syncData();
        $this->team = auth()->user()->currentTeam();
        $this->testEmailAddress = auth()->user()->email;
    }

    public function syncData(bool $toModel = false)
    {
        if ($toModel) {
            $this->validate();
            $this->settings->smtp_enabled = $this->smtpEnabled;
            $this->settings->smtp_host = $this->smtpHost;
            $this->settings->smtp_port = $this->smtpPort;
            $this->settings->smtp_encryption = $this->smtpEncryption;
            $this->settings->smtp_username = $this->smtpUsername;
            $this->settings->smtp_password = $this->smtpPassword;
            $this->settings->smtp_timeout = $this->smtpTimeout;
            $this->settings->smtp_from_address = $this->smtpFromAddress;
            $this->settings->smtp_from_name = $this->smtpFromName;

            $this->settings->resend_enabled = $this->resendEnabled;
            $this->settings->resend_api_key = $this->resendApiKey;
            $this->settings->save();
        } else {
            $this->smtpEnabled = $this->settings->smtp_enabled;
            $this->smtpHost = $this->settings->smtp_host;
            $this->smtpPort = $this->settings->smtp_port;
            $this->smtpEncryption = $this->settings->smtp_encryption;
            $this->smtpUsername = $this->settings->smtp_username;
            $this->smtpPassword = $this->settings->smtp_password;
            $this->smtpTimeout = $this->settings->smtp_timeout;
            $this->smtpFromAddress = $this->settings->smtp_from_address;
            $this->smtpFromName = $this->settings->smtp_from_name;

            $this->resendEnabled = $this->settings->resend_enabled;
            $this->resendApiKey = $this->settings->resend_api_key;
        }
    }

    public function submit()
    {
        try {
            $this->resetErrorBag();
            $this->syncData(true);
            $this->dispatch('success', __('settings.email_page.transactional_updated'));
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function instantSave(string $type)
    {
        try {
            $currentSmtpEnabled = $this->settings->smtp_enabled;
            $currentResendEnabled = $this->settings->resend_enabled;
            $this->resetErrorBag();

            if ($type === 'SMTP') {
                $this->submitSmtp();
                $this->resendEnabled = $this->settings->resend_enabled = false;
            } elseif ($type === 'Resend') {
                $this->submitResend();
                $this->smtpEnabled = $this->settings->smtp_enabled = false;
            }
            $this->settings->save();

        } catch (\Throwable $e) {
            if ($type === 'SMTP') {
                $this->smtpEnabled = $currentSmtpEnabled;
            } elseif ($type === 'Resend') {
                $this->resendEnabled = $currentResendEnabled;
            }

            return handleError($e, $this);
        }
    }

    public function submitSmtp()
    {
        try {
            $this->validate([
                'smtpEnabled' => 'boolean',
                'smtpFromAddress' => 'required|email',
                'smtpFromName' => 'required|string',
                'smtpHost' => 'required|string',
                'smtpPort' => 'required|numeric',
                'smtpEncryption' => 'required|string|in:starttls,tls,none',
                'smtpUsername' => 'nullable|string',
                'smtpPassword' => 'nullable|string',
                'smtpTimeout' => 'nullable|numeric',
            ], [
                'smtpFromAddress.required' => __('settings.email_page.validation.from_address_required'),
                'smtpFromAddress.email' => __('settings.email_page.validation.from_address_email'),
                'smtpFromName.required' => __('settings.email_page.validation.from_name_required'),
                'smtpHost.required' => __('settings.email_page.validation.smtp_host_required'),
                'smtpPort.required' => __('settings.email_page.validation.smtp_port_required'),
                'smtpPort.numeric' => __('settings.email_page.validation.smtp_port_numeric'),
                'smtpEncryption.required' => __('settings.email_page.validation.smtp_encryption_required'),
            ]);

            $this->settings->smtp_enabled = $this->smtpEnabled;
            $this->settings->smtp_host = $this->smtpHost;
            $this->settings->smtp_port = $this->smtpPort;
            $this->settings->smtp_encryption = $this->smtpEncryption;
            $this->settings->smtp_username = $this->smtpUsername;
            $this->settings->smtp_password = $this->smtpPassword;
            $this->settings->smtp_timeout = $this->smtpTimeout;
            $this->settings->smtp_from_address = $this->smtpFromAddress;
            $this->settings->smtp_from_name = $this->smtpFromName;

            $this->settings->save();

            $this->dispatch('success', __('settings.email_page.smtp_updated'));
        } catch (\Throwable $e) {
            $this->smtpEnabled = false;

            return handleError($e, $this);
        }
    }

    public function submitResend()
    {
        try {
            $this->validate([
                'resendEnabled' => 'boolean',
                'resendApiKey' => 'required|string',
                'smtpFromAddress' => 'required|email',
                'smtpFromName' => 'required|string',
            ], [
                'resendApiKey.required' => __('settings.email_page.validation.resend_api_key_required'),
                'smtpFromAddress.required' => __('settings.email_page.validation.from_address_required'),
                'smtpFromAddress.email' => __('settings.email_page.validation.from_address_email'),
                'smtpFromName.required' => __('settings.email_page.validation.from_name_required'),
            ]);

            $this->settings->resend_enabled = $this->resendEnabled;
            $this->settings->resend_api_key = $this->resendApiKey;
            $this->settings->smtp_from_address = $this->smtpFromAddress;
            $this->settings->smtp_from_name = $this->smtpFromName;

            $this->settings->save();

            $this->dispatch('success', __('settings.email_page.resend_updated'));
        } catch (\Throwable $e) {
            $this->resendEnabled = false;

            return handleError($e, $this);
        }
    }

    public function sendTestEmail()
    {
        try {
            $this->validate([
                'testEmailAddress' => 'required|email',
            ], [
                'testEmailAddress.required' => __('settings.email_page.validation.test_email_required'),
                'testEmailAddress.email' => __('settings.email_page.validation.test_email_email'),
            ]);

            $executed = RateLimiter::attempt(
                'test-email:'.$this->team->id,
                $perMinute = 0,
                function () {
                    $this->team?->notifyNow(new Test($this->testEmailAddress));
                    $this->dispatch('success', __('settings.email_page.test_email_sent'));
                },
                $decaySeconds = 10,
            );

            if (! $executed) {
                throw new \Exception(__('settings.email_page.rate_limited'));
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }
}
