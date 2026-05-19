<div>
    <x-slot:title>
        {{ __('Environment Variable') }} | Coolify
    </x-slot>
    <div class="flex gap-2">
        <h1>{{ __('Shared Variables for :project/:environment', ['project' => $project->name, 'environment' => $environment->name]) }}</h1>
        @can('update', $environment)
            <x-modal-input :buttonTitle="__('+ Add')" :title="__('New Shared Variable')">
                <livewire:project.shared.environment-variable.add :shared="true" />
            </x-modal-input>
        @endcan
        <x-forms.button canGate="update" :canResource="$environment" wire:click='switch'>{{ $view === 'normal' ? __('Developer view') : __('Normal view') }}</x-forms.button>
    </div>
    <div class="flex items-center gap-1 subtitle">{{ __('You can use these variables anywhere with') }} <span
            class="dark:text-warning text-coollabs">@{{ environment.VARIABLENAME }}</span><x-helper
            :helper="__('More info <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/environment-variables#shared-variables\' target=\'_blank\'>here</a>.')"></x-helper>
    </div>
    @if ($view === 'normal')
        <div class="flex flex-col gap-2">
            @forelse ($environment->environment_variables->sort()->sortBy('key') as $env)
                <livewire:project.shared.environment-variable.show wire:key="environment-{{ $env->id }}"
                    :env="$env" type="environment" />
            @empty
                <div>{{ __('No environment variables found.') }}</div>
            @endforelse
        </div>
    @else
        <form wire:submit='submit' class="flex flex-col gap-2">
            <x-forms.textarea canGate="update" :canResource="$environment" rows="20" class="whitespace-pre-wrap" id="variables" wire:model="variables" monospace
                :label="__('Environment Shared Variables')"></x-forms.textarea>
            <x-forms.button canGate="update" :canResource="$environment" type="submit" class="btn btn-primary">{{ __('Save All Environment Variables') }}</x-forms.button>
        </form>
    @endif
</div>
