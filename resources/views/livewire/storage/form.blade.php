<div>
    <form class="flex flex-col gap-2 pb-6" wire:submit='submit'>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$storage" :label="__('storage.fields.name')" id="name" />
            <x-forms.input canGate="update" :canResource="$storage" :label="__('storage.fields.description')" id="description" />
        </div>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$storage" required :label="__('storage.fields.endpoint')" id="endpoint" />
            <x-forms.input canGate="update" :canResource="$storage" required :label="__('storage.fields.bucket')" id="bucket" />
            <x-forms.input canGate="update" :canResource="$storage" required :label="__('storage.fields.region')" id="region" />
        </div>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$storage" required type="password" :label="__('storage.fields.access_key')"
                id="key" />
            <x-forms.input canGate="update" :canResource="$storage" required type="password" :label="__('storage.fields.secret_key')"
                id="secret" />
        </div>
        @can('validateConnection', $storage)
            <x-forms.button class="mt-4" isHighlighted wire:click="testConnection">
                {{ __('storage.validate_connection') }}
            </x-forms.button>
        @endcan
    </form>
</div>
