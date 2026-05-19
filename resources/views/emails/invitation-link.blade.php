<x-emails.layout>
{{ __('mail.invitation_link.intro', ['team' => $team, 'app' => config('app.name')]) }}

{{ __('mail.invitation_link.accept', ['url' => $invitation_link]) }}

{{ __('mail.invitation_link.contact') }}<br><br>

{{ __('mail.invitation_link.ignore') }}
</x-emails.layout>
