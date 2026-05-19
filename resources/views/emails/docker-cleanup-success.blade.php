<x-emails.layout>
{{ __('mail.docker_cleanup_success.body', ['server' => $name]) }}


<pre>
{{ $text }}
</pre>

</x-emails.layout>
