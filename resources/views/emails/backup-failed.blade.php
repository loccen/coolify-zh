<x-emails.layout>
{{ __('mail.backup_failed.body', ['name' => $name, 'database' => $database_name ? ' (db:'.$database_name.')' : '', 'frequency' => $frequency]) }}

### {{ __('mail.common.reason_heading') }}

{{ $output }}
</x-emails.layout>
