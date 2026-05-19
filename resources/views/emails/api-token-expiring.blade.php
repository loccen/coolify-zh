<x-emails.layout>
{{ __('mail.api_token_expiring.intro', ['tokenName' => $tokenName, 'expiresAt' => $expiresAt]) }}

{{ __('mail.api_token_expiring.rotate') }}

{{ __('mail.common.manage_api_tokens_here') }}
</x-emails.layout>
