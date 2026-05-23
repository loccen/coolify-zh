@php
    $previewUrlTemplateHelper = __("Templates:<br/><span class='text-helper'>&#123;&#123; random &#125;&#125;</span> to generate a random sub-domain each time a PR is deployed<br/><span class='text-helper'>&#123;&#123; pr_id &#125;&#125;</span> to use the pull request ID as the sub-domain, or <span class='text-helper'>&#123;&#123; domain &#125;&#125;</span> to replace the domain name with the application's domain name.");
@endphp

<form wire:submit='submit'>
    <div class="flex items-center gap-2">
        <h2>{{ __('Preview Deployments') }}</h2>
        @can('update', $application)
            <x-forms.button type="submit">{{ __('Save') }}</x-forms.button>
            <x-forms.button isHighlighted wire:click="resetToDefault">{{ __('Reset template to default') }}</x-forms.button>
        @endcan
    </div>
    <div class="pb-4 ">{{ __('Preview Deployments based on pull requests are here.') }}</div>
    <div class="flex flex-col gap-2 pb-4">
        <x-forms.input id="previewUrlTemplate" :label="__('Preview URL Template')"
            :helper="$previewUrlTemplateHelper" canGate="update" :canResource="$application" />
        @if ($previewUrlTemplate)
            <div class="">{{ __('Domain Preview: :preview', ['preview' => $previewUrlTemplate]) }}</div>
        @endif
    </div>
</form>
