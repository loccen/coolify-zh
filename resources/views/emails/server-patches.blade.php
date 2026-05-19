<x-emails.layout>
{{ __('mail.server_patches.body', ['count' => $total_updates, 'name' => $name]) }}

## {{ __('mail.common.summary_heading') }}

- {{ __('mail.server_patches.operating_system', ['value' => ucfirst($osId)]) }}
- {{ __('mail.server_patches.package_manager', ['value' => $package_manager]) }}
- {{ __('mail.server_patches.total_updates', ['count' => $total_updates]) }}

## {{ __('mail.common.available_updates_heading') }}

@if ($total_updates > 0)
@foreach ($updates as $update)

{{ __('mail.server_patches.package_line', ['package' => $update['package'], 'architecture' => $update['architecture'], 'current' => $update['current_version'], 'new' => $update['new_version'], 'repository' => $update['repository'] ?? __('mail.server_patches.unknown_repository')]) }}
@endforeach

## {{ __('mail.common.security_considerations_heading') }}

{{ __('mail.server_patches.security_warning') }}

### {{ __('mail.server_patches.critical_heading') }}
@php
$criticalPackages = collect($updates)->filter(function ($update) {
                return str_contains(strtolower($update['package']), 'docker') ||
                    str_contains(strtolower($update['package']), 'kernel') ||
                    str_contains(strtolower($update['package']), 'openssh') ||
                    str_contains(strtolower($update['package']), 'ssl');
            });
@endphp

@if ($criticalPackages->count() > 0)
@foreach ($criticalPackages as $package)
- {{ $package['package'] }}: {{ $package['current_version'] }} → {{ $package['new_version'] }}
@endforeach
@else
{{ __('mail.server_patches.critical_none') }}
@endif

## {{ __('mail.common.next_steps_heading') }}

@foreach (__('mail.server_patches.steps') as $index => $step)
{{ $index + 1 }}. {{ $step }}
@endforeach
@else
{{ __('mail.server_patches.up_to_date') }}
@endif

---

{{ __('mail.server_patches.dashboard', ['url' => $server_url]) }}
</x-emails.layout>
