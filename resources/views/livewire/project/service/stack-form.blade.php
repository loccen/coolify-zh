<form wire:submit.prevent='submit' class="flex flex-col gap-4 pb-2">
    <div>
        <div class="flex gap-2">
            <h2>{{ __('Service Stack') }}</h2>
            @if (isDev())
                <div>{{ $service->compose_parsing_version }}</div>
            @endif
            <x-forms.button canGate="update" :canResource="$service" wire:target='submit'
                type="submit">{{ __('Save') }}</x-forms.button>
            @can('update', $service)
                <x-modal-input :buttonTitle="__('Edit Compose File')" :title="__('Edit Docker Compose')" :closeOutside="false">
                    <livewire:project.service.edit-compose serviceId="{{ $service->id }}" />
                </x-modal-input>
            @endcan
        </div>
        <div>{{ __('Configuration') }}</div>
    </div>
    <div class="flex gap-2">
        <x-forms.input canGate="update" :canResource="$service" id="name" required :label="__('Service Name')"
            :placeholder="__('My super WordPress site')" />
        <x-forms.input canGate="update" :canResource="$service" id="description" :label="__('Description')" />
    </div>
    <div>
        <h3>{{ __('Network') }}</h3>
    </div>
    <div class="w-full sm:w-96">
        <x-forms.checkbox canGate="update" :canResource="$service" instantSave id="connectToDockerNetwork"
            :label="__('Connect To Predefined Network')"
            :helper="__('By default, you do not reach the Coolify defined networks.<br>Starting a docker compose based resource will have an internal network. <br>If you connect to a Coolify defined network, you maybe need to use different internal DNS names to connect to a resource.<br><br>For more information, check <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://coolify.io/docs/knowledge-base/docker/compose#connect-to-predefined-networks\'>this</a>.')" />
    </div>
    @if ($fields->count() > 0)
        <div>
            <h3>{{ __('Service Specific Configuration') }}</h3>
        </div>
        <div class="grid grid-cols-2 gap-2">
            @foreach ($fields as $serviceName => $field)
                <div class="flex items-center gap-2"><span
                        class="font-bold">{{ data_get($field, 'serviceName') }}</span>{{ data_get($field, 'name') }}
                    @if (data_get($field, 'customHelper'))
                        <x-helper helper="{{ data_get($field, 'customHelper') }}" />
                    @else
                        <x-helper helper="{{ __('Variable name: :name', ['name' => $serviceName]) }}" />
                    @endif
                </div>
                <x-forms.input canGate="update" :canResource="$service"
                    type="{{ data_get($field, 'isPassword') ? 'password' : 'text' }}"
                    required="{{ str(data_get($field, 'rules'))?->contains('required') }}"
                    id="fields.{{ $serviceName }}.value"></x-forms.input>
            @endforeach
        </div>
    @endif
</form>
