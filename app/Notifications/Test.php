<?php

namespace App\Notifications;

use App\Notifications\Channels\DiscordChannel;
use App\Notifications\Channels\EmailChannel;
use App\Notifications\Channels\PushoverChannel;
use App\Notifications\Channels\SlackChannel;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Channels\WebhookChannel;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\Middleware\RateLimited;

class Test extends CustomEmailNotification
{
    public $tries = 5;

    public bool $isTestNotification = true;

    public function __construct(public ?string $emails = null, public ?string $channel = null, public ?bool $ping = false)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        if ($this->channel) {
            $channels = match ($this->channel) {
                'email' => [EmailChannel::class],
                'discord' => [DiscordChannel::class],
                'telegram' => [TelegramChannel::class],
                'slack' => [SlackChannel::class],
                'pushover' => [PushoverChannel::class],
                'webhook' => [WebhookChannel::class],
                default => [],
            };
        } else {
            $channels = $notifiable->getEnabledChannels('test');
        }

        return $channels;
    }

    public function middleware(object $notifiable, string $channel)
    {
        return match ($channel) {
            EmailChannel::class => [new RateLimited('email')],
            default => [],
        };
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.test.subject'));
            $mail->view('emails.test');

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.test.discord_title'),
            description: $this->trans('notifications.test.discord_description'),
            color: DiscordMessage::successColor(),
            isCritical: $this->ping,
        );

        $message->addField(
            name: $this->trans('notifications.common.dashboard'),
            value: '[Link]('.base_url().')',
            inline: true
        );

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.test.telegram_message'),
            'buttons' => [
                [
                    'text' => $this->trans('notifications.test.dashboard_button'),
                    'url' => isDev() ? 'https://staging-but-dev.coolify.io' : base_url(),
                ],
            ],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.test.pushover_title'),
            message: $this->trans('notifications.test.pushover_message'),
            buttons: [
                [
                    'text' => $this->trans('notifications.test.dashboard_button'),
                    'url' => base_url(),
                ],
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.test.slack_title'),
            description: $this->trans('notifications.test.slack_description')
        );
    }

    public function toWebhook(): array
    {
        return [
            'success' => true,
            'message' => $this->trans('notifications.test.webhook_message'),
            'event' => 'test',
            'url' => base_url(),
        ];
    }
}
