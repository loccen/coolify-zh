<form>
    <x-slot:title>
        {{ data_get_str($project, 'name')->limit(10) }} > {{ __('Clone') }} | Coolify
    </x-slot>
    <div class="flex flex-col">
        <h1>{{ __('Clone') }}</h1>
        <div class="subtitle ">{{ __('Quickly clone all resources to a new project or environment.') }}</div>
    </div>
    <x-forms.input required id="newName" :label="__('New Name')" />
    <h3 class="pt-8 ">{{ __('Destination Server') }}</h3>
    <div class="pb-2">{{ __('Choose the server and network to clone the resources to.') }}</div>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('Server') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('Network') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($servers->sortBy('id') as $server)
                                    @foreach ($server->destinations() as $destination)
                                        <tr class="cursor-pointer hover:bg-coolgray-50 dark:hover:bg-coolgray-200"
                                            wire:click="selectServer('{{ $server->id }}', '{{ $destination->id }}')">
                                            <td class="px-5 py-4 text-sm whitespace-nowrap dark:text-white"
                                                :class="'{{ $selectedDestination === $destination->id }}' ?
                                                'bg-coollabs text-white' : 'dark:bg-coolgray-100 bg-white'">
                                                {{ $server->name }}</td>
                                            <td class="px-5 py-4 text-sm whitespace-nowrap dark:text-white "
                                                :class="'{{ $selectedDestination === $destination->id }}' ?
                                                'bg-coollabs text-white' : 'dark:bg-coolgray-100 bg-white'">
                                                {{ $destination->name }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="pt-8">{{ __('Resources') }}</h3>
    <div class="pb-2">{{ __('These will be cloned to the new project') }}</div>
    <div class="flex flex-col pt-4">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('Name') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('Type') }}</th>
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($environment->applications->sortBy('name') as $application)
                                    <tr>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap font-bold dark:text-white">
                                            {{ $application->name }}</td>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap dark:text-white">{{ __('Application') }}</td>
                                        <td class="px-5 py-4 text-sm dark:text-white">
                                            {{ $application->description ?: '-' }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($environment->databases()->sortBy('name') as $database)
                                    <tr>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap font-bold dark:text-white">
                                            {{ $database->name }}
                                        </td>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap dark:text-white">{{ __('Database') }}</td>
                                        <td class="px-5 py-4 text-sm dark:text-white">
                                            {{ $database->description ?: '-' }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($environment->services->sortBy('name') as $service)
                                    <tr>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap font-bold dark:text-white">
                                            {{ $service->name }}
                                        </td>
                                        <td class="px-5 py-4 text-sm whitespace-nowrap dark:text-white">{{ __('Service') }}</td>
                                        <td class="px-5 py-4 text-sm dark:text-white">
                                            {{ $service->description ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex gap-4 pt-4 w-full">
        <x-forms.button isHighlighted class="w-full" wire:click="clone('project')" :disabled="!filled($selectedDestination)">{{ __('Clone to new Project') }}</x-forms.button>
        <x-forms.button isHighlighted class="w-full" wire:click="clone('environment')" :disabled="!filled($selectedDestination)">{{ __('Clone to new Environment') }}</x-forms.button>
    </div>
</form>
