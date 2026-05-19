<?php

namespace App\Notifications\Database;

use App\Models\ScheduledDatabaseBackup;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class BackupSuccess extends CustomEmailNotification
{
    public string $name;

    public string $frequency;

    public function __construct(ScheduledDatabaseBackup $backup, public $database, public $database_name)
    {
        $this->onQueue('high');
        $this->useLocale();

        $this->name = $database->name;
        $this->frequency = $backup->frequency;
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('backup_success');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.backup_success.subject', ['name' => $this->database->name]));
            $mail->view('emails.backup-success', [
                'name' => $this->name,
                'database_name' => $this->database_name,
                'frequency' => $this->frequency,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.backup_success.discord_title'),
            description: $this->trans('notifications.backup_success.discord_description', [
                'name' => $this->name,
                'database' => $this->database_name,
            ]),
            color: DiscordMessage::successColor(),
        );

        $message->addField($this->trans('notifications.common.frequency'), $this->frequency, true);

        return $message;
    }

    public function toTelegram(): array
    {
        $message = $this->trans('notifications.backup_success.telegram_message', [
            'name' => $this->name,
            'database' => $this->database_name,
            'frequency' => $this->frequency,
        ]);

        return [
            'message' => $message,
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.backup_success.pushover_title'),
            level: 'success',
            message: $this->trans('notifications.backup_success.pushover_message', [
                'name' => $this->name,
                'database' => $this->database_name,
            ]).'<br/><br/><b>'.$this->trans('notifications.common.frequency').":</b> {$this->frequency}.",
        );
    }

    public function toSlack(): SlackMessage
    {
        $title = $this->trans('notifications.backup_success.slack_title');
        $description = $this->trans('notifications.backup_success.slack_description', [
            'name' => $this->name,
            'database' => $this->database_name,
        ]);

        $description .= "\n\n*".$this->trans('notifications.common.frequency').":* {$this->frequency}";

        return new SlackMessage(
            title: $title,
            description: $description,
            color: SlackMessage::successColor()
        );
    }

    public function toWebhook(): array
    {
        $url = base_url().'/project/'.data_get($this->database, 'environment.project.uuid').'/environment/'.data_get($this->database, 'environment.uuid').'/database/'.$this->database->uuid;

        return [
            'success' => true,
            'message' => $this->trans('notifications.backup_success.webhook_message'),
            'event' => 'backup_success',
            'database_name' => $this->name,
            'database_uuid' => $this->database->uuid,
            'database_type' => $this->database_name,
            'frequency' => $this->frequency,
            'url' => $url,
        ];
    }
}
