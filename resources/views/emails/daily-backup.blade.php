<x-emails.layout>
@foreach ($databases as $database_name => $databases)

@if(data_get($databases,'failed_count') > 0)

<div style="color:red">

{{ __('mail.daily_backup.failed', ['database' => $database_name]) }}

</div>

@else

{{ __('mail.daily_backup.success', ['database' => $database_name]) }}

@endif

@endforeach
</x-emails.layout>
