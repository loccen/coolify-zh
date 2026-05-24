<form class="flex flex-col w-full gap-2 rounded-sm" wire:submit='submit'>
    <x-forms.input placeholder="{{ __('Your Cool Project') }}" id="name" label="{{ __('Name') }}" required />
    <x-forms.input placeholder="{{ __('This is my cool project everyone knows about') }}" id="description" label="{{ __('Description') }}" />
    <div class="subtitle">{!! __('New project will have a default :environment environment.', ['environment' => '<span class=\'dark:text-warning font-bold\'>production</span>']) !!}</div>
    <x-forms.button type="submit">
        {{ __('Continue') }}
    </x-forms.button>
</form>
