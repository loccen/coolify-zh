<div>
    <h2>{{ __('Danger Zone') }}</h2>
    <div class="">{{ __('Woah. I hope you know what are you doing.') }}</div>
    <h4 class="pt-4">{{ __('Delete Resource') }}</h4>
    <div class="pb-4">{{ __('This will stop your containers, delete all related data, etc. Beware! There is no coming back!') }}
    </div>

    @if ($canDelete)
        <x-modal-confirmation :title="__('Confirm Resource Deletion?')" :buttonTitle="__('Delete')" isErrorButton submitAction="delete"
            :checkboxes="$checkboxes" :actions="[__('Permanently delete all containers of this resource.')]" confirmationText="{{ $resourceName }}"
            :confirmationLabel="__('Please confirm the execution of the actions by entering the Resource Name below')"
            :shortConfirmationLabel="__('Resource Name')" />
    @else
        <x-callout type="danger" :title="__('Insufficient Permissions')">
            {{ __("You don't have permission to delete this resource. Contact your team administrator for access.") }}
        </x-callout>
    @endif
</div>
