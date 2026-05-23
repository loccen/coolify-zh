<div>
    <h1>{{ __('Create a new Application') }}</h1>
    <div class="pb-4">{{ __('Deploy any public or private Git repositories through a Deploy Key.') }}</div>
    <div class="flex flex-col ">
        @if ($current_step === 'private_keys')
            <h2 class="pb-4">{{ __('Select a private key') }}</h2>
            <div class="flex flex-col justify-center gap-2 text-left ">
                @forelse ($private_keys as $key)
                    @if ($private_key_id == $key->id)
                        <div class="gap-2 py-4 cursor-pointer group coolbox"
                            wire:click="setPrivateKey('{{ $key->id }}')" wire:key="{{ $key->id }}">
                            <div class="flex flex-col mx-6">
                                <div class="box-title">
                                    {{ $key->name }}
                                </div>
                                <div class="box-description">
                                    {{ $key->description }}</div>
                                <span wire:target="loadRepositories" wire:loading.delay
                                    class="loading loading-xs dark:text-warning loading-spinner"></span>
                            </div>
                        </div>
                    @else
                        <div class="gap-2 py-4 cursor-pointer group coolbox"
                            wire:click="setPrivateKey('{{ $key->id }}')" wire:key="{{ $key->id }}">
                            <div class="flex flex-col mx-6">
                                <div class="box-title">
                                    {{ $key->name }}
                                </div>
                                <div class="box-description">
                                    {{ $key->description }}</div>
                                <span wire:target="loadRepositories" wire:loading.delay
                                    class="loading loading-xs dark:text-warning loading-spinner"></span>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div>
                            {{ __('No private keys found.') }}
                        </div>
                        <a href="{{ route('security.private-key.index') }}" {{ wireNavigate() }}>
                            <x-forms.button>{{ __('Create a new private key') }}</x-forms.button>
                        </a>
                    </div>
                @endforelse
            </div>
        @endif
        @if ($current_step === 'repository')
            <form class="flex flex-col gap-2" wire:submit='submit'>
                <x-forms.input id="repository_url" required label="{{ __('Repository URL (https:// or git@)') }}" />
                <div class="flex gap-2">
                    <x-forms.input id="branch" required label="{{ __('Branch') }}" />
                    <x-forms.select wire:model.live="build_pack" label="{{ __('Build Pack') }}" required>
                        <option value="nixpacks">Nixpacks</option>
                        <option value="railpack">Railpack (Beta)</option>
                        <option value="static">{{ __('Static') }}</option>
                        <option value="dockerfile">Dockerfile</option>
                        <option value="dockercompose">Docker Compose</option>
                    </x-forms.select>
                    @if ($is_static)
                        <x-forms.input id="publish_directory" required label="{{ __('Publish Directory') }}" />
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
                        <x-forms.input placeholder="/" wire:model.defer="base_directory" label="{{ __('Base Directory') }}"
                            helper="{{ __('Directory to use as root. Useful for monorepos.') }}" x-model="baseDir"
                            @blur="normalizeBaseDir()" />
                        <x-forms.input placeholder="/docker-compose.yaml" wire:model.defer="docker_compose_location"
                            label="{{ __('Docker Compose Location') }}" helper="{{ __('It is calculated together with the Base Directory.') }}"
                            x-model="composeLocation" @blur="normalizeComposeLocation()" />
                        <div class="pt-2">
                            <span>
                                {{ __('Compose file location in your repository:') }} </span><span class='dark:text-warning'
                                x-text='(baseDir === "/" ? "" : baseDir) + (composeLocation.startsWith("/") ? composeLocation : "/" + composeLocation)'></span>
                        </div>
                    </div>
                @else
                    <x-forms.input wire:model="base_directory" label="{{ __('Base Directory') }}"
                        helper="{{ __('Directory to use as root. Useful for monorepos.') }}" />
                @endif
                @if ($show_is_static)
                    <x-forms.input type="number" required id="port" label="{{ __('Port') }}" :readonly="$is_static || $build_pack === 'static'" />
                    <div class="w-52">
                        <x-forms.checkbox instantSave id="is_static" label="{{ __('Is it a static site?') }}"
                            helper="{{ __('If your application is a static site or the final build assets should be served as a static site, enable this.') }}" />
                    </div>
                @endif
                <x-forms.button type="submit" class="mt-4">
                    {{ __('Continue') }}
                </x-forms.button>
            </form>
        @endif
    </div>
</div>
