<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class Reachable extends CustomEmailNotification
{
    protected bool $isRateLimited = false;

    public function __construct(public Server $server)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->isRateLimited = isEmailRateLimited(
            limiterKey: 'server-reachable:'.$this->server->id,
        );
    }

    public function via(object $notifiable): array
    {
        if ($this->isRateLimited) {
            return [];
        }

        return $notifiable->getEnabledChannels('server_reachable');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.server_revived.subject', ['name' => $this->server->name]));
            $mail->view('emails.server-revived', [
                'name' => $this->server->name,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.server_reachable.discord_title', ['name' => $this->server->name]),
            description: $this->trans('notifications.server_reachable.discord_description'),
            color: DiscordMessage::successColor(),
        );
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.server_reachable.pushover_title'),
            message: $this->trans('notifications.server_reachable.pushover_message', ['name' => $this->server->name]),
            level: 'success',
        );
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.server_reachable.telegram_message', ['name' => $this->server->name]),
        ];
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.server_reachable.slack_title'),
            description: $this->trans('notifications.server_reachable.slack_description', ['name' => $this->server->name]),
            color: SlackMessage::successColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/server/'.$this->server->uuid;

        return [
            'success' => true,
            'message' => $this->trans('notifications.server_reachable.webhook_message'),
            'event' => 'server_reachable',
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
            'url' => $url,
        ];
    }
}
