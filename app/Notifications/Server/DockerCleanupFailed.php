<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class DockerCleanupFailed extends CustomEmailNotification
{
    public function __construct(public Server $server, public string $message)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('docker_cleanup_failure');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.docker_cleanup_failed.subject', ['server' => $this->server->name]));
            $mail->view('emails.docker-cleanup-failed', [
                'name' => $this->server->name,
                'text' => $this->message,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.docker_cleanup_failed.discord_title', ['server' => $this->server->name]),
            description: $this->message,
            color: DiscordMessage::errorColor(),
        );
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.docker_cleanup_failed.telegram_message', [
                'server' => $this->server->name,
                'message' => $this->message,
            ]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.docker_cleanup_failed.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.docker_cleanup_failed.pushover_message', [
                'server' => $this->server->name,
                'message' => $this->message,
            ]),
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.docker_cleanup_failed.slack_title'),
            description: $this->trans('notifications.docker_cleanup_failed.slack_description', [
                'server' => $this->server->name,
                'message' => $this->message,
            ]),
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/server/'.$this->server->uuid;

        return [
            'success' => false,
            'message' => $this->trans('notifications.docker_cleanup_failed.webhook_message'),
            'event' => 'docker_cleanup_failed',
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
            'error_message' => $this->message,
            'url' => $url,
        ];
    }
}
