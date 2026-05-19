<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class Unreachable extends CustomEmailNotification
{
    protected bool $isRateLimited = false;

    public function __construct(public Server $server)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->isRateLimited = isEmailRateLimited(
            limiterKey: 'server-unreachable:'.$this->server->id,
        );
    }

    public function via(object $notifiable): array
    {
        if ($this->isRateLimited) {
            return [];
        }

        return $notifiable->getEnabledChannels('server_unreachable');
    }

    public function toMail(): ?MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.server_lost_connection.subject', ['name' => $this->server->name]));
            $mail->view('emails.server-lost-connection', [
                'name' => $this->server->name,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): ?DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.server_unreachable.discord_title'),
            description: $this->trans('notifications.server_unreachable.discord_description', ['name' => $this->server->name]),
            color: DiscordMessage::errorColor(),
        );

        $message->addField($this->trans('notifications.common.important'), $this->trans('notifications.server_unreachable.discord_important_message'));

        return $message;
    }

    public function toTelegram(): ?array
    {
        return [
            'message' => $this->trans('notifications.server_unreachable.telegram_message', ['name' => $this->server->name]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.server_unreachable.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.server_unreachable.pushover_message', ['name' => $this->server->name]),
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.server_unreachable.slack_title'),
            description: $this->trans('notifications.server_unreachable.slack_description', ['name' => $this->server->name]),
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/server/'.$this->server->uuid;

        return [
            'success' => false,
            'message' => $this->trans('notifications.server_unreachable.webhook_message'),
            'event' => 'server_unreachable',
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
            'url' => $url,
        ];
    }
}
