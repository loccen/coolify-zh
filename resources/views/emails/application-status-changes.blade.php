<x-emails.layout>
{{ __('mail.application_status_changed.body', ['name' => $name]) }}

{{ __('mail.application_status_changed.ignore') }}

{{ __('mail.application_status_changed.check', ['url' => $resource_url ?? $application_url ?? '#']) }}
</x-emails.layout>
