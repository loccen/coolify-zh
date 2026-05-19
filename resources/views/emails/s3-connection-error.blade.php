<x-emails.layout>

{{ __('mail.s3_connection_error.body', ['name' => $name, 'url' => $url]) }}

{{ $reason }}
</x-emails.layout>
