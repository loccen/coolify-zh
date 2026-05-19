<x-emails.layout>
{{ __('mail.server_patches_error.body', ['name' => $name]) }}

## {{ __('mail.common.error_heading') }}

- {{ __('mail.server_patches.operating_system', ['value' => ucfirst($osId)]) }}
- {{ __('mail.server_patches.package_manager', ['value' => $package_manager]) }}
- {{ __('notifications.common.error') }}: {{ $error }}

---

{{ __('mail.server_patches_error.dashboard', ['url' => $server_url]) }}
</x-emails.layout>
