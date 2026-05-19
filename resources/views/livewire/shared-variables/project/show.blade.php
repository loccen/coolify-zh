<div>
    <x-slot:title>
        {{ __('Project Variable') }} | Coolify
    </x-slot>
    <div class="flex gap-2 items-center">
        <h1>{{ __('Shared Variables for :name', ['name' => data_get($project, 'name')]) }}</h1>
        @can('update', $project)
            <x-modal-input :buttonTitle="__('+ Add')" :title="__('New Shared Variable')">
                <livewire:project.shared.environment-variable.add :shared="true" />
            </x-modal-input>
        @endcan
        <x-forms.button canGate="update" :canResource="$project" wire:click='switch'>{{ $view === 'normal' ? __('Developer view') : __('Normal view') }}</x-forms.button>
    </div>
    <div class="flex flex-wrap gap-1 subtitle">
        <div>{{ __('You can use these variables anywhere with') }}</div>
        <div class="dark:text-warning text-coollabs">@{{ project.VARIABLENAME }} </div>
        <x-helper
            :helper="__('More info <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/environment-variables#shared-variables\' target=\'_blank\'>here</a>.')"></x-helper>
    </div>
    @if ($view === 'normal')
        <div class="flex flex-col gap-2">
            @forelse ($project->environment_variables->sort()->sortBy('key') as $env)
                <livewire:project.shared.environment-variable.show wire:key="environment-{{ $env->id }}"
                    :env="$env" type="project" />
            @empty
                <div>{{ __('No environment variables found.') }}</div>
            @endforelse
        </div>
    @else
        <form wire:submit='submit' class="flex flex-col gap-2">
            <x-forms.textarea canGate="update" :canResource="$project" rows="20" class="whitespace-pre-wrap" id="variables" wire:model="variables" monospace
                :label="__('Project Shared Variables')"></x-forms.textarea>
            <x-forms.button canGate="update" :canResource="$project" type="submit" class="btn btn-primary">{{ __('Save All Environment Variables') }}</x-forms.button>
        </form>
    @endif
</div>
