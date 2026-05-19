<x-emails.layout>
{{ __('mail.container_restarted.body', ['name' => $containerName, 'server' => $serverName]) }}

@if ($containerName === 'coolify-proxy')
{{ __('mail.container_restarted.proxy') }}

{{ __('mail.container_restarted.proxy_hint') }}
@endif
</x-emails.layout>
