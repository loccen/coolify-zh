<div>
    <h2>{{ __('Tags') }}</h2>
    @can('update', $resource)
        <form wire:submit='submit' class="flex items-end gap-2">
            <div class="w-64">
                <x-forms.input label="{{ __('Create new or assign existing tags') }}"
                    helper="{{ __('You can add multiple tags at once with a space separated list: web api something<br><br>If the tag does not exist, it will be created.') }}"
                    wire:model="newTags" placeholder="example: prod app1 user" />
            </div>
            <x-forms.button type="submit">{{ __('Add') }}</x-forms.button>
        </form>
    @else
        <x-callout type="warning" :title="__('Access Restricted')" class="mt-4">
            {{ __("You don't have permission to manage tags. Contact your team administrator to request access.") }}
        </x-callout>
    @endcan
    @if (data_get($this->resource, 'tags') && count(data_get($this->resource, 'tags')) > 0)
        <h3 class="pt-4">{{ __('Assigned Tags') }}</h3>
        <div class="flex flex-wrap gap-2 pt-4">
            @foreach (data_get($this->resource, 'tags') as $tagId => $tag)
                <div class="button">
                    {{ $tag->name }}
                    @can('update', $resource)
                        <svg wire:click="deleteTag('{{ $tag->id }}')" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24"
                            class="inline-block w-3 h-3 rounded-sm cursor-pointer stroke-current hover:bg-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    @endcan
                </div>
            @endforeach
        </div>
    @endif
    @can('update', $resource)
        @if (count($filteredTags) > 0)
            <h3 class="pt-4">{{ __('Existing Tags') }}</h3>
            <div>{{ __('Click to add quickly') }}</div>
            <div class="flex flex-wrap gap-2 pt-4">
                @foreach ($filteredTags as $tag)
                    <x-forms.button wire:click="addTag('{{ $tag->id }}','{{ $tag->name }}')">
                        {{ $tag->name }}</x-forms.button>
                @endforeach
            </div>
        @endif
    @endcan
</div>
