<x-emails.layout>
{{ __('mail.container_stopped.body', ['name' => $containerName, 'server' => $serverName]) }}

@if ($url)
{{ __('mail.container_stopped.check', ['url' => $url]) }}
@endif
</x-emails.layout>
