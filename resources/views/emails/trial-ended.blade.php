<x-emails.layout>
{{ __('mail.trial_ended.body') }}

{{ __('mail.trial_ended.cta', ['url' => $stripeCustomerPortal]) }}
</x-emails.layout>
