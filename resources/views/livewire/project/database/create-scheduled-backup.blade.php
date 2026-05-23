<form class="flex flex-col w-full gap-2 rounded-sm" wire:submit='submit'>
    <x-forms.input :placeholder="__('0 0 * * * or daily')" id="frequency"
        :helper="__('You can use every_minute, hourly, daily, weekly, monthly, yearly or a cron expression.')" :label="__('Frequency')"
        required />
    <h2>{{ __('S3') }}</h2>
    @if ($definedS3s->count() === 0)
        <div class="text-red-500">{{ __('No validated S3 Storages found.') }}</div>
    @else
        <x-forms.checkbox wire:model.live="saveToS3" :label="__('Save to S3')" />
        @if ($saveToS3)
            <x-forms.select id="s3StorageId" :label="__('Select a S3 Storage')">
                @foreach ($definedS3s as $s3)
                    <option value="{{ $s3->id }}">{{ $s3->name }}</option>
                @endforeach
            </x-forms.select>
        @endif
    @endif
    <x-forms.button type="submit" @click="modalOpen=false">
        {{ __('Save') }}
    </x-forms.button>
</form>
