<div>
    <form wire:submit="submit" class="w-full">
        <div class="flex flex-col gap-2 pb-2">
            <div class="flex gap-2 items-end">
                <h2>{{ __('Task: :name', ['name' => $task->name]) }}</h2>
                <x-forms.button type="submit">
                    {{ __('Save') }}
                </x-forms.button>
                @if ($resource->isRunning())
                    <x-forms.button type="button" wire:click="executeNow">
                        {{ __('Execute Now') }}
                    </x-forms.button>
                @endif
                @if (!$isEnabled)
                    <x-forms.button wire:click="toggleEnabled" isHighlighted>{{ __('Enable Task') }}</x-forms.button>
                @else
                    <x-forms.button wire:click="toggleEnabled">{{ __('Disable Task') }}</x-forms.button>
                @endif
                <x-modal-confirmation :title="__('Confirm Scheduled Task Deletion?')" isErrorButton :buttonTitle="__('Delete')"
                    submitAction="delete({{ $task->id }})" :actions="[__('The selected scheduled task will be permanently deleted.')]" confirmationText="{{ $task->name }}"
                    :confirmationLabel="__('Please confirm the execution of the actions by entering the Scheduled Task Name below')"
                    :shortConfirmationLabel="__('Scheduled Task Name')" :confirmWithPassword="false"
                    :step2ButtonText="__('Permanently Delete')" />

            </div>
            <h3 class="pt-4">{{ __('Configuration') }}</h3>
            <div class="flex gap-2 w-full">
                <x-forms.input :placeholder="__('Name')" id="name" :label="__('Name')" required />
                <x-forms.input :placeholder="__('0 0 * * * or daily')" id="frequency" :label="__('Frequency')"
                    :helper="__('You can use every_minute, hourly, daily, weekly, monthly, yearly or a cron expression.')" required />
                <x-forms.input type="number" placeholder="300" id="timeout"
                    :helper="__('Maximum execution time in seconds (60-36000).')" :label="__('Timeout (seconds)')" required />
                @if ($type === 'application')
                    <x-forms.input placeholder="php"
                        :helper="__('You can leave this empty if your resource only has one container.')" id="container"
                        :label="__('Container name')" />
                @elseif ($type === 'service')
                    <x-forms.input placeholder="php"
                        :helper="__('You can leave this empty if your resource only has one service in your stack. Otherwise use the stack name, without the random generated ID. So if you have a mysql service in your stack, use mysql.')"
                        id="container" :label="__('Service name')" />
                @endif
            </div>
            <x-forms.input placeholder="php artisan schedule:run" id="command" :label="__('Command')" required />
    </form>

    <div class="pt-4">
        <h3 class="py-4">{{ __('Recent executions') }} <span class="text-xs text-neutral-500">{{ __('(click to check output)') }}</span></h3>
        <livewire:project.shared.scheduled-task.executions :taskId="$task->id" />
    </div>
</div>
