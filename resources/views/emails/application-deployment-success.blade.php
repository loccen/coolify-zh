<x-emails.layout>
@if ($pull_request_id === 0)
{{ __('mail.application_deployment_success.body', ['name' => $name, 'fqdn' => $fqdn]) }}
@else
{{ __('mail.application_deployment_success.body_preview', ['pullRequestId' => $pull_request_id, 'name' => $name, 'fqdn' => $fqdn]) }}
@endif

[{{ __('mail.common.view_deployment_logs') }}]({{ $deployment_url }})

</x-emails.layout>
