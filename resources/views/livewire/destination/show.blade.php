<div>
    <form class="flex flex-col">
        <div class="flex items-center gap-2">
            <h1>{{ __('Destination') }}</h1>
            <x-forms.button canGate="update" :canResource="$destination" wire:click.prevent='submit'
                type="submit">{{ __('Save') }}</x-forms.button>
            @if ($network !== 'coolify')
                <x-modal-confirmation title="{{ __('Confirm Destination Deletion?') }}" buttonTitle="{{ __('Delete Destination') }}" isErrorButton
                    submitAction="delete" :actions="[__('This will delete the selected destination/network.')]" confirmationText="{{ $destination->name }}"
                    confirmationLabel="{{ __('Please confirm the execution of the actions by entering the Destination Name below') }}"
                    shortConfirmationLabel="{{ __('Destination Name') }}" :confirmWithPassword="false" step2ButtonText="{{ __('Permanently Delete') }}"
                    canGate="delete" :canResource="$destination" />
            @endif
        </div>

        @if ($destination->getMorphClass() === 'App\Models\StandaloneDocker')
            <div class="subtitle ">{{ __('destination.subtitle.simple') }}</div>
        @else
            <div class="subtitle flex items-center gap-2">{{ __('destination.subtitle.swarm') }}
                <x-deprecated-badge />
            </div>
        @endif
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$destination" id="name" :label="__('destination.fields.name')" />
            <x-forms.input id="serverIp" :label="__('destination.fields.server_ip')" readonly />
            @if ($destination->getMorphClass() === 'App\Models\StandaloneDocker')
                <x-forms.input id="network" :label="__('destination.fields.docker_network')" readonly />
            @endif
        </div>
    </form>
</div>
