<div>
    <x-slot:title>
        {{ data_get_str($database, 'name')->limit(10) }} > {{ __('Backups') }} | Coolify
    </x-slot>
    <h1>{{ __('Backups') }}</h1>
    <livewire:project.shared.configuration-checker :resource="$database" />
    <livewire:project.database.heading :database="$database" />
    <div>
        <div class="flex gap-2">
            <h2 class="pb-4">{{ __('Scheduled Backups') }}</h2>
            @can('update', $database)
                <x-modal-input :buttonTitle="'+ ' . __('Add')" :title="__('New Scheduled Backup')">
                    <livewire:project.database.create-scheduled-backup :database="$database" />
                </x-modal-input>
            @endcan
        </div>
        <livewire:project.database.scheduled-backups :database="$database" />
    </div>
</div>
