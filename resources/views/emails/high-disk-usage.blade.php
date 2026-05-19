<x-emails.layout>
{{ __('mail.high_disk_usage.body', ['name' => $name, 'usage' => $disk_usage, 'threshold' => $threshold]) }}

{{ __('mail.high_disk_usage.cleanup') }}

{{ __('mail.high_disk_usage.threshold_hint') }}
</x-emails.layout>
