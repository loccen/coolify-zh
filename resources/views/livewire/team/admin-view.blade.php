<div>
    <x-slot:title>
        {{ __('team.admin_page_title') }} | Coolify
    </x-slot>
    <x-team.navbar />
    <h2>{{ __('team.admin_view') }}</h2>
    <div class="subtitle">
        {{ __('team.admin_subtitle') }}
    </div>
    <form wire:submit="submitSearch" class="flex flex-col gap-2 lg:flex-row">
        <x-forms.input wire:model="search" :placeholder="__('team.search_for_user')" />
        <x-forms.button type="submit">{{ __('team.search') }}</x-forms.button>
    </form>
    <h3 class="py-4">{{ __('team.users') }}</h3>
    <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
        @forelse ($users as $user)
            <div wire:key="user-{{ $user->id }}"
                class="flex items-center justify-center gap-2 bg-white box-without-bg dark:bg-coolgray-100">
                <div>{{ $user->name }}</div>
                <div>{{ $user->email }}</div>
                <div class="flex-1"></div>
                <div class="flex items-center justify-center gap-2 mx-4 text-xs font-bold ">
                    <x-modal-confirmation :title="__('team.delete_user_title')" :buttonTitle="__('team.delete_user_button')" isErrorButton
                        submitAction="delete({{ $user->id }})" :actions="__('team.delete_user_actions')"
                        confirmationText="{{ $user->name }}"
                        :confirmationLabel="__('team.delete_user_confirmation')"
                        :shortConfirmationLabel="__('team.delete_user_confirmation_short')" />
                </div>
            </div>
        @empty
            <div>{{ __('team.no_other_users') }}</div>
        @endforelse
        @if ($lots_of_users)
            <div>{{ __('team.more_users_hint') }}</div>
        @endif
    </div>
</div>
