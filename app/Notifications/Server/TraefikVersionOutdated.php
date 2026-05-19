<?php

namespace App\Notifications\Server;

use App\Notifications\CustomEmailNotification;
use App\Notifications\Dto\DiscordMessage;
use App\Notifications\Dto\PushoverMessage;
use App\Notifications\Dto\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class TraefikVersionOutdated extends CustomEmailNotification
{
    public function __construct(public Collection $servers)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function via(object $notifiable): array
    {
        return $notifiable->getEnabledChannels('traefik_outdated');
    }

    private function formatVersion(string $version): string
    {
        // Add 'v' prefix if not present for consistent display
        return str_starts_with($version, 'v') ? $version : "v{$version}";
    }

    private function getUpgradeTarget(array $info): string
    {
        // For minor upgrades, use the upgrade_target field (e.g., "v3.6")
        if (($info['type'] ?? 'patch_update') === 'minor_upgrade' && isset($info['upgrade_target'])) {
            return $this->formatVersion($info['upgrade_target']);
        }

        // For patch updates, show the full version
        return $this->formatVersion($info['latest'] ?? 'unknown');
    }

    public function toMail($notifiable = null): MailMessage
    {
        return $this->withUserVisibleLocale(function () {
            $mail = new MailMessage;
            $count = $this->servers->count();

            $serversWithUrls = $this->servers->map(function ($server) {
                return [
                    'name' => $server->name,
                    'uuid' => $server->uuid,
                    'url' => base_url().'/server/'.$server->uuid.'/proxy',
                    'outdatedInfo' => $server->outdatedInfo ?? [],
                ];
            });

            $mail->subject($this->trans('mail.traefik_version_outdated.subject', ['count' => $count]));
            $mail->view('emails.traefik-version-outdated', [
                'servers' => $serversWithUrls,
                'count' => $count,
            ]);

            return $mail;
        });
    }

    public function toDiscord(): DiscordMessage
    {
        $count = $this->servers->count();
        $hasUpgrades = $this->servers->contains(fn ($s) => ($s->outdatedInfo['type'] ?? 'patch_update') === 'minor_upgrade' ||
            isset($s->outdatedInfo['newer_branch_target'])
        );

        $description = $this->trans('notifications.traefik_version_outdated.discord_description', ['count' => $count])."\n\n";
        $description .= '**'.$this->trans('notifications.common.affected_servers').":**\n";

        foreach ($this->servers as $server) {
            $info = $server->outdatedInfo ?? [];
            $current = $this->formatVersion($info['current'] ?? 'unknown');
            $latest = $this->formatVersion($info['latest'] ?? 'unknown');
            $upgradeTarget = $this->getUpgradeTarget($info);
            $isPatch = ($info['type'] ?? 'patch_update') === 'patch_update';
            $hasNewerBranch = isset($info['newer_branch_target']);

            if ($isPatch && $hasNewerBranch) {
                $newerBranchTarget = $info['newer_branch_target'];
                $newerBranchLatest = $this->formatVersion($info['newer_branch_latest']);
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_newer_branch', ['target' => $newerBranchTarget, 'latest' => $newerBranchLatest])."\n";
            } elseif ($isPatch) {
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
            } else {
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_minor', ['name' => $server->name, 'current' => $current, 'latest' => $latest, 'target' => $upgradeTarget])."\n";
            }
        }

        $description .= "\n⚠️ ".$this->trans('notifications.traefik_version_outdated.recommendation');

        if ($hasUpgrades) {
            $description .= "\n\n📖 ".$this->trans('notifications.traefik_version_outdated.changelog_note');
        }

        return new DiscordMessage(
            title: $this->trans('notifications.traefik_version_outdated.discord_title'),
            description: $description,
            color: DiscordMessage::warningColor(),
        );
    }

    public function toTelegram(): array
    {
        $count = $this->servers->count();
        $hasUpgrades = $this->servers->contains(fn ($s) => ($s->outdatedInfo['type'] ?? 'patch_update') === 'minor_upgrade' ||
            isset($s->outdatedInfo['newer_branch_target'])
        );

        $message = $this->trans('notifications.traefik_version_outdated.telegram_message', ['count' => $count])."\n\n";
        $message .= $this->trans('notifications.common.affected_servers').":\n";

        foreach ($this->servers as $server) {
            $info = $server->outdatedInfo ?? [];
            $current = $this->formatVersion($info['current'] ?? 'unknown');
            $latest = $this->formatVersion($info['latest'] ?? 'unknown');
            $upgradeTarget = $this->getUpgradeTarget($info);
            $isPatch = ($info['type'] ?? 'patch_update') === 'patch_update';
            $hasNewerBranch = isset($info['newer_branch_target']);

            if ($isPatch && $hasNewerBranch) {
                $newerBranchTarget = $info['newer_branch_target'];
                $newerBranchLatest = $this->formatVersion($info['newer_branch_latest']);
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_newer_branch', ['target' => $newerBranchTarget, 'latest' => $newerBranchLatest])."\n";
            } elseif ($isPatch) {
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
            } else {
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_minor', ['name' => $server->name, 'current' => $current, 'latest' => $latest, 'target' => $upgradeTarget])."\n";
            }
        }

        $message .= "\n⚠️ ".$this->trans('notifications.traefik_version_outdated.recommendation');

        if ($hasUpgrades) {
            $message .= "\n\n📖 ".$this->trans('notifications.traefik_version_outdated.changelog_note');
        }

        return [
            'message' => $message,
            'buttons' => [],
        ];
    }

    public function toPushover(): PushoverMessage
    {
        $count = $this->servers->count();
        $hasUpgrades = $this->servers->contains(fn ($s) => ($s->outdatedInfo['type'] ?? 'patch_update') === 'minor_upgrade' ||
            isset($s->outdatedInfo['newer_branch_target'])
        );

        $message = $this->trans('notifications.traefik_version_outdated.pushover_message', ['count' => $count])."\n";
        $message .= $this->trans('notifications.common.affected_servers').":\n";

        foreach ($this->servers as $server) {
            $info = $server->outdatedInfo ?? [];
            $current = $this->formatVersion($info['current'] ?? 'unknown');
            $latest = $this->formatVersion($info['latest'] ?? 'unknown');
            $upgradeTarget = $this->getUpgradeTarget($info);
            $isPatch = ($info['type'] ?? 'patch_update') === 'patch_update';
            $hasNewerBranch = isset($info['newer_branch_target']);

            if ($isPatch && $hasNewerBranch) {
                $newerBranchTarget = $info['newer_branch_target'];
                $newerBranchLatest = $this->formatVersion($info['newer_branch_latest']);
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_newer_branch', ['target' => $newerBranchTarget, 'latest' => $newerBranchLatest])."\n";
            } elseif ($isPatch) {
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => $server->name, 'current' => $current, 'target' => $upgradeTarget])."\n";
            } else {
                $message .= $this->trans('notifications.traefik_version_outdated.server_line_minor', ['name' => $server->name, 'current' => $current, 'latest' => $latest, 'target' => $upgradeTarget])."\n";
            }
        }

        $message .= "\n".$this->trans('notifications.traefik_version_outdated.recommendation');

        if ($hasUpgrades) {
            $message .= "\n\n".$this->trans('notifications.traefik_version_outdated.changelog_note');
        }

        return new PushoverMessage(
            title: $this->trans('notifications.traefik_version_outdated.pushover_title'),
            level: 'warning',
            message: $message,
        );
    }

    public function toSlack(): SlackMessage
    {
        $count = $this->servers->count();
        $hasUpgrades = $this->servers->contains(fn ($s) => ($s->outdatedInfo['type'] ?? 'patch_update') === 'minor_upgrade' ||
            isset($s->outdatedInfo['newer_branch_target'])
        );

        $description = $this->trans('notifications.traefik_version_outdated.slack_description', ['count' => $count])."\n";
        $description .= '*'.$this->trans('notifications.common.affected_servers').":*\n";

        foreach ($this->servers as $server) {
            $info = $server->outdatedInfo ?? [];
            $current = $this->formatVersion($info['current'] ?? 'unknown');
            $latest = $this->formatVersion($info['latest'] ?? 'unknown');
            $upgradeTarget = $this->getUpgradeTarget($info);
            $isPatch = ($info['type'] ?? 'patch_update') === 'patch_update';
            $hasNewerBranch = isset($info['newer_branch_target']);

            if ($isPatch && $hasNewerBranch) {
                $newerBranchTarget = $info['newer_branch_target'];
                $newerBranchLatest = $this->formatVersion($info['newer_branch_latest']);
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => "`{$server->name}`", 'current' => $current, 'target' => $upgradeTarget])."\n";
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_newer_branch', ['target' => $newerBranchTarget, 'latest' => $newerBranchLatest])."\n";
            } elseif ($isPatch) {
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_patch', ['name' => "`{$server->name}`", 'current' => $current, 'target' => $upgradeTarget])."\n";
            } else {
                $description .= $this->trans('notifications.traefik_version_outdated.server_line_minor', ['name' => "`{$server->name}`", 'current' => $current, 'latest' => $latest, 'target' => $upgradeTarget])."\n";
            }
        }

        $description .= "\n:warning: ".$this->trans('notifications.traefik_version_outdated.recommendation');

        if ($hasUpgrades) {
            $description .= "\n\n:book: ".$this->trans('notifications.traefik_version_outdated.changelog_note');
        }

        return new SlackMessage(
            title: $this->trans('notifications.traefik_version_outdated.slack_title'),
            description: $description,
            color: SlackMessage::warningColor()
        );
    }

    public function toWebhook(): array
    {
        $servers = $this->servers->map(function ($server) {
            $info = $server->outdatedInfo ?? [];

            $webhookData = [
                'name' => $server->name,
                'uuid' => $server->uuid,
                'current_version' => $info['current'] ?? 'unknown',
                'latest_version' => $info['latest'] ?? 'unknown',
                'update_type' => $info['type'] ?? 'patch_update',
            ];

            // For minor upgrades, include the upgrade target (e.g., "v3.6")
            if (($info['type'] ?? 'patch_update') === 'minor_upgrade' && isset($info['upgrade_target'])) {
                $webhookData['upgrade_target'] = $info['upgrade_target'];
            }

            // Include newer branch info if available
            if (isset($info['newer_branch_target'])) {
                $webhookData['newer_branch_target'] = $info['newer_branch_target'];
                $webhookData['newer_branch_latest'] = $info['newer_branch_latest'];
            }

            return $webhookData;
        })->toArray();

        return [
            'success' => false,
            'message' => $this->trans('notifications.traefik_version_outdated.webhook_message'),
            'event' => 'traefik_version_outdated',
            'affected_servers_count' => $this->servers->count(),
            'servers' => $servers,
        ];
    }
}
