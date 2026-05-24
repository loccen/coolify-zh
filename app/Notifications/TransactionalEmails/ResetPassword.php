<?php

namespace App\Notifications\TransactionalEmails;

use App\Models\InstanceSettings;
use App\Support\UserVisibleLocale;
use Exception;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public static $createUrlCallback;

    public static $toMailCallback;

    public $token;

    public InstanceSettings $settings;

    public function __construct($token, public bool $isTransactionalEmail = true)
    {
        $this->locale = UserVisibleLocale::resolve();
        $this->settings = instanceSettings();
        $this->token = $token;
    }

    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    public function via($notifiable)
    {
        $type = set_transanctional_email_settings();
        if (blank($type)) {
            throw new Exception(trans('mail.channels.transactional_email.no_email_settings_found'));
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    protected function buildMailMessage($url)
    {
        return UserVisibleLocale::withLocale($this->locale ?? null, function () use ($url) {
            $mail = new MailMessage;
            $mail->subject(trans('mail.reset_password.subject', locale: UserVisibleLocale::resolve($this->locale ?? null)));
            $mail->view('emails.reset-password', ['url' => $url, 'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]);

            return $mail;
        });
    }

    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        $path = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false);

        // Use server-side config (FQDN / public IP) instead of request host
        return rtrim(base_url(), '/').$path;
    }
}
