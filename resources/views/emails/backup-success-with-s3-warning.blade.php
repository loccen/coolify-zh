<x-emails.layout>
{{ __('mail.backup_success_with_s3_warning.body', ['name' => $name, 'database' => $database_name ? ' (db:'.$database_name.')' : '', 'frequency' => $frequency]) }}

{{ __('mail.backup_success_with_s3_warning.s3_error', ['error' => $s3_error]) }}

@if($s3_storage_url)
{{ __('mail.backup_success_with_s3_warning.check_s3_configuration', ['url' => $s3_storage_url]) }}
@endif
</x-emails.layout>
