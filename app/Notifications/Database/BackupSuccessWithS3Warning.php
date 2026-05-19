<?php

namespace App\Notifications\Database;

use App\Models\ScheduledDatabaseBackup;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class BackupSuccessWithS3Warning extends CustomEmailNotification
{
    public string $name;

    public string $frequency;

    public ?string $s3_storage_url = null;

    public function __construct(ScheduledDatabaseBackup $backup, public $database, public $database_name, public $s3_error)
    {
        $this->onQueue('high');
        $this->useLocale();

        $this->name = $database->name;
        $this->frequency = $backup->frequency;

        if ($backup->s3) {
            $this->s3_storage_url = base_url().'/storages/'.$backup->s3->uuid;
        }
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('backup_failure');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.backup_success_with_s3_warning.subject', ['name' => $this->database->name]));
            $mail->view('emails.backup-success-with-s3-warning', [
                'name' => $this->name,
                'database_name' => $this->database_name,
                'frequency' => $this->frequency,
                's3_error' => $this->s3_error,
                's3_storage_url' => $this->s3_storage_url,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.backup_success_with_s3_warning.discord_title'),
            description: $this->trans('notifications.backup_success_with_s3_warning.discord_description', [
                'name' => $this->name,
                'database' => $this->database_name,
            ]),
            color: DiscordMessage::warningColor(),
        );

        $message->addField($this->trans('notifications.common.frequency'), $this->frequency, true);
        $message->addField($this->trans('notifications.common.s3_error'), $this->s3_error);

        if ($this->s3_storage_url) {
            $message->addField($this->trans('notifications.common.s3_storage'), "[{$this->trans('notifications.common.check_s3_configuration')}]({$this->s3_storage_url})");
        }

        return $message;
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.backup_success_with_s3_warning.telegram_message', [
            'name' => $this->name,
            'database' => $this->database_name,
            'frequency' => $this->frequency,
            'error' => $this->s3_error,
        ]);

        if ($this->s3_storage_url) {
            $message .= "\n\n".$this->trans('notifications.common.check_s3_configuration').": {$this->s3_storage_url}";
        }

        return [
            'message' => $message,
        ];
    }

    public function toPushover(): PushoverMessage
    {
        $message = $this->trans('notifications.backup_success_with_s3_warning.pushover_message', [
            'name' => $this->name,
            'database' => $this->database_name,
        ]).'<br/><br/><b>'.$this->trans('notifications.common.frequency').":</b> {$this->frequency}.<br/><b>".$this->trans('notifications.common.s3_error').":</b> {$this->s3_error}";

        if ($this->s3_storage_url) {
            $message .= '<br/><br/><a href="'.$this->s3_storage_url.'">'.$this->trans('notifications.common.check_s3_configuration').'</a>';
        }

        return new PushoverMessage(
            title: $this->trans('notifications.backup_success_with_s3_warning.pushover_title'),
            level: 'warning',
            message: $message,
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.backup_success_with_s3_warning.slack_title');
        $description = $this->trans('notifications.backup_success_with_s3_warning.slack_description', [
            'name' => $this->name,
            'database' => $this->database_name,
        ]);

        $description .= "\n\n*".$this->trans('notifications.common.frequency').":* {$this->frequency}";
        $description .= "\n\n*".$this->trans('notifications.common.s3_error').":* {$this->s3_error}";

        if ($this->s3_storage_url) {
            $description .= "\n\n*".$this->trans('notifications.common.s3_storage').":* <{$this->s3_storage_url}|".$this->trans('notifications.common.check_s3_configuration').'>';
        }

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::warningColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/project/'.data_get($this->database, 'environment.project.uuid').'/environment/'.data_get($this->database, 'environment.uuid').'/database/'.$this->database->uuid;

        $data = [
            'success' => true,
            'message' => $this->trans('notifications.backup_success_with_s3_warning.webhook_message'),
            'event' => 'backup_success_with_s3_warning',
            'database_name' => $this->name,
            'database_uuid' => $this->database->uuid,
            'database_type' => $this->database_name,
            'frequency' => $this->frequency,
            's3_error' => $this->s3_error,
            'url' => $url,
        ];

        if ($this->s3_storage_url) {
            $data['s3_storage_url'] = $this->s3_storage_url;
        }

        return $data;
    }
}
