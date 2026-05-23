<form wire:submit="submit">
    <div class="flex items-end gap-2">
        <x-forms.input id="filename" :label="__('Filename')" />
        <x-forms.button type="submit">{{ __('Save') }}</x-forms.button>
        <x-modal-confirmation :title="__('Confirm init-script deletion?')" :buttonTitle="__('Delete')" isErrorButton
            submitAction="delete" :actions="[
                __('The init-script of this database will be permanently deleted from the database and the server.'),
                __('If you are actively using this init-script, it could cause errors on redeployment.'),
            ]" confirmationText="{{ $filename }}"
            :confirmationLabel="__('Please confirm the execution of the actions by entering the init-script name below')"
            :shortConfirmationLabel="__('Init-script Name')" :confirmWithPassword=false :step2ButtonText="__('Permanently Delete')" />
    </div>
    <x-forms.textarea id="content" :label="__('Content')" />
</form>
