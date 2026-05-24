<div>
    <form wire:submit='submit' class="flex flex-col items-center gap-4 p-4 bg-white border lg:items-start dark:bg-base dark:border-coolgray-300 border-neutral-200">
        @if ($isReadOnly)
            @if (!$storage->isServiceResource() && !$storage->isDockerComposeResource())
                <div class="w-full p-2 text-sm rounded bg-warning/10 text-warning">
                    {{ __('This volume is mounted as read-only and cannot be modified from the UI.') }}
                </div>
            @endif
            @if ($isFirst)
                <div class="flex gap-2 items-end w-full  md:flex-row flex-col">
                    @if (
                        $storage->resource_type === 'App\Models\ServiceApplication' ||
                            $storage->resource_type === 'App\Models\ServiceDatabase')
                        <x-forms.input id="name" :label="__('Volume Name')" required readonly
                            :helper="__('Warning: Changing the volume name after the initial start could cause problems. Only use it when you know what are you doing.')" />
                    @else
                        <x-forms.input id="name" :label="__('Volume Name')" required readonly
                            :helper="__('Warning: Changing the volume name after the initial start could cause problems. Only use it when you know what are you doing.')" />
                    @endif
                    @if ($isService || $startedAt)
                        <x-forms.input id="hostPath" readonly :helper="__('Warning: Changing the source path after the initial start could cause problems. Only use it when you know what are you doing.')"
                            :label="__('Source Path')" />
                        <x-forms.input id="mountPath" :label="__('Destination Path')"
                            :helper="__('Directory inside the container.')" required readonly />
                    @else
                        <x-forms.input id="hostPath" readonly :helper="__('Warning: Changing the source path after the initial start could cause problems. Only use it when you know what are you doing.')"
                            :label="__('Source Path')" />
                        <x-forms.input id="mountPath" :label="__('Destination Path')"
                            :helper="__('Directory inside the container.')" required readonly />
                    @endif
                </div>
            @else
                <div class="flex gap-2 items-end w-full">
                    <x-forms.input id="name" required readonly />
                    <x-forms.input id="hostPath" readonly />
                    <x-forms.input id="mountPath" required readonly />
                </div>
            @endif
            @if (!$isService)
                @can('update', $resource)
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox instantSave canGate="update" :canResource="$resource" :label="__('Add suffix for PR deployments')"
                            id="isPreviewSuffixEnabled"
                            :helper="__('When enabled, a -pr-N suffix is added to this volume\'s name for preview deployments (e.g. myvolume becomes myvolume-pr-1). Disable this for volumes that should be shared between the main and preview deployments.')"></x-forms.checkbox>
                    </div>
                @endcan
            @endif
        @else
            @can('update', $resource)
                @if ($isFirst)
                    <div class="flex gap-2 items-end w-full">
                        <x-forms.input id="name" :label="__('Volume Name')" required />
                        <x-forms.input id="hostPath" :helper="__('Directory on the host system.')" :label="__('Source Path')" />
                        <x-forms.input id="mountPath" :label="__('Destination Path')"
                            :helper="__('Directory inside the container.')" required />
                    </div>
                @else
                    <div class="flex gap-2 items-end w-full">
                        <x-forms.input id="name" required />
                        <x-forms.input id="hostPath" />
                        <x-forms.input id="mountPath" required />
                    </div>
                @endif
                @if (!$isService)
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox instantSave canGate="update" :canResource="$resource" :label="__('Add suffix for PR deployments')"
                            id="isPreviewSuffixEnabled"
                            :helper="__('When enabled, a -pr-N suffix is added to this volume\'s name for preview deployments (e.g. myvolume becomes myvolume-pr-1). Disable this for volumes that should be shared between the main and preview deployments.')"></x-forms.checkbox>
                    </div>
                @endif
                <div class="flex gap-2">
                    <x-forms.button type="submit">
                        {{ __('Update') }}
                    </x-forms.button>
                    <x-modal-confirmation :title="__('Confirm persistent storage deletion?')" isErrorButton :buttonTitle="__('Delete')"
                        submitAction="delete" :actions="[
                            __('The selected persistent storage/volume will be permanently deleted.'),
                            __('If the persistent storage/volume is actvily used by a resource data will be lost.'),
                        ]" confirmationText="{{ $storage->name }}"
                        :confirmationLabel="__('Please confirm the execution of the actions by entering the Storage Name below')"
                        :shortConfirmationLabel="__('Storage Name')" />
                </div>
            @else
                @if ($isFirst)
                    <div class="flex gap-2 items-end w-full">
                        <x-forms.input id="name" :label="__('Volume Name')" required disabled />
                        <x-forms.input id="hostPath" :helper="__('Directory on the host system.')" :label="__('Source Path')"
                            disabled />
                        <x-forms.input id="mountPath" :label="__('Destination Path')"
                            :helper="__('Directory inside the container.')" required disabled />
                    </div>
                @else
                    <div class="flex gap-2 items-end w-full">
                        <x-forms.input id="name" required disabled />
                        <x-forms.input id="hostPath" disabled />
                        <x-forms.input id="mountPath" required disabled />
                    </div>
                @endif
            @endcan
        @endif
    </form>
</div>
