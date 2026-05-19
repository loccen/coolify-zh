<?php

namespace App\Notifications\Application;

use App\Models\Application;
use App\Models\ApplicationPreview;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class DeploymentFailed extends CustomEmailNotification
{
    public Application $application;

    public ?ApplicationPreview $preview = null;

    public string $deployment_uuid;

    public string $application_name;

    public string $project_uuid;

    public string $environment_uuid;

    public string $environment_name;

    public ?string $deployment_url = null;

    public ?string $fqdn = null;

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
        return $notifiable->getEnabledChannels('deployment_failure');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $pullRequestId = data_get($this->preview, 'pull_request_id', 0);
            $fqdn = $this->fqdn;
            if ($pullRequestId === 0) {
                $mail->subject($this->trans('mail.application_deployment_failed.subject', ['name' => $this->application_name]));
            } else {
                $fqdn = $this->preview->fqdn;
                $mail->subject($this->trans('mail.application_deployment_failed.subject_preview', [
                    'pullRequestId' => $pullRequestId,
                    'name' => $this->application_name,
                ]));
            }
            $mail->view('emails.application-deployment-failed', [
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
                title: $this->trans('notifications.deployment_failed.discord_title'),
                description: $this->trans('notifications.deployment_failed.discord_preview_description', [
                    'pullRequestId' => $this->preview->pull_request_id,
                ]),
                color: DiscordMessage::errorColor(),
                isCritical: true,
            );

            $message->addField($this->trans('notifications.common.project'), data_get($this->application, 'environment.project.name'), true);
            $message->addField($this->trans('notifications.common.environment'), $this->environment_name, true);
            $message->addField($this->trans('notifications.common.name'), $this->application_name, true);

            $message->addField($this->trans('notifications.common.deployment_logs'), '[Link]('.$this->deployment_url.')');
            if ($this->fqdn) {
                $message->addField($this->trans('notifications.common.domain'), $this->fqdn, true);
            }
        } else {
            if ($this->fqdn) {
                $description = '[Open application]('.$this->fqdn.')';
            } else {
                $description = '';
            }
            $message = new DiscordMessage(
                title: $this->trans('notifications.deployment_failed.discord_title'),
                description: $description,
                color: DiscordMessage::errorColor(),
                isCritical: true,
            );

            $message->addField($this->trans('notifications.common.project'), data_get($this->application, 'environment.project.name'), true);
            $message->addField($this->trans('notifications.common.environment'), $this->environment_name, true);
            $message->addField($this->trans('notifications.common.name'), $this->application_name, true);

            $message->addField($this->trans('notifications.common.deployment_logs'), '[Link]('.$this->deployment_url.')');
        }

        return $message;
    }

    public function toTelegram(): array
    {
        if ($this->preview) {
            $message = $this->trans('notifications.deployment_failed.telegram_preview_message', [
                'pullRequestId' => $this->preview->pull_request_id,
                'name' => $this->application_name,
                'fqdn' => $this->preview->fqdn,
            ]);
        } else {
            $message = $this->trans('notifications.deployment_failed.telegram_message', [
                'name' => $this->application_name,
                'fqdn' => $this->fqdn,
            ]);
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
            $title = $this->trans('notifications.deployment_failed.pushover_preview_title', ['pullRequestId' => $this->preview->pull_request_id]);
            $message = $this->trans('notifications.deployment_failed.pushover_preview_message', ['name' => $this->application_name]);
        } else {
            $title = $this->trans('notifications.deployment_failed.pushover_title');
            $message = $this->trans('notifications.deployment_failed.pushover_message', ['name' => $this->application_name]);
        }

        $buttons[] = [
            'text' => $this->trans('notifications.common.deployment_logs_button'),
            'url' => $this->deployment_url,
        ];

        return new PushoverMessage(
            title: $title,
            level: 'error',
            message: $message,
            buttons: [
                ...$buttons,
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        if ($this->preview) {
            $title = $this->trans('notifications.deployment_failed.slack_preview_title', ['pullRequestId' => $this->preview->pull_request_id]);
            $description = $this->trans('notifications.deployment_failed.slack_description', ['name' => $this->application_name]);
            if ($this->preview->fqdn) {
                $description .= "\n".$this->trans('notifications.common.preview_url').": {$this->preview->fqdn}";
            }
        } else {
            $title = $this->trans('notifications.deployment_failed.slack_title');
            $description = $this->trans('notifications.deployment_failed.slack_description', ['name' => $this->application_name]);
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
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        $data = [
            'success' => false,
            'message' => 'Deployment failed',
            'event' => 'deployment_failed',
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
