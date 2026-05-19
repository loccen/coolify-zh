<?php

namespace App\Notifications\Server;

use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class HetznerDeletionFailed extends CustomEmailNotification
{
    public function __construct(public int $hetznerServerId, public int $teamId, public string $errorMessage)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        ray('hello');
        ray($notifiable);

        return $notifiable->getEnabledChannels('hetzner_deletion_failed');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.hetzner_deletion_failed.subject', ['id' => $this->hetznerServerId]));
            $mail->view('emails.hetzner-deletion-failed', [
                'hetznerServerId' => $this->hetznerServerId,
                'errorMessage' => $this->errorMessage,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.hetzner_deletion_failed.discord_title'),
            description: $this->trans('notifications.hetzner_deletion_failed.discord_description', [
                'id' => $this->hetznerServerId,
                'error' => $this->errorMessage,
            ]),
            color: DiscordMessage::errorColor(),
        );
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.hetzner_deletion_failed.telegram_message', [
                'id' => $this->hetznerServerId,
                'error' => $this->errorMessage,
            ]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.hetzner_deletion_failed.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.hetzner_deletion_failed.pushover_message', [
                'id' => $this->hetznerServerId,
                'error' => $this->errorMessage,
            ]),
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.hetzner_deletion_failed.slack_title'),
            description: $this->trans('notifications.hetzner_deletion_failed.slack_description', [
                'id' => $this->hetznerServerId,
                'error' => $this->errorMessage,
            ]),
            color: SlackMessage::errorColor()
        );
    }
}
