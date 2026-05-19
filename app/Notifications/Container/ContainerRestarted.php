<?php

namespace App\Notifications\Container;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ContainerRestarted extends CustomEmailNotification
{
    public function __construct(public string $name, public Server $server, public ?string $url = null)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('status_change');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.container_restarted.subject', [
                'name' => $this->name,
                'server' => $this->server->name,
            ]));
            $mail->view('emails.container-restarted', [
                'containerName' => $this->name,
                'serverName' => $this->server->name,
                'url' => $this->url,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.container_restarted.discord_title'),
            description: $this->trans('notifications.container_restarted.discord_description', [
                'name' => $this->name,
                'server' => $this->server->name,
            ]),
            color: DiscordMessage::infoColor(),
        );

        if ($this->url) {
            $message->addField($this->trans('notifications.common.resource'), "[{$this->trans('notifications.common.link')}]({$this->url})");
        }

        return $message;
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.container_restarted.telegram_message', [
            'name' => $this->name,
            'server' => $this->server->name,
        ]);
        $payload = [
            'message' => $message,
        ];
        if ($this->url) {
            $payload['buttons'] = [
                [
                    [
                        'text' => $this->trans('notifications.common.check_proxy_in_coolify'),
                        'url' => $this->url,
                    ],
                ],
            ];
        }

        return $payload;
    }

    public function toPushover(): PushoverMessage
    {
        $buttons = [];
        if ($this->url) {
            $buttons[] = [
                'text' => $this->trans('notifications.common.check_proxy_in_coolify'),
                'url' => $this->url,
            ];
        }

        return new PushoverMessage(
            title: $this->trans('notifications.container_restarted.pushover_title'),
            level: 'warning',
            message: $this->trans('notifications.container_restarted.pushover_message', [
                'name' => $this->name,
                'server' => $this->server->name,
            ]),
            buttons: $buttons,
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.container_restarted.slack_title');
        $description = $this->trans('notifications.container_restarted.slack_description', [
            'name' => $this->name,
            'server' => $this->server->name,
        ]);

        if ($this->url) {
            $description .= "\n*".$this->trans('notifications.common.resource_url').":* {$this->url}";
        }

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::warningColor()
        );
    }

    public function toWebhook(): array
    {
        $data = [
            'success' => true,
            'message' => $this->trans('notifications.container_restarted.webhook_message'),
            'event' => 'container_restarted',
            'container_name' => $this->name,
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
        ];

        if ($this->url) {
            $data['url'] = $this->url;
        }

        return $data;
    }
}
