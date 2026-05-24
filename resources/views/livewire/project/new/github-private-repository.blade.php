<div>
    <div class="flex items-end gap-2">
        <h1>{{ __('Create a new Application') }}</h1>
        <x-modal-input buttonTitle="{{ __('+ Add GitHub App') }}" title="{{ __('New GitHub App') }}" closeOutside="false">
            <livewire:source.github.create />
        </x-modal-input>
        @if ($repositories->count() > 0)
            <x-forms.button wire:click.prevent="loadRepositories({{ $github_app->id }})">
                {{ __('Refresh Repository List') }}
            </x-forms.button>
            <a target="_blank" class="inline-flex items-center self-center gap-1 text-sm hover:underline dark:text-neutral-400"
                href="{{ getInstallationPath($github_app) }}">
                {{ __('Change Repositories on GitHub') }}
                <x-external-link />
            </a>
        @endif
    </div>
    <div class="pb-4">{{ __('Deploy any public or private Git repositories through a GitHub App.') }}</div>
    @if ($github_apps->count() !== 0)
        <div class="flex flex-col gap-2">
            @if ($current_step === 'github_apps')
                <h2 class="pt-4 pb-4">{{ __('Select a Github App') }}</h2>
                <div class="flex flex-col justify-center gap-2 text-left">
                    @foreach ($github_apps as $ghapp)
                        <div class="flex">
                            <div class="w-full gap-2 py-4 group coolbox"
                                wire:click.prevent="loadRepositories({{ $ghapp->id }})"
                                wire:key="{{ $ghapp->id }}">
                                <div class="flex mr-4">
                                    <div class="flex flex-col mx-6">
                                        <div class="box-title">
                                            {{ data_get($ghapp, 'name') }}
                                        </div>
                                        <div class="box-description">
                                            {{ data_get($ghapp, 'html_url') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <x-loading wire:loading wire:target="loadRepositories({{ $ghapp->id }})" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($current_step === 'repository')
                @if ($repositories->count() > 0)
                    <div class="flex flex-col gap-2 pb-6">
                        <div class="flex gap-2">
                            <x-forms.datalist class="w-full" label="{{ __('Repository') }}" placeholder="{{ __('Search repositories...') }}" wire:model.live="selected_repository_id">
                                @foreach ($repositories as $repo)
                                    <option value="{{ data_get($repo, 'id') }}">{{ data_get($repo, 'name') }}</option>
                                @endforeach
                            </x-forms.datalist>
                        </div>
                        <x-forms.button :showLoadingIndicator="false" wire:click.prevent="loadBranches" wire:target="loadBranches, selected_repository_id">
                            {{ __('Load Repository') }}
                            <x-loading-on-button wire:loading.delay wire:target="loadBranches, selected_repository_id" />
                        </x-forms.button>
                    </div>
                @else
                    <div>{{ __('No repositories found. Check your GitHub App configuration.') }}</div>
                @endif
                @if ($branches->count() > 0)
                    <h2 class="text-lg font-bold">{{ __('Configuration') }}</h2>
                    <div class="flex flex-col gap-2 pb-6">
                        <form class="flex flex-col" wire:submit='submit'>
                            <div class="flex flex-col gap-2 pb-6">
                                <div class="flex gap-2">
                                    <x-forms.select id="selected_branch_name" label="{{ __('Branch') }}">
                                        <option value="default" disabled selected>{{ __('Select a branch') }}</option>
                                        @foreach ($branches as $branch)
                                            @if ($loop->first)
                                                <option selected value="{{ data_get($branch, 'name') }}">
                                                    {{ data_get($branch, 'name') }}
                                                </option>
                                            @else
                                                <option value="{{ data_get($branch, 'name') }}">
                                                    {{ data_get($branch, 'name') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </x-forms.select>
                                    <x-forms.select wire:model.live="build_pack" label="{{ __('Build Pack') }}" required>
                                        <option value="nixpacks">Nixpacks</option>
                                        <option value="railpack">Railpack (Beta)</option>
                                        <option value="static">{{ __('Static') }}</option>
                                        <option value="dockerfile">Dockerfile</option>
                                        <option value="dockercompose">Docker Compose</option>
                                    </x-forms.select>
                                    @if ($is_static)
                                        <x-forms.input id="publish_directory" label="{{ __('Publish Directory') }}"
                                            helper="{{ __('If there is a build process involved (like Svelte, React, Next, etc..), please specify the output directory for the build assets.') }}" />
                                    @endif
                                </div>
                                @if ($build_pack === 'dockercompose')
                                    <div x-data="{
                                        baseDir: '{{ $base_directory }}',
                                        composeLocation: '{{ $docker_compose_location }}',
                                        normalizePath(path) {
                                            if (!path || path.trim() === '') return '/';
                                            path = path.trim();
                                            // Remove trailing slashes
                                            path = path.replace(/\/+$/, '');
                                            // Ensure leading slash
                                            if (!path.startsWith('/')) {
                                                path = '/' + path;
                                            }
                                            return path;
                                        },
                                        normalizeBaseDir() {
                                            this.baseDir = this.normalizePath(this.baseDir);
                                        },
                                        normalizeComposeLocation() {
                                            this.composeLocation = this.normalizePath(this.composeLocation);
                                        }
                                    }" class="gap-2 flex flex-col">
                                        <x-forms.input placeholder="/" wire:model.defer="base_directory"
                                            label="{{ __('Base Directory') }}"
                                            helper="{{ __('Directory to use as root. Useful for monorepos.') }}" x-model="baseDir"
                                            @blur="normalizeBaseDir()" />
                                        <x-forms.input placeholder="/docker-compose.yaml"
                                            wire:model.defer="docker_compose_location" label="{{ __('Docker Compose Location') }}"
                                            helper="{{ __('It is calculated together with the Base Directory.') }}"
                                            x-model="composeLocation" @blur="normalizeComposeLocation()" />
                                        <div class="pt-2">
                                            <span>
                                                {{ __('Compose file location in your repository:') }} </span><span
                                                class='dark:text-warning'
                                                x-text='(baseDir === "/" ? "" : baseDir) + (composeLocation.startsWith("/") ? composeLocation : "/" + composeLocation)'></span>
                                        </div>
                                    </div>
                                @else
                                    <x-forms.input wire:model="base_directory" label="{{ __('Base Directory') }}"
                                        helper="{{ __('Directory to use as root. Useful for monorepos.') }}" />
                                @endif
                                @if ($show_is_static)
                                    <x-forms.input type="number" id="port" label="{{ __('Port') }}" :readonly="$is_static || $build_pack === 'static'"
                                        helper="{{ __('The port your application listens on.') }}" />
                                    <div class="w-52">
                                        <x-forms.checkbox instantSave id="is_static" label="{{ __('Is it a static site?') }}"
                                            helper="{{ __('If your application is a static site or the final build assets should be served as a static site, enable this.') }}" />
                                    </div>
                                @endif
                            </div>
                            <x-forms.button type="submit">
                                {{ __('Continue') }}
                            </x-forms.button>
                @endif
            @endif
        </div>
    @else
        <div class="hero">
            {{ __('No GitHub Application found. Please create a new GitHub Application.') }}
        </div>
    @endif
</div>
