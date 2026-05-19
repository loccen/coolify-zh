<x-emails.layout>
{{ __('mail.docker_cleanup_failed.body', ['server' => $name]) }}

<pre>
{{ $text }}
</pre>

</x-emails.layout>
