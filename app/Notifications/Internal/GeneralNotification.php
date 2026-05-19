<?php

namespace App\Notifications\Internal;

use App\Support\UserVisibleLocale;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public function __construct(public string $message)
    {
        $this->onQueue('high');
        $this->locale = UserVisibleLocale::resolve();
    }

    private function trans(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace, UserVisibleLocale::resolve($this->locale ?? null));
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('general');
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.general_notification.discord_title'),
            description: $this->message,
            color: DiscordMessage::infoColor(),
        );
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->message,
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.general_notification.pushover_title'),
            level: 'info',
            message: $this->message,
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.general_notification.slack_title'),
            description: $this->message,
            color: SlackMessage::infoColor(),
        );
    }
}
