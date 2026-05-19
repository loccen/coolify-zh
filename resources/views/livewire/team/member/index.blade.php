<div>
    <x-slot:title>
        {{ __('team.members_page_title') }} | Coolify
    </x-slot>
    <x-team.navbar />
    <h2>{{ __('team.members') }}</h2>
    <div class="subtitle">
        {{ __('team.members_subtitle') }}
    </div>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('team.table.name') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('team.table.email') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('team.table.role') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('team.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (currentTeam()->members as $member)
                                    <livewire:team.member :member="$member" :wire:key="$member->id" />
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('manageInvitations', currentTeam())
        <div class="py-4">
            @if (is_transactional_emails_enabled())
                <h2 class="pb-4">{{ __('team.invite_new_member') }}</h2>
            @else
                <h2>{{ __('team.invite_new_member') }}</h2>
                @if (isInstanceAdmin())
                    <div class="pb-4 text-xs dark:text-warning">{!! __('team.transactional_email_required', ['link' => '<a '.wireNavigate().' href="/settings/email" class="underline dark:text-warning">'.__('team.transactional_email_link').'</a>']) !!}</div>
                @endif
            @endif
            <livewire:team.invite-link />
        </div>
        <livewire:team.invitations :invitations="$invitations" />
    @endcan
</div>
