<div class="w-full px-2">
    <x-modal-confirmation buttonFullWidth :title="__('team.delete_team_modal_title')" :buttonTitle="__('team.delete_team_modal_button')" isErrorButton
        submitAction="delete" :actions="__('team.delete_team_modal_actions')" confirmationText="{{ $team }}"
        :confirmationLabel="__('team.delete_team_modal_confirmation')"
        :shortConfirmationLabel="__('team.delete_team_modal_confirmation_short')" />
</div>
