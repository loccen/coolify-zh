<?php

namespace App\Notifications\Database;

use App\Models\ScheduledDatabaseBackup;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class BackupFailed extends CustomEmailNotification
{
    public string $name;

    public string $frequency;

    public function __construct(ScheduledDatabaseBackup $backup, public $database, public $output, public $database_name)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->name = $database->name;
        $this->frequency = $backup->frequency;
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('backup_failure');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.backup_failed.subject', ['name' => $this->database->name]));
            $mail->view('emails.backup-failed', [
                'name' => $this->name,
                'database_name' => $this->database_name,
                'frequency' => $this->frequency,
                'output' => $this->output,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.backup_failed.discord_title'),
            description: $this->trans('notifications.backup_failed.discord_description', [
                'name' => $this->name,
                'database' => $this->database_name,
            ]),
            color: DiscordMessage::errorColor(),
            isCritical: true,
        );

        $message->addField($this->trans('notifications.common.frequency'), $this->frequency, true);
        $message->addField($this->trans('notifications.common.output'), $this->output);

        return $message;
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.backup_failed.telegram_message', [
            'name' => $this->name,
            'database' => $this->database_name,
            'frequency' => $this->frequency,
            'output' => $this->output,
        ]);

        return [
            'message' => $message,
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.backup_failed.pushover_title'),
            level: 'error',
            message: $this->trans('notifications.backup_failed.pushover_message', [
                'name' => $this->name,
                'database' => $this->database_name,
            ]).'<br/><br/><b>'.$this->trans('notifications.common.frequency').":</b> {$this->frequency} .<br/><b>".$this->trans('notifications.common.reason').":</b> {$this->output}",
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.backup_failed.slack_title');
        $description = $this->trans('notifications.backup_failed.slack_description', [
            'name' => $this->name,
            'database' => $this->database_name,
        ]);

        $description .= "\n\n*".$this->trans('notifications.common.frequency').":* {$this->frequency}";
        $description .= "\n\n*".$this->trans('notifications.common.error_output').":* {$this->output}";

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/project/'.data_get($this->database, 'environment.project.uuid').'/environment/'.data_get($this->database, 'environment.uuid').'/database/'.$this->database->uuid;

        return [
            'success' => false,
            'message' => $this->trans('notifications.backup_failed.webhook_message'),
            'event' => 'backup_failed',
            'database_name' => $this->name,
            'database_uuid' => $this->database->uuid,
            'database_type' => $this->database_name,
            'frequency' => $this->frequency,
            'error_output' => $this->output,
            'url' => $url,
        ];
    }
}
