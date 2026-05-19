<x-emails.layout>
{{ __('mail.scheduled_task_success.body', ['name' => $task->name]) }}

<pre>
{{ $output }}
</pre>

{{ __('mail.scheduled_task_success.cta', ['url' => $url]) }}
</x-emails.layout>
