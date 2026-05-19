<form wire:submit='save' class="flex flex-col gap-4 w-full">
    <x-forms.input id="name" :label="__('Script Name')" :helper="__('A descriptive name for this cloud-init script.')" required />

    <x-forms.textarea id="script" :label="__('Script Content')" rows="12"
        :helper="__('Enter your cloud-init script. Supports cloud-config YAML format.')" required />

    <div class="flex justify-end gap-2">
        @if ($modal_mode)
            <x-forms.button type="button" @click="$dispatch('closeModal')">
                {{ __('Cancel') }}
            </x-forms.button>
        @endif
        <x-forms.button type="submit" isHighlighted>
            {{ $scriptId ? __('Update Script') : __('Create Script') }}
        </x-forms.button>
    </div>
</form>
