<div x-init="$wire.loadPublicKey()">
    <x-slot:title>
        {{ __('Private Key') }} | Coolify
    </x-slot>
    <x-security.navbar />
    <div x-data="{ showPrivateKey: false }">
        <form class="flex flex-col" wire:submit='changePrivateKey'>
            <div class="flex items-start gap-2">
                <h2 class="pb-4">{{ __('Private Key') }}</h2>
                <x-forms.button canGate="update" :canResource="$private_key" type="submit">
                    {{ __('Save') }}
                </x-forms.button>
                @if (data_get($private_key, 'id') > 0)
                    @can('delete', $private_key)
                        <x-modal-confirmation :title="__('Confirm Private Key Deletion?')" isErrorButton :buttonTitle="__('Delete')"
                            submitAction="delete({{ $private_key->id }})" :actions="[
                                __('This private key will be permanently deleted.'),
                                __('All servers connected to this private key will stop working.'),
                                __('Any Git app using this private key will stop working.'),
                            ]"
                            confirmationText="{{ $private_key->name }}"
                            :confirmationLabel="__('Please confirm the execution of the actions by entering the Private Key Name below')"
                            :shortConfirmationLabel="__('Private Key Name')" :confirmWithPassword="false"
                            :step2ButtonText="__('Delete Private Key')" />
                    @endcan
                @endif
            </div>
            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <x-forms.input canGate="update" :canResource="$private_key" id="name" :label="__('Name')" required />
                    <x-forms.input canGate="update" :canResource="$private_key" id="description" :label="__('Description')" />
                </div>
                <div>
                    <div class="flex items-end gap-2 py-2 ">
                        <div class="pl-1">{{ __('Public Key') }}</div>
                    </div>
                    <x-forms.input canGate="update" :canResource="$private_key" readonly id="public_key" />
                    <div class="flex items-end gap-2 py-2 ">
                        <div class="pl-1">{{ __('Private Key') }} <span class='text-helper'>*</span></div>
                        <div class="text-xs underline cursor-pointer dark:text-white" x-cloak x-show="!showPrivateKey"
                            x-on:click="showPrivateKey = true">
                            {{ __('Edit') }}
                        </div>
                        <div class="text-xs underline cursor-pointer dark:text-white" x-cloak x-show="showPrivateKey"
                            x-on:click="showPrivateKey = false">
                            {{ __('Hide') }}
                        </div>
                    </div>
                    @if ($isGitRelated)
                        <div class="w-48">
                            <x-forms.checkbox id="isGitRelated" disabled :label="__('Is used by a Git App?')" />
                        </div>
                    @endif
                    <div x-cloak x-show="!showPrivateKey">
                        <x-forms.input canGate="update" :canResource="$private_key" allowToPeak="false" type="password" rows="10" id="privateKeyValue"
                            required disabled />
                    </div>
                    <div x-cloak x-show="showPrivateKey">
                        <x-forms.textarea canGate="update" :canResource="$private_key" rows="10" id="privateKeyValue" required monospace />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
