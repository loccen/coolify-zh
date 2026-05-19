<?php

namespace App\Notifications\Server;

use App\Models\Server;
use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ServerPatchCheck extends CustomEmailNotification
{
    public string $serverUrl;

    public function __construct(public Server $server, public array $patchData)
    {
        $this->onQueue('high');
        $this->useLocale();
        $this->serverUrl = base_url().'/server/'.$this->server->uuid.'/security/patches';
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('server_patch');
    }

    public function toMail($notifiable = null): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;

            if (isset($this->patchData['error'])) {
                $mail->subject($this->trans('mail.server_patches_error.subject', ['name' => $this->server->name]));
                $mail->view('emails.server-patches-error', [
                    'name' => $this->server->name,
                    'error' => $this->patchData['error'],
                    'osId' => $this->patchData['osId'] ?? 'unknown',
                    'package_manager' => $this->patchData['package_manager'] ?? 'unknown',
                    'server_url' => $this->serverUrl,
                ]);

                return $mail;
            }

            $totalUpdates = $this->patchData['total_updates'] ?? 0;
            $mail->subject($this->trans('mail.server_patches.subject', [
                'count' => $totalUpdates,
                'name' => $this->server->name,
            ]));
            $mail->view('emails.server-patches', [
                'name' => $this->server->name,
                'total_updates' => $totalUpdates,
                'updates' => $this->patchData['updates'] ?? [],
                'osId' => $this->patchData['osId'] ?? 'unknown',
                'package_manager' => $this->patchData['package_manager'] ?? 'unknown',
                'server_url' => $this->serverUrl,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        if (isset($this->patchData['error'])) {
            $osId = $this->patchData['osId'] ?? 'unknown';
            $packageManager = $this->patchData['package_manager'] ?? 'unknown';
            $error = $this->patchData['error'];

            $description = $this->trans('notifications.server_patch_check.error.discord_description', ['server' => $this->server->name])."\n\n";
            $description .= '**'.$this->trans('notifications.common.error_details').":**\n";
            $description .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
            $description .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
            $description .= '• '.$this->trans('notifications.common.error').": {$error}\n\n";
            $description .= "[{$this->trans('notifications.common.manage_server')}]($this->serverUrl)";

            return new DiscordMessage(
                title: $this->trans('notifications.server_patch_check.error.discord_title', ['server' => $this->server->name]),
                description: $description,
                color: DiscordMessage::errorColor(),
            );
        }

        $totalUpdates = $this->patchData['total_updates'] ?? 0;
        $updates = $this->patchData['updates'] ?? [];
        $osId = $this->patchData['osId'] ?? 'unknown';
        $packageManager = $this->patchData['package_manager'] ?? 'unknown';

        $description = $this->trans('notifications.server_patch_check.available.discord_description', [
            'count' => $totalUpdates,
            'server' => $this->server->name,
        ])."\n\n";
        $description .= '**'.$this->trans('notifications.common.summary').":**\n";
        $description .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
        $description .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
        $description .= '• '.$this->trans('notifications.common.total_updates').": {$totalUpdates}\n\n";

        if (count($updates) > 0) {
            $description .= '**'.$this->trans('notifications.common.sample_updates').":**\n";
            $sampleUpdates = array_slice($updates, 0, 5);
            foreach ($sampleUpdates as $update) {
                $description .= "• {$update['package']}: {$update['current_version']} → {$update['new_version']}\n";
            }
            if (count($updates) > 5) {
                $description .= '• '.$this->trans('notifications.common.more_packages', ['count' => count($updates) - 5])."\n";
            }

            $criticalPackages = collect($updates)->filter(function ($update) {
                return str_contains(strtolower($update['package']), 'docker') ||
                    str_contains(strtolower($update['package']), 'kernel') ||
                    str_contains(strtolower($update['package']), 'openssh') ||
                    str_contains(strtolower($update['package']), 'ssl');
            });

            if ($criticalPackages->count() > 0) {
                $description .= "\n**".$this->trans('notifications.server_patch_check.available.critical_packages_detected').'** ';
                $description .= '('.$this->trans('notifications.common.packages_may_require_restarts', ['count' => $criticalPackages->count()]).')';
            }
        }

        $description .= "\n[{$this->trans('notifications.common.manage_server_patches')}]($this->serverUrl)";

        return new DiscordMessage(
            title: $this->trans('notifications.server_patch_check.available.discord_title', ['server' => $this->server->name]),
            description: $description,
            color: DiscordMessage::errorColor(),
        );

    }

    public function toTelegram(): array
    {
        if (isset($this->patchData['error'])) {
            $osId = $this->patchData['osId'] ?? 'unknown';
            $packageManager = $this->patchData['package_manager'] ?? 'unknown';
            $error = $this->patchData['error'];

            $message = $this->trans('notifications.server_patch_check.error.telegram_message', ['server' => $this->server->name])."\n\n";
            $message .= $this->trans('notifications.common.error_details').":\n";
            $message .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
            $message .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
            $message .= '• '.$this->trans('notifications.common.error').": {$error}\n\n";

            return [
                'message' => $message,
                'buttons' => [
                    [
                        'text' => $this->trans('notifications.common.manage_server'),
                        'url' => $this->serverUrl,
                    ],
                ],
            ];
        }

        $totalUpdates = $this->patchData['total_updates'] ?? 0;
        $updates = $this->patchData['updates'] ?? [];
        $osId = $this->patchData['osId'] ?? 'unknown';
        $packageManager = $this->patchData['package_manager'] ?? 'unknown';

        $message = $this->trans('notifications.server_patch_check.available.telegram_message', [
            'count' => $totalUpdates,
            'server' => $this->server->name,
        ])."\n\n";
        $message .= $this->trans('notifications.common.summary').":\n";
        $message .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
        $message .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
        $message .= '• '.$this->trans('notifications.common.total_updates').": {$totalUpdates}\n\n";

        if (count($updates) > 0) {
            $message .= $this->trans('notifications.common.sample_updates').":\n";
            $sampleUpdates = array_slice($updates, 0, 5);
            foreach ($sampleUpdates as $update) {
                $message .= "• {$update['package']}: {$update['current_version']} → {$update['new_version']}\n";
            }
            if (count($updates) > 5) {
                $message .= '• '.$this->trans('notifications.common.more_packages', ['count' => count($updates) - 5])."\n";
            }

            $criticalPackages = collect($updates)->filter(function ($update) {
                return str_contains(strtolower($update['package']), 'docker') ||
                    str_contains(strtolower($update['package']), 'kernel') ||
                    str_contains(strtolower($update['package']), 'openssh') ||
                    str_contains(strtolower($update['package']), 'ssl');
            });

            if ($criticalPackages->count() > 0) {
                $message .= "\n⚠️ ".$this->trans('notifications.server_patch_check.available.critical_packages_detected').': ';
                $message .= $this->trans('notifications.common.packages_may_require_restarts', ['count' => $criticalPackages->count()])."\n";
                foreach ($criticalPackages->take(3) as $package) {
                    $message .= "• {$package['package']}: {$package['current_version']} → {$package['new_version']}\n";
                }
                if ($criticalPackages->count() > 3) {
                    $message .= '• '.$this->trans('notifications.common.more_critical_packages', ['count' => $criticalPackages->count() - 3])."\n";
                }
            }
        }

        return [
            'message' => $message,
            'buttons' => [
                [
                    'text' => $this->trans('notifications.common.manage_server_patches'),
                    'url' => $this->serverUrl,
                ],
            ],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        if (isset($this->patchData['error'])) {
            $osId = $this->patchData['osId'] ?? 'unknown';
            $packageManager = $this->patchData['package_manager'] ?? 'unknown';
            $error = $this->patchData['error'];

            $message = $this->trans('notifications.server_patch_check.error.pushover_message', ['server' => $this->server->name])."\n\n";
            $message .= $this->trans('notifications.common.error_details').":\n";
            $message .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
            $message .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
            $message .= '• '.$this->trans('notifications.common.error').": {$error}\n\n";

            return new PushoverMessage(
                title: $this->trans('notifications.server_patch_check.error.pushover_title'),
                level: 'error',
                message: $message,
                buttons: [
                    [
                        'text' => $this->trans('notifications.common.manage_server'),
                        'url' => $this->serverUrl,
                    ],
                ],
            );
        }

        $totalUpdates = $this->patchData['total_updates'] ?? 0;
        $updates = $this->patchData['updates'] ?? [];
        $osId = $this->patchData['osId'] ?? 'unknown';
        $packageManager = $this->patchData['package_manager'] ?? 'unknown';

        $message = $this->trans('notifications.server_patch_check.available.pushover_message', [
            'count' => $totalUpdates,
            'server' => $this->server->name,
        ])."\n\n";
        $message .= $this->trans('notifications.common.summary').":\n";
        $message .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
        $message .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
        $message .= '• '.$this->trans('notifications.common.total_updates').": {$totalUpdates}\n\n";

        if (count($updates) > 0) {
            $message .= $this->trans('notifications.common.sample_updates').":\n";
            $sampleUpdates = array_slice($updates, 0, 3);
            foreach ($sampleUpdates as $update) {
                $message .= "• {$update['package']}: {$update['current_version']} → {$update['new_version']}\n";
            }
            if (count($updates) > 3) {
                $message .= '• '.$this->trans('notifications.common.more_packages', ['count' => count($updates) - 3])."\n";
            }

            $criticalPackages = collect($updates)->filter(function ($update) {
                return str_contains(strtolower($update['package']), 'docker') ||
                    str_contains(strtolower($update['package']), 'kernel') ||
                    str_contains(strtolower($update['package']), 'openssh') ||
                    str_contains(strtolower($update['package']), 'ssl');
            });

            if ($criticalPackages->count() > 0) {
                $message .= "\n".$this->trans('notifications.server_patch_check.available.critical_packages_detected').': ';
                $message .= $this->trans('notifications.common.packages_may_require_restarts', ['count' => $criticalPackages->count()]);
            }
        }

        return new PushoverMessage(
            title: $this->trans('notifications.server_patch_check.available.pushover_title'),
            level: 'error',
            message: $message,
            buttons: [
                [
                    'text' => $this->trans('notifications.common.manage_server_patches'),
                    'url' => $this->serverUrl,
                ],
            ],
        );
    }

    public function toSlack(): SlackMessage
    {
        if (isset($this->patchData['error'])) {
            $osId = $this->patchData['osId'] ?? 'unknown';
            $packageManager = $this->patchData['package_manager'] ?? 'unknown';
            $error = $this->patchData['error'];

            $description = $this->trans('notifications.server_patch_check.error.slack_description', ['server' => $this->server->name])."\n\n";
            $description .= '*'.$this->trans('notifications.common.error_details').":*\n";
            $description .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
            $description .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
            $description .= '• '.$this->trans('notifications.common.error').": `{$error}`\n\n";
            $description .= "\n:link: <{$this->serverUrl}|".$this->trans('notifications.common.manage_server').'>';

            return new SlackMessage(
                title: $this->trans('notifications.server_patch_check.error.slack_title'),
                description: $description,
                color: SlackMessage::errorColor()
            );
        }

        $totalUpdates = $this->patchData['total_updates'] ?? 0;
        $updates = $this->patchData['updates'] ?? [];
        $osId = $this->patchData['osId'] ?? 'unknown';
        $packageManager = $this->patchData['package_manager'] ?? 'unknown';

        $description = $this->trans('notifications.server_patch_check.available.slack_description', [
            'count' => $totalUpdates,
            'server' => $this->server->name,
        ])."\n\n";
        $description .= '*'.$this->trans('notifications.common.summary').":*\n";
        $description .= '• '.$this->trans('notifications.common.operating_system').': '.ucfirst($osId)."\n";
        $description .= '• '.$this->trans('notifications.common.package_manager').": {$packageManager}\n";
        $description .= '• '.$this->trans('notifications.common.total_updates').": {$totalUpdates}\n\n";

        if (count($updates) > 0) {
            $description .= '*'.$this->trans('notifications.common.sample_updates').":*\n";
            $sampleUpdates = array_slice($updates, 0, 5);
            foreach ($sampleUpdates as $update) {
                $description .= "• `{$update['package']}`: {$update['current_version']} → {$update['new_version']}\n";
            }
            if (count($updates) > 5) {
                $description .= '• '.$this->trans('notifications.common.more_packages', ['count' => count($updates) - 5])."\n";
            }

            $criticalPackages = collect($updates)->filter(function ($update) {
                return str_contains(strtolower($update['package']), 'docker') ||
                    str_contains(strtolower($update['package']), 'kernel') ||
                    str_contains(strtolower($update['package']), 'openssh') ||
                    str_contains(strtolower($update['package']), 'ssl');
            });

            if ($criticalPackages->count() > 0) {
                $description .= "\n:warning: *".$this->trans('notifications.server_patch_check.available.critical_packages_detected').':* ';
                $description .= $this->trans('notifications.common.packages_may_require_restarts', ['count' => $criticalPackages->count()])."\n";
                foreach ($criticalPackages->take(3) as $package) {
                    $description .= "• `{$package['package']}`: {$package['current_version']} → {$package['new_version']}\n";
                }
                if ($criticalPackages->count() > 3) {
                    $description .= '• '.$this->trans('notifications.common.more_critical_packages', ['count' => $criticalPackages->count() - 3])."\n";
                }
            }
        }

        $description .= "\n:link: <{$this->serverUrl}|".$this->trans('notifications.common.manage_server_patches').'>';

        return new SlackMessage(
            title: $this->trans('notifications.server_patch_check.available.slack_title'),
            description: $description,
            color: SlackMessage::errorColor()
        );
    }

    public function toWebhook(): array
    {
        // Handle error case
        if (isset($this->patchData['error'])) {
            return [
                'success' => false,
                'message' => $this->trans('notifications.server_patch_check.error.webhook_message'),
                'event' => 'server_patch_check_error',
                'server_name' => $this->server->name,
                'server_uuid' => $this->server->uuid,
                'os_id' => $this->patchData['osId'] ?? 'unknown',
                'package_manager' => $this->patchData['package_manager'] ?? 'unknown',
                'error' => $this->patchData['error'],
                'url' => $this->serverUrl,
            ];
        }

        $totalUpdates = $this->patchData['total_updates'] ?? 0;
        $updates = $this->patchData['updates'] ?? [];

        $criticalPackages = collect($updates)->filter(function ($update) {
            return str_contains(strtolower($update['package']), 'docker') ||
                str_contains(strtolower($update['package']), 'kernel') ||
                str_contains(strtolower($update['package']), 'openssh') ||
                str_contains(strtolower($update['package']), 'ssl');
        });

        return [
            'success' => false,
            'message' => $this->trans('notifications.server_patch_check.available.webhook_message'),
            'event' => 'server_patch_check',
            'server_name' => $this->server->name,
            'server_uuid' => $this->server->uuid,
            'total_updates' => $totalUpdates,
            'os_id' => $this->patchData['osId'] ?? 'unknown',
            'package_manager' => $this->patchData['package_manager'] ?? 'unknown',
            'updates' => $updates,
            'critical_packages_count' => $criticalPackages->count(),
            'url' => $this->serverUrl,
        ];
    }
}
