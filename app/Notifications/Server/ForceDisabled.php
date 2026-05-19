<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ForceDisabled extends CustomEmailNotification
{
    public function __construct(public Server $server)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('server_force_disabled');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.server_force_disabled.subject', ['name' => $this->server->name]));
            $mail->view('emails.server-force-disabled', [
                'name' => $this->server->name,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.server_force_disabled.discord_title'),
            description: $this->trans('notifications.server_force_disabled.discord_description', ['name' => $this->server->name]),
            color: DiscordMessage::errorColor(),
        );

        $message->addField($this->trans('notifications.server_force_disabled.discord_update_subscription'), "[{$this->trans('notifications.common.link')}](https://app.coolify.io/subscription)");

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.server_force_disabled.telegram_message', ['name' => $this->server->name]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.server_force_disabled.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.server_force_disabled.pushover_message', ['name' => $this->server->name]),
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.server_force_disabled.slack_title'),
            description: $this->trans('notifications.server_force_disabled.slack_description', ['name' => $this->server->name]),
            color: SlackMessage::errorColor()
        );
    }
}
