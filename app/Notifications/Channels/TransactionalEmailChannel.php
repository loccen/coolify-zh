<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Support\UserVisibleLocale;
use Exception;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class TransactionalEmailChannel
{
    public function send(User $notifiable, Notification $notification): void
    {
        $settings = instanceSettings();
        if (! data_get($settings, 'smtp_enabled') && ! data_get($settings, 'resend_enabled')) {
            return;
        }

        // Check if notification has a custom recipient (for email changes)
        $email = property_exists($notification, 'newEmail') && $notification->newEmail
            ? $notification->newEmail
            : $notifiable->email;

        if (! $email) {
            return;
        }
        $this->bootConfigs();
        $locale = UserVisibleLocale::resolve(data_get($notification, 'locale'));
        $mailMessage = UserVisibleLocale::withLocale($locale, fn () => $notification->toMail($notifiable));
        $rendered = UserVisibleLocale::withLocale($locale, fn () => (string) $mailMessage->render());
        Mail::send(
            [],
            [],
            fn (Message $message) => $message
                ->to($email)
                ->subject($mailMessage->subject)
                ->html($rendered)
        );
    }

    private function bootConfigs(): void
    {
        $type = set_transanctional_email_settings();
        if (blank($type)) {
            throw new Exception(trans('mail.channels.transactional_email.no_email_settings_found'));
        }
    }
}
