<x-emails.layout>
{{ __('mail.hetzner_deletion_failed.body', ['id' => $hetznerServerId]) }}

{{ __('mail.common.error_heading') }}:
<pre>
{{ $errorMessage }}
</pre>

{{ __('mail.hetzner_deletion_failed.still_exists') }}

{{ __('mail.hetzner_deletion_failed.manual_delete') }}

</x-emails.layout>
