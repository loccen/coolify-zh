<x-emails.layout>
{{ trans_choice('mail.before_trial_conversion.intro', 1, ['days' => config('constants.limits.trial_period')]) }}

{{ __('mail.before_trial_conversion.description') }}

{!! Illuminate\Mail\Markdown::parse(__('mail.before_trial_conversion.cta')) !!}
</x-emails.layout>
