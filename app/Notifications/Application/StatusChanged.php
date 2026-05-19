<?php

namespace App\Notifications\Application;

use App\Models\Application;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class StatusChanged extends CustomEmailNotification
{
    public string $resource_name;

    public string $project_uuid;

    public string $environment_uuid;

    public string $environment_name;

    public ?string $resource_url = null;

    public ?string $fqdn;

    public function __construct(public Application $resource)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->resource_name = data_get($resource, 'name');
        $this->project_uuid = data_get($resource, 'environment.project.uuid');
        $this->environment_uuid = data_get($resource, 'environment.uuid');
        $this->environment_name = data_get($resource, 'environment.name');
        $this->fqdn = data_get($resource, 'fqdn', null);
        if (str($this->fqdn)->explode(',')->count() > 1) {
            $this->fqdn = str($this->fqdn)->explode(',')->first();
        }
        $this->resource_url = base_url()."/project/{$this->project_uuid}/environment/{$this->environment_uuid}/application/{$this->resource->uuid}";
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('status_change');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.application_status_changed.subject', ['name' => $this->resource_name]));
            $mail->view('emails.application-status-changes', [
                'name' => $this->resource_name,
                'fqdn' => $this->fqdn,
                'resource_url' => $this->resource_url,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            title: $this->trans('notifications.status_changed.discord_title'),
            description: "[{$this->trans('notifications.common.open_application_in_coolify')}]({$this->resource_url})",
            color: DiscordMessage::errorColor(),
            isCritical: true,
        );
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.status_changed.telegram_message', ['name' => $this->resource_name]);

        return [
            'message' => $message,
            'buttons' => [
                [
                    'text' => $this->trans('notifications.common.open_application_in_coolify'),
                    'url' => $this->resource_url,
                ],
            ],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        $message = $this->resource_name.' has been stopped.';

        return new PushoverMessage(
            title: $this->trans('notifications.status_changed.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.status_changed.pushover_message', ['name' => $this->resource_name]),
            buttons: [
                [
                    'text' => $this->trans('notifications.common.open_application_in_coolify'),
                    'url' => $this->resource_url,
                ],
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.status_changed.slack_title');
        $description = $this->trans('notifications.status_changed.slack_description', ['name' => $this->resource_name]);

        $description .= "\n\n*".$this->trans('notifications.common.project').':* '.data_get($this->resource, 'environment.project.name');
        $description .= "\n*".$this->trans('notifications.common.environment').":* {$this->environment_name}";
        $description .= "\n*".$this->trans('notifications.common.application_url').":* {$this->resource_url}";

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        return [
            'success' => false,
            'message' => $this->trans('notifications.status_changed.webhook_message'),
            'event' => 'status_changed',
            'application_name' => $this->resource_name,
            'application_uuid' => $this->resource->uuid,
            'url' => $this->resource_url,
            'project' => data_get($this->resource, 'environment.project.name'),
            'environment' => $this->environment_name,
            'fqdn' => $this->fqdn,
        ];
    }
}
