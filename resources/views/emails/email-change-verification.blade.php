<x-emails.layout>
{{ __('mail.email_change_verification.requested', ['email' => $newEmail]) }}

{{ __('mail.email_change_verification.code_prompt') }}

{{ __('mail.email_change_verification.code', ['code' => $verificationCode]) }}

{{ __('mail.email_change_verification.expiry', ['minutes' => $expiryMinutes]) }}

{{ __('mail.email_change_verification.ignore') }}
</x-emails.layout>
