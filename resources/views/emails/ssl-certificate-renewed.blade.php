<x-emails.layout>
<h2>{{ __('mail.ssl_certificate_renewed.title') }}</h2>

<p>{{ __('mail.ssl_certificate_renewed.intro') }}</p>

<ul>
@foreach($resources as $resource)
    <li>{{ $resource->name }}</li>
@endforeach
</ul>

<div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;">
    <strong>⚠️ {{ __('mail.common.action_required') }}:</strong> {{ __('mail.ssl_certificate_renewed.action_required') }}
</div>

<p>{{ __('mail.ssl_certificate_renewed.old_certificate_notice') }}</p>

@if(isset($urls) && count($urls) > 0)
<div style="margin-top: 20px;">
    <p>{{ __('mail.ssl_certificate_renewed.redeploy_here') }}</p>
    <ul>
    @foreach($urls as $name => $url)
        <li><a href="{{ $url }}">{{ $name }}</a></li>
    @endforeach
    </ul>
</div>
@endif
</x-emails.layout> 
