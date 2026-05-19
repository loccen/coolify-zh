<x-emails.layout>
{{ __('mail.reset_password.requested') }}

{{ __('mail.reset_password.cta', ['url' => $url]) }}

{{ __('mail.reset_password.expiry', ['minutes' => $count]) }}
</x-emails.layout>
