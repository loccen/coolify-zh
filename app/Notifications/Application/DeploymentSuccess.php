<?php

namespace App\Notifications\Application;

use App\Models\Application;
use App\Models\ApplicationPreview;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class DeploymentSuccess extends CustomEmailNotification
{
    public Application $application;

    public ?ApplicationPreview $preview = null;

    public string $deployment_uuid;

    public string $application_name;

    public string $project_uuid;

    public string $environment_uuid;

    public string $environment_name;

    public ?string $deployment_url = null;

    public ?string $fqdn;

    public function __construct(Application $application, string $deployment_uuid, ?ApplicationPreview $preview = null)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->application = $application;
        $this->deployment_uuid = $deployment_uuid;
        $this->preview = $preview;
        $this->application_name = data_get($application, 'name');
        $this->project_uuid = data_get($application, 'environment.project.uuid');
        $this->environment_uuid = data_get($application, 'environment.uuid');
        $this->environment_name = data_get($application, 'environment.name');
        $this->fqdn = data_get($application, 'fqdn');
        if (str($this->fqdn)->explode(',')->count() > 1) {
            $this->fqdn = str($this->fqdn)->explode(',')->first();
        }
        $this->deployment_url = base_url()."/project/{$this->project_uuid}/environment/{$this->environment_uuid}/application/{$this->application->uuid}/deployment/{$this->deployment_uuid}";
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('deployment_success');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $pullRequestId = data_get($this->preview, 'pull_request_id', 0);
            $fqdn = $this->fqdn;
            if ($pullRequestId === 0) {
                $mail->subject($this->trans('mail.application_deployment_success.subject', ['name' => $this->application_name]));
            } else {
                $fqdn = $this->preview->fqdn;
                $mail->subject($this->trans('mail.application_deployment_success.subject_preview', [
                    'pullRequestId' => $pullRequestId,
                    'name' => $this->application_name,
                ]));
            }
            $mail->view('emails.application-deployment-success', [
                'name' => $this->application_name,
                'fqdn' => $fqdn,
                'deployment_url' => $this->deployment_url,
                'pull_request_id' => $pullRequestId,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        if ($this->preview) {
            $message = new DiscordMessage(
                title: $this->trans('notifications.deployment_success.discord_preview_title'),
                description: $this->trans('notifications.deployment_success.discord_preview_description', [
                    'pullRequestId' => $this->preview->pull_request_id,
                ]),
                color: DiscordMessage::successColor(),
            );

            if ($this->preview->fqdn) {
                $message->addField($this->trans('notifications.common.application'), "[{$this->trans('notifications.common.open_application')}]({$this->preview->fqdn})");
            }

            $message->addField($this->trans('notifications.common.project'), data_get($this->application, 'environment.project.name'), true);
            $message->addField($this->trans('notifications.common.environment'), $this->environment_name, true);
            $message->addField($this->trans('notifications.common.name'), $this->application_name, true);
            $message->addField($this->trans('notifications.common.deployment_logs'), "[{$this->trans('notifications.common.link')}]({$this->deployment_url})");
        } else {
            if ($this->fqdn) {
                $description = "[{$this->trans('notifications.common.open_application')}]({$this->fqdn})";
            } else {
                $description = '';
            }
            $message = new DiscordMessage(
                title: $this->trans('notifications.deployment_success.discord_title'),
                description: $description,
                color: DiscordMessage::successColor(),
            );
            $message->addField($this->trans('notifications.common.project'), data_get($this->application, 'environment.project.name'), true);
            $message->addField($this->trans('notifications.common.environment'), $this->environment_name, true);
            $message->addField($this->trans('notifications.common.name'), $this->application_name, true);

            $message->addField($this->trans('notifications.common.deployment_logs'), "[{$this->trans('notifications.common.link')}]({$this->deployment_url})");
        }

        return $message;
    }

    public function toTelegram(): array
    {
        if ($this->preview) {
            $message = $this->trans('notifications.deployment_success.telegram_preview_message', [
                'pullRequestId' => $this->preview->pull_request_id,
                'name' => $this->application_name,
            ]);
            if ($this->preview->fqdn) {
                $buttons[] = [
                    'text' => $this->trans('notifications.common.open_application'),
                    'url' => $this->preview->fqdn,
                ];
            }
        } else {
            $message = $this->trans('notifications.deployment_success.telegram_message', ['name' => $this->application_name]);
            if ($this->fqdn) {
                $buttons[] = [
                    'text' => $this->trans('notifications.common.open_application'),
                    'url' => $this->fqdn,
                ];
            }
        }
        $buttons[] = [
            'text' => $this->trans('notifications.common.deployment_logs_button'),
            'url' => $this->deployment_url,
        ];

        return [
            'message' => $message,
            'buttons' => [
                ...$buttons,
            ],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        if ($this->preview) {
            $title = $this->trans('notifications.deployment_success.pushover_preview_title', ['pullRequestId' => $this->preview->pull_request_id]);
            $message = $this->trans('notifications.deployment_success.pushover_preview_message', [
                'pullRequestId' => $this->preview->pull_request_id,
                'name' => $this->application_name,
            ]);
            if ($this->preview->fqdn) {
                $buttons[] = [
                    'text' => $this->trans('notifications.common.open_application'),
                    'url' => $this->preview->fqdn,
                ];
            }
        } else {
            $title = $this->trans('notifications.deployment_success.pushover_title');
            $message = $this->trans('notifications.deployment_success.pushover_message', ['name' => $this->application_name]);
            if ($this->fqdn) {
                $buttons[] = [
                    'text' => $this->trans('notifications.common.open_application'),
                    'url' => $this->fqdn,
                ];
            }
        }
        $buttons[] = [
            'text' => $this->trans('notifications.common.deployment_logs_button'),
            'url' => $this->deployment_url,
        ];

        return new PushoverMessage(
            title: $title,
            level: 'success',
            message: $message,
            buttons: [
                ...$buttons,
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        if ($this->preview) {
            $title = $this->trans('notifications.deployment_success.slack_preview_title', ['pullRequestId' => $this->preview->pull_request_id]);
            $description = $this->trans('notifications.deployment_success.slack_description', ['name' => $this->application_name]);
            if ($this->preview->fqdn) {
                $description .= "\n".$this->trans('notifications.common.preview_url').": {$this->preview->fqdn}";
            }
        } else {
            $title = $this->trans('notifications.deployment_success.slack_title');
            $description = $this->trans('notifications.deployment_success.slack_description', ['name' => $this->application_name]);
            if ($this->fqdn) {
                $description .= "\n".$this->trans('notifications.common.application_url').": {$this->fqdn}";
            }
        }

        $description .= "\n\n*".$this->trans('notifications.common.project').':* '.data_get($this->application, 'environment.project.name');
        $description .= "\n*".$this->trans('notifications.common.environment').":* {$this->environment_name}";
        $description .= "\n*<{$this->deployment_url}|".$this->trans('notifications.common.deployment_logs').'>*';

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::successColor()
        );
    }

    public function toWebhook(): array
    {
        $data = [
            'success' => true,
            'message' => $this->trans('notifications.deployment_success.webhook_message'),
            'event' => 'deployment_success',
            'application_name' => $this->application_name,
            'application_uuid' => $this->application->uuid,
            'deployment_uuid' => $this->deployment_uuid,
            'deployment_url' => $this->deployment_url,
            'project' => data_get($this->application, 'environment.project.name'),
            'environment' => $this->environment_name,
        ];

        if ($this->preview) {
            $data['pull_request_id'] = $this->preview->pull_request_id;
            $data['preview_fqdn'] = $this->preview->fqdn;
        }

        if ($this->fqdn) {
            $data['fqdn'] = $this->fqdn;
        }

        return $data;
    }
}
