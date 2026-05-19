<x-emails.layout>
{{ __('mail.scheduled_task_failed.body', ['name' => $task->name]) }}

<pre>
{{ $output }}
</pre>

{{ __('mail.scheduled_task_failed.cta', ['url' => $url]) }}
</x-emails.layout>
