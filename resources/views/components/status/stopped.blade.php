@props([
    'status' => 'Stopped',
    'noLoading' => false,
])
@php
    $translateStatus = function (string $value): string {
        $normalized = str($value)->lower()->trim()->value();
        $translated = __($normalized);

        return $translated === $normalized
            ? str($value)->headline()->value()
            : $translated;
    };

    // Handle both colon format (backend) and parentheses format (from services.blade.php)
    // For exited containers, health status is hidden (health checks don't run on stopped containers)
    // exited:unhealthy → Exited
    // exited (unhealthy) → Exited

    if (str($status)->contains('(')) {
        // Already in parentheses format from services.blade.php - use as-is
        $displayStatus = $status;
        $healthStatus = str($status)->after('(')->before(')')->trim()->value();

        // Don't show health status for exited containers (health checks don't run on stopped containers)
        if (str($displayStatus)->lower()->contains('exited')) {
            $displayStatus = $translateStatus(str($status)->before('(')->trim()->value());
            $healthStatus = null;
        } else {
            $displayStatus = $translateStatus(str($status)->before('(')->trim()->value());
            $healthStatus = $healthStatus ? $translateStatus($healthStatus) : null;
        }
    } elseif (str($status)->contains(':')) {
        // Colon format from backend - transform it
        $parts = explode(':', $status);
        $displayStatus = $translateStatus($parts[0]);
        $healthStatus = $parts[1] ?? null;

        // Don't show health status for exited containers (health checks don't run on stopped containers)
        if (str($displayStatus)->lower()->contains('exited')) {
            $healthStatus = null;
        } else {
            $healthStatus = $healthStatus ? $translateStatus($healthStatus) : null;
        }
    } else {
        // Simple status without health
        $displayStatus = $translateStatus($status);
        $healthStatus = null;
    }
@endphp
<div class="flex items-center">
    @if (!$noLoading)
        <x-loading wire:loading.delay.longer />
    @endif
    <span wire:loading.remove.delay.longer class="flex items-center">
        <div class="badge badge-error "></div>
        <div class="pl-2 pr-1 text-xs font-bold text-error">{{ $displayStatus }}</div>
        @if ($healthStatus && !str($displayStatus)->contains('('))
            <div class="text-xs text-error">({{ $healthStatus }})</div>
        @endif
    </span>
</div>
