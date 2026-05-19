<x-emails.layout>
{{ __('mail.backup_success.body', ['name' => $name, 'database' => $database_name ? ' (db:'.$database_name.')' : '', 'frequency' => $frequency]) }}
</x-emails.layout>
