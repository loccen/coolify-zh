<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public int $userId;

    public string $email;

    public string $current_password;

    public string $new_password;

    public string $new_password_confirmation;

    #[Validate('required')]
    public string $name;

    public string $new_email = '';

    public string $email_verification_code = '';

    public bool $show_email_change = false;

    public bool $show_verification = false;

    public function mount()
    {
        $this->userId = Auth::id();
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;

        // Check if there's a pending email change
        if (Auth::user()->hasEmailChangeRequest()) {
            $this->new_email = Auth::user()->pending_email;
            $this->show_verification = true;
        }
    }

    public function submit()
    {
        try {
            $this->validate([
                'name' => 'required',
            ]);
            Auth::user()->update([
                'name' => $this->name,
            ]);

            $this->dispatch('success', __('profile.messages.updated'));
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function requestEmailChange()
    {
        try {
            // For self-hosted, check if email is enabled
            if (! isCloud()) {
                $settings = instanceSettings();
                if (! $settings->smtp_enabled && ! $settings->resend_enabled) {
                    $this->dispatch('error', __('profile.messages.email_not_configured'));

                    return;
                }
            }

            $this->validate([
                'new_email' => ['required', 'email', 'unique:users,email'],
            ]);

            $this->new_email = strtolower($this->new_email);

            // Skip rate limiting in development mode
            if (! isDev()) {
                // Rate limit by current user's email (1 request per 2 minutes)
                $userEmailKey = 'email-change:user:'.Auth::id();
                if (! RateLimiter::attempt($userEmailKey, 1, function () {}, 120)) {
                    $seconds = RateLimiter::availableIn($userEmailKey);
                    $this->dispatch('error', __('profile.messages.email_rate_limited_seconds', ['seconds' => $seconds]));

                    return;
                }

                // Rate limit by new email address (3 requests per hour per email)
                $newEmailKey = 'email-change:email:'.md5($this->new_email);
                if (! RateLimiter::attempt($newEmailKey, 3, function () {}, 3600)) {
                    $this->dispatch('error', __('profile.messages.email_rate_limited_address'));

                    return;
                }

                // Additional rate limit by IP address (5 requests per hour)
                $ipKey = 'email-change:ip:'.request()->ip();
                if (! RateLimiter::attempt($ipKey, 5, function () {}, 3600)) {
                    $this->dispatch('error', __('profile.messages.email_rate_limited_ip'));

                    return;
                }
            }

            Auth::user()->requestEmailChange($this->new_email);

            $this->show_email_change = false;
            $this->show_verification = true;

            $this->dispatch('success', __('profile.messages.verification_sent', ['email' => $this->new_email]));
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function verifyEmailChange()
    {
        try {
            $this->validate([
                'email_verification_code' => ['required', 'string', 'size:6'],
            ]);

            // Skip rate limiting in development mode
            if (! isDev()) {
                // Rate limit verification attempts (5 attempts per 10 minutes)
                $verifyKey = 'email-verify:user:'.Auth::id();
                if (! RateLimiter::attempt($verifyKey, 5, function () {}, 600)) {
                    $seconds = RateLimiter::availableIn($verifyKey);
                    $minutes = ceil($seconds / 60);
                    $this->dispatch('error', __('profile.messages.verification_attempts_limited', ['minutes' => $minutes]));

                    // If too many failed attempts, clear the email change request for security
                    if (RateLimiter::attempts($verifyKey) >= 10) {
                        Auth::user()->clearEmailChangeRequest();
                        $this->new_email = '';
                        $this->email_verification_code = '';
                        $this->show_verification = false;
                        $this->dispatch('error', __('profile.messages.verification_cancelled'));
                    }

                    return;
                }
            }

            if (! Auth::user()->isEmailChangeCodeValid($this->email_verification_code)) {
                $this->dispatch('error', __('profile.messages.verification_invalid'));

                return;
            }

            if (Auth::user()->confirmEmailChange($this->email_verification_code)) {
                // Clear rate limiters on successful verification (only in production)
                if (! isDev()) {
                    $verifyKey = 'email-verify:user:'.Auth::id();
                    RateLimiter::clear($verifyKey);
                }

                $this->email = Auth::user()->email;
                $this->new_email = '';
                $this->email_verification_code = '';
                $this->show_verification = false;

                $this->dispatch('success', __('profile.messages.email_updated'));
            } else {
                $this->dispatch('error', __('profile.messages.email_update_failed'));
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function resendVerificationCode()
    {
        try {
            // Check if there's a pending request
            if (! Auth::user()->hasEmailChangeRequest()) {
                $this->dispatch('error', __('profile.messages.no_pending_email_change'));

                return;
            }

            // Check if enough time has passed (at least half of the expiry time)
            $expiryMinutes = config('constants.email_change.verification_code_expiry_minutes', 10);
            $halfExpiryMinutes = $expiryMinutes / 2;
            $codeExpiry = Auth::user()->email_change_code_expires_at;
            $timeSinceCreated = $codeExpiry->subMinutes($expiryMinutes)->diffInMinutes(now());

            if ($timeSinceCreated < $halfExpiryMinutes) {
                $minutesToWait = ceil($halfExpiryMinutes - $timeSinceCreated);
                $this->dispatch('error', __('profile.messages.resend_wait', ['minutes' => $minutesToWait]));

                return;
            }

            $pendingEmail = Auth::user()->pending_email;

            // Skip rate limiting in development mode
            if (! isDev()) {
                // Rate limit by email address
                $newEmailKey = 'email-change:email:'.md5(strtolower($pendingEmail));
                if (! RateLimiter::attempt($newEmailKey, 3, function () {}, 3600)) {
                    $this->dispatch('error', __('profile.messages.email_rate_limited_address'));

                    return;
                }
            }

            // Generate and send new code
            Auth::user()->requestEmailChange($pendingEmail);

            $this->dispatch('success', __('profile.messages.verification_resent', ['email' => $pendingEmail]));
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function cancelEmailChange()
    {
        Auth::user()->clearEmailChangeRequest();
        $this->new_email = '';
        $this->email_verification_code = '';
        $this->show_email_change = false;
        $this->show_verification = false;

        $this->dispatch('success', __('profile.messages.email_change_cancelled'));
    }

    public function showEmailChangeForm()
    {
        $this->show_email_change = true;
        $this->new_email = '';
    }

    public function resetPassword()
    {
        try {
            $this->validate([
                'current_password' => ['required'],
                'new_password' => ['required', Password::defaults(), 'confirmed'],
            ]);
            if (! Hash::check($this->current_password, auth()->user()->password)) {
                $this->dispatch('error', __('profile.messages.current_password_incorrect'));

                return;
            }
            if ($this->new_password !== $this->new_password_confirmation) {
                $this->dispatch('error', __('profile.messages.new_password_mismatch'));

                return;
            }
            auth()->user()->update([
                'password' => Hash::make($this->new_password),
            ]);
            $this->dispatch('success', __('profile.messages.password_updated'));
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';
            $this->dispatch('reloadWindow');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.profile.index');
    }
}
