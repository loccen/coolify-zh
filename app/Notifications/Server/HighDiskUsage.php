<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class HighDiskUsage extends CustomEmailNotification
{
    public function __construct(public Server $server, public int $disk_usage, public int $server_disk_usage_notification_threshold)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('server_disk_usage');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.high_disk_usage.subject', ['name' => $this->server->name]));
            $mail->view('emails.high-disk-usage', [
                'name' => $this->server->name,
                'disk_usage' => $this->disk_usage,
                'threshold' => $this->server_disk_usage_notification_threshold,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.server_high_disk_usage.discord_title'),
            description: $this->trans('notifications.server_high_disk_usage.discord_description', ['name' => $this->server->name]),
            color: DiscordMessage::errorColor(),
            isCritical: true,
        );

        $message->addField($this->trans('notifications.common.disk_usage'), "{$this->disk_usage}%", true);
        $message->addField($this->trans('notifications.common.threshold'), "{$this->server_disk_usage_notification_threshold}%", true);
        $message->addField($this->trans('notifications.common.what_to_do'), "[{$this->trans('notifications.common.link')}](https://coolify.io/docs/knowledge-base/server/automated-cleanup)", true);
        $message->addField($this->trans('notifications.common.change_settings'), "[{$this->trans('notifications.common.threshold')}](".base_url().'/server/'.$this->server->uuid."#advanced) | [{$this->trans('notifications.common.notifications')}](".base_url().'/notifications/discord)');

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.server_high_disk_usage.telegram_message', [
                'name' => $this->server->name,
                'diskUsage' => $this->disk_usage,
                'threshold' => $this->server_disk_usage_notification_threshold,
            ]),
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.server_high_disk_usage.pushover_title'),
            level: 'warning',
            message: $this->trans('notifications.server_high_disk_usage.pushover_message', [
                'name' => $this->server->name,
                'diskUsage' => $this->disk_usage,
                'threshold' => $this->server_disk_usage_notification_threshold,
            ]),
            buttons: [
                $this->trans('notifications.common.change_settings') => base_url().'/server/'.$this->server->uuid.'#advanced',
                $this->trans('notifications.common.tips_for_cleanup') => 'https://coolify.io/docs/knowledge-base/server/automated-cleanup',
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        $description = $this->trans('notifications.server_high_disk_usage.slack_description', ['name' => $this->server->name])."\n";
        $description .= $this->trans('notifications.common.disk_usage').": {$this->disk_usage}%\n";
        $description .= $this->trans('notifications.common.threshold').": {$this->server_disk_usage_notification_threshold}%\n\n";
        $description .= $this->trans('notifications.common.tips_for_cleanup').": https://coolify.io/docs/knowledge-base/server/automated-cleanup\n";
        $description .= $this->trans('notifications.common.change_settings').":\n";
        $description .= '- '.$this->trans('notifications.common.threshold').': '.base_url().'/server/'.$this->server->uuid."#advanced\n";
        $description .= '- '.$this->trans('notifications.common.notifications').': '.base_url().'/notifications/slack';

        return new SlackMessage(
            title: $this->trans('notifications.server_high_disk_usage.slack_title'),
            description: $description,
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        return [
            'success' => false,
            'message' => $this->trans('notifications.server_high_disk_usage.webhook_message'),
            'event' => 'high_disk_usage',
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
            'disk_usage' => $this->disk_usage,
            'threshold' => $this->server_disk_usage_notification_threshold,
            'url' => base_url().'/server/'.$this->server->uuid,
        ];
    }
}
