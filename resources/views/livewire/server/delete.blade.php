<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Delete Server') }} | Coolify
    </x-slot>
    <livewire:server.navbar :server="$server" />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-server.sidebar :server="$server" activeMenu="danger" />
        <div class="w-full">
            @if ($server->id !== 0)
                <h2>{{ __('Danger Zone') }}</h2>
                <div class="">{{ __('Proceed carefully. This action is irreversible.') }}</div>
                <h4 class="pt-4">{{ __('Delete Server') }}</h4>
                <div class="pb-4">{{ __('This will remove this server from Coolify. There is no undo.') }}
                </div>
                @if ($server->definedResources()->count() > 0)
                    <div class="pb-2 text-red-500">{{ __('This server has resources. You can force delete all resources by checking the option below.') }}</div>
                @endif

                <x-modal-confirmation :title="__('Confirm Server Deletion?')" isErrorButton :buttonTitle="__('Delete')"
                    submitAction="delete"
                    :actions="[__('This server will be permanently deleted from Coolify.')]"
                    :checkboxes="$checkboxes"
                    confirmationText="{{ $server->name }}"
                    :confirmationLabel="__('Please confirm the execution of the actions by entering the Server Name below')"
                    :shortConfirmationLabel="__('Server Name')" />
            @endif
        </div>
    </div>
</div>
