<?php

namespace App\Notifications\ScheduledTask;

use App\Models\ScheduledTask;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class TaskFailed extends CustomEmailNotification
{
    public ?string $url = null;

    public function __construct(public ScheduledTask $task, public string $output)
    {
        $this->onQueue('high');
        $this->useLocale();
        if ($task->application) {
            $this->url = $task->application->taskLink($task->uuid);
        } elseif ($task->service) {
            $this->url = $task->service->taskLink($task->uuid);
        }
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('scheduled_task_failure');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.scheduled_task_failed.subject', ['name' => $this->task->name]));
            $mail->view('emails.scheduled-task-failed', [
                'task' => $this->task,
                'url' => $this->url,
                'output' => $this->output,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.scheduled_task_failed.discord_title'),
            description: $this->trans('notifications.scheduled_task_failed.discord_description', ['name' => $this->task->name]),
            color: DiscordMessage::errorColor(),
        );

        if ($this->url) {
            $message->addField($this->trans('notifications.common.task'), "[{$this->trans('notifications.common.link')}]({$this->url})");
        }

        return $message;
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.scheduled_task_failed.telegram_message', [
            'name' => $this->task->name,
            'output' => $this->output,
        ]);
        if ($this->url) {
            $buttons[] = [
                'text' => $this->trans('notifications.common.open_task_in_coolify'),
                'url' => (string) $this->url,
            ];
        }

        return [
            'message' => $message,
        ];
    }

    public function toPushover(): PushoverMessage
    {
        $message = $this->trans('notifications.scheduled_task_failed.pushover_message', ['name' => $this->task->name]).'<br/>';

        if ($this->output) {
            $message .= '<br/><b>'.$this->trans('notifications.common.error_output').":</b>{$this->output}";
        }

        $buttons = [];
        if ($this->url) {
            $buttons[] = [
                'text' => $this->trans('notifications.common.open_task_in_coolify'),
                'url' => (string) $this->url,
            ];
        }

        return new PushoverMessage(
            title: $this->trans('notifications.scheduled_task_failed.pushover_title'),
            level: 'error',
            message: $message,
            buttons: $buttons,
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.scheduled_task_failed.slack_title');
        $description = $this->trans('notifications.scheduled_task_failed.slack_description', ['name' => $this->task->name]);

        if ($this->output) {
            $description .= "\n\n*".$this->trans('notifications.common.error_output').":* {$this->output}";
        }

        if ($this->url) {
            $description .= "\n\n*".$this->trans('notifications.common.task_url').":* {$this->url}";
        }

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
            'message' => $this->trans('notifications.scheduled_task_failed.webhook_message'),
            'event' => 'task_failed',
            'task_name' => $this->task->name,
            'task_uuid' => $this->task->uuid,
            'output' => $this->output,
        ];

        if ($this->task->application) {
            $data['application_uuid'] = $this->task->application->uuid;
        } elseif ($this->task->service) {
            $data['service_uuid'] = $this->task->service->uuid;
        }

        if ($this->url) {
            $data['url'] = $this->url;
        }

        return $data;
    }
}
