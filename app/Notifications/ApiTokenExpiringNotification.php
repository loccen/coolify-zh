<?php

namespace App\Notifications;

use App\Models\PersonalAccessToken;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ApiTokenExpiringNotification extends CustomEmailNotification
{
    protected string $tokenName;

    protected string $expiresAt;

    protected string $manageUrl;

    public function __construct(public PersonalAccessToken $token)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->tokenName = $token->name;
        $this->expiresAt = $token->expires_at?->format('Y-m-d H:i:s') ?? '';
        $this->manageUrl = route('security.api-tokens');
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('api_token_expiring');
    }

    public function toMail(): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.api_token_expiring.subject', ['tokenName' => $this->tokenName]));
            $mail->view('emails.api-token-expiring', [
                'tokenName' => $this->tokenName,
                'expiresAt' => $this->expiresAt,
                'manageUrl' => $this->manageUrl,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $message = new DiscordMessage(
            title: $this->trans('notifications.api_token_expiring.discord_title'),
            description: $this->trans('notifications.api_token_expiring.discord_description', [
                'token' => $this->tokenName,
                'expiresAt' => $this->expiresAt,
            ]),
            color: DiscordMessage::warningColor(),
        );

        $message->addField(
            $this->trans('notifications.common.manage_tokens_field'),
            "[{$this->trans('notifications.common.manage_security_settings')}]({$this->manageUrl})"
        );

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => $this->trans('notifications.api_token_expiring.telegram_message', [
                'token' => $this->tokenName,
                'expiresAt' => $this->expiresAt,
            ]),
            'buttons' => [
                [
                    'text' => $this->trans('notifications.common.manage_tokens'),
                    'url' => $this->manageUrl,
                ],
            ],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        return new PushoverMessage(
            title: $this->trans('notifications.api_token_expiring.pushover_title'),
            level: 'warning',
            message: $this->trans('notifications.api_token_expiring.pushover_message', [
                'token' => $this->tokenName,
                'expiresAt' => $this->expiresAt,
            ]),
            buttons: [
                [
                    'text' => $this->trans('notifications.common.manage_tokens'),
                    'url' => $this->manageUrl,
                ],
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        return new SlackMessage(
            title: $this->trans('notifications.api_token_expiring.slack_title'),
            description: $this->trans('notifications.api_token_expiring.slack_description', [
                'token' => $this->tokenName,
                'expiresAt' => $this->expiresAt,
                'url' => $this->manageUrl,
            ]),
            color: SlackMessage::warningColor(),
        );
    }
}
