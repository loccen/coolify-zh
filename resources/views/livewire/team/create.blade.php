<form class="flex flex-col w-full gap-2" wire:submit='submit'>
    <x-forms.input id="name" :label="__('input.name')" required />
    <x-forms.input id="description" :label="__('settings.backup_page.description')" />
    <x-forms.button type="submit">
        {{ __('button.continue') }}
    </x-forms.button>
</form>
