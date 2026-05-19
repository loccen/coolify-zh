<x-emails.layout>
    {{ __('mail.server_force_disabled.body', ['name' => $name]) }}

    {!! Illuminate\Mail\Markdown::parse(__('mail.server_force_disabled.cta')) !!}
</x-emails.layout>
