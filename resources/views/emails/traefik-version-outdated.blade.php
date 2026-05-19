<x-emails.layout>
{{ __('mail.traefik_version_outdated.body', ['count' => $count]) }}

## {{ __('mail.traefik_version_outdated.affected_servers') }}

@foreach ($servers as $server)
@php
    $serverName = data_get($server, 'name', 'Unknown Server');
    $serverUrl = data_get($server, 'url', '#');
    $info = data_get($server, 'outdatedInfo', []);
    $current = data_get($info, 'current', 'unknown');
    $latest = data_get($info, 'latest', 'unknown');
    $isPatch = (data_get($info, 'type', 'patch_update') === 'patch_update');
    $hasNewerBranch = isset($info['newer_branch_target']);
    $hasUpgrades = $hasUpgrades ?? false;
    if (!$isPatch || $hasNewerBranch) {
        $hasUpgrades = true;
    }
    // Add 'v' prefix for display
    $current = str_starts_with($current, 'v') ? $current : "v{$current}";
    $latest = str_starts_with($latest, 'v') ? $latest : "v{$latest}";

    // For minor upgrades, use the upgrade_target (e.g., "v3.6")
    if (!$isPatch && data_get($info, 'upgrade_target')) {
        $upgradeTarget = data_get($info, 'upgrade_target');
        $upgradeTarget = str_starts_with($upgradeTarget, 'v') ? $upgradeTarget : "v{$upgradeTarget}";
    } else {
        // For patch updates, show the full version
        $upgradeTarget = $latest;
    }

    // Get newer branch info if available
    if ($hasNewerBranch) {
        $newerBranchTarget = data_get($info, 'newer_branch_target', 'unknown');
        $newerBranchLatest = data_get($info, 'newer_branch_latest', 'unknown');
        $newerBranchLatest = str_starts_with($newerBranchLatest, 'v') ? $newerBranchLatest : "v{$newerBranchLatest}";
    }
@endphp
@if ($isPatch && $hasNewerBranch)
{{ __('mail.traefik_version_outdated.line_patch_with_minor', ['server' => $serverName, 'url' => $serverUrl, 'current' => $current, 'target' => $upgradeTarget, 'newerTarget' => $newerBranchTarget, 'newerLatest' => $newerBranchLatest]) }}
@elseif ($isPatch)
{{ __('mail.traefik_version_outdated.line_patch', ['server' => $serverName, 'url' => $serverUrl, 'current' => $current, 'target' => $upgradeTarget]) }}
@else
{{ __('mail.traefik_version_outdated.line_minor', ['server' => $serverName, 'url' => $serverUrl, 'current' => $current, 'latest' => $latest, 'target' => $upgradeTarget]) }}
@endif
@endforeach

## {{ __('mail.common.recommendation_heading') }}

{{ __('mail.traefik_version_outdated.recommendation') }}

@if ($hasUpgrades ?? false)
{{ __('mail.traefik_version_outdated.minor_warning') }}
@endif

## {{ __('mail.common.next_steps_heading') }}

@foreach (__('mail.traefik_version_outdated.steps') as $index => $step)
{{ $index + 1 }}. {{ $step }}
@endforeach

---

{{ __('mail.traefik_version_outdated.footer') }}
</x-emails.layout>
