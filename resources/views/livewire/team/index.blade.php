<div>
    <x-slot:title>
        {{ __('team.page_title') }} | Coolify
    </x-slot>
    <x-team.navbar />

    <form class="flex flex-col" wire:submit='submit'>
        <h2>{{ __('team.general') }}</h2>
        <div class="subtitle">
            {{ __('team.general_subtitle') }}
        </div>

        <div class="flex items-end gap-2 pb-6">
            <x-forms.input id="name" :label="__('input.name')" required canGate="update" :canResource="$team" />
            <x-forms.input id="description" :label="__('settings.backup_page.description')" canGate="update" :canResource="$team" />
            @can('update', $team)
                <x-forms.button type="submit">
                    {{ __('button.save') }}
                </x-forms.button>
            @endcan
        </div>
    </form>

    @can('delete', $team)
        <div>
            <h2>{{ __('team.danger_zone') }}</h2>
            <div class="pb-4">{{ __('team.danger_subtitle') }}</div>
            <h4 class="pb-4">{{ __('team.delete_team_heading') }}</h4>
            @if (session('currentTeam.id') === 0)
                <div>{{ __('team.default_team_immutable') }}</div>
            @elseif(auth()->user()->teams()->get()->count() === 1 || auth()->user()->currentTeam()->personal_team)
                <div>{{ __('team.last_team_immutable') }}</div>
            @elseif(currentTeam()->subscription)
                <div>{!! __('team.cancel_subscription_first', ['link' => '<a class="underline dark:text-white" '.wireNavigate().' href="'.route('subscription.show').'">'.__('team.subscription_link').'</a>']) !!}</div>
            @else
                @if (currentTeam()->isEmpty())
                    <div class="pb-4">{{ __('team.delete_team_warning') }}</div>
                    <x-modal-confirmation :title="__('team.delete_team_title')" :buttonTitle="__('team.delete_team_button')" isErrorButton
                        submitAction="delete({{ currentTeam()->id }})" :actions="__('team.delete_team_actions')"
                        confirmationText="{{ currentTeam()->name }}"
                        :confirmationLabel="__('team.delete_team_confirmation')"
                        :shortConfirmationLabel="__('team.delete_team_confirmation_short')" :confirmWithPassword="false" :step2ButtonText="__('team.delete_team_step2')" />
                @else
                    <div>
                        <div class="pb-4">{{ __('team.delete_team_requirements') }}</div>
                        @if (currentTeam()->projects()->count() > 0)
                            <h4 class="pb-4">{{ __('team.resources.projects') }}:</h4>
                            <ul class="pl-8 list-disc">
                                @foreach (currentTeam()->projects as $resource)
                                    <li>{{ $resource->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (currentTeam()->servers()->count() > 0)
                            <h4 class="py-4">{{ __('team.resources.servers') }}:</h4>
                            <ul class="pl-8 list-disc">
                                @foreach (currentTeam()->servers as $resource)
                                    <li>{{ $resource->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (currentTeam()->privateKeys()->count() > 0)
                            <h4 class="py-4">{{ __('team.resources.private_keys') }}:</h4>
                            <ul class="pl-8 list-disc">
                                @foreach (currentTeam()->privateKeys as $resource)
                                    <li>{{ $resource->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (currentTeam()->sources()->count() > 0)
                            <h4 class="py-4">{{ __('team.resources.sources') }}:</h4>
                            <ul class="pl-8 list-disc">
                                @foreach (currentTeam()->sources() as $resource)
                                    <li>{{ $resource->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    @endcan
</div>
