<x-emails.layout>
{{ __('mail.subscription_invoice_failed.body') }}

{{ __('mail.subscription_invoice_failed.cta', ['url' => $stripeCustomerPortal]) }}
</x-emails.layout>
