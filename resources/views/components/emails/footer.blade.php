{{ Illuminate\Mail\Markdown::parse('---') }}

{{ __('mail.common.sign_off') }}<br>
{{ config('app.name') ?? 'Coolify' }}

{{ Illuminate\Mail\Markdown::parse('['.__('mail.common.contact_support').'](https://coolify.io/docs/contact)') }}
