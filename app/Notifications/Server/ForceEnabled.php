<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ForceEnabled extends CustomEmailNotification
{
    public function __construct(public Server $server)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('server_force_enabled');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.server_force_enabled.subject', ['name' => $this->server->name]));
            $mail->view('emails.server-force-enabled', [
                'name' => $this->server->name,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.server_force_enabled.discord_title'),
            description: $this->trans('notifications.server_force_enabled.discord_description', ['name' => $this->server->name]),
            color: DiscordMessage::successColor(),
        );
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.server_force_enabled.telegram_message', ['name' => $this->server->name]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.server_force_enabled.pushover_title'),
            level: 'success',
            message: $this->trans('notifications.server_force_enabled.pushover_message', ['name' => $this->server->name]),
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.server_force_enabled.slack_title'),
            description: $this->trans('notifications.server_force_enabled.slack_description', ['name' => $this->server->name]),
            color: SlackMessage::successColor()
        );
    }
}
