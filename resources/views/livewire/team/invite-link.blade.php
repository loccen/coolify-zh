@can('manageInvitations', currentTeam())
    <form wire:submit='viaLink' class="flex gap-2 flex-col lg:flex-row items-end">
        <div class="flex flex-1 lg:w-fit w-full gap-2">
            <x-forms.input id="email" type="email" :label="__('team.table.email')" name="email" :placeholder="__('team.table.email')" required />
            <x-forms.select id="role" name="role" :label="__('team.table.role')">
                @if (auth()->user()->role() === 'owner')
                    <option value="owner">{{ __('team.roles.owner') }}</option>
                @endif
                <option value="admin">{{ __('team.roles.admin') }}</option>
                <option value="member">{{ __('team.roles.member') }}</option>
            </x-forms.select>
        </div>
        <div class="flex gap-2 lg:w-fit w-full">
            <x-forms.button type="submit">{{ __('team.generate_invitation_link') }}</x-forms.button>
            @if (is_transactional_emails_enabled())
                <x-forms.button wire:click.prevent='viaEmail'>{{ __('team.send_invitation_email') }}</x-forms.button>
            @endif
        </div>
    </form>
@endcan
