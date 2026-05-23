<form class="flex flex-col w-full gap-2 rounded-sm" wire:submit='submit'>
    <x-forms.input :placeholder="__('Run cron')" id="name" :label="__('Name')" />
    <x-forms.input placeholder="php artisan schedule:run" id="command" :label="__('Command')" />
    <x-forms.input :placeholder="__('0 0 * * * or daily')"
        :helper="__('You can use every_minute, hourly, daily, weekly, monthly, yearly or a cron expression.')" id="frequency"
        :label="__('Frequency')" />
    <x-forms.input type="number" placeholder="300" id="timeout"
        :helper="__('Maximum execution time in seconds (60-36000). Default is 300 seconds (5 minutes).')"
        :label="__('Timeout (seconds)')" />
    @if ($type === 'application')
        @if ($containerNames->count() > 1)
            <x-forms.select id="container" :label="__('Container name')">
                @foreach ($containerNames as $containerName)
                    <option value="{{ $containerName }}">{{ $containerName }}</option>
                @endforeach
            </x-forms.select>
        @else
            <x-forms.input placeholder="php" id="container"
                :helper="__('You can leave this empty if your resource only has one container.')" :label="__('Container name')" />
        @endif
    @elseif ($type === 'service')
        <x-forms.select id="container" :label="__('Container name')">
            @foreach ($containerNames as $containerName)
                <option value="{{ $containerName }}">{{ $containerName }}</option>
            @endforeach
        </x-forms.select>
    @endif

    <x-forms.button @click="modalOpen=false" type="submit">
        {{ __('Save') }}
    </x-forms.button>
</form>
