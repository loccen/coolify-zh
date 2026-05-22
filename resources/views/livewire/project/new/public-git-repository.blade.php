<div x-data x-init="$nextTick(() => { if ($refs.autofocusInput) $refs.autofocusInput.focus(); })">
    <h1>{{ __('Create a new Application') }}</h1>
    <div class="pb-8">{{ __('Deploy any public Git repositories.') }}</div>

    <!-- Repository URL Form -->
    <form class="flex flex-col gap-2" wire:submit='loadBranch'>
        <div class="flex flex-col gap-2">
            <div class="flex gap-2 items-end">
                <x-forms.input required id="repository_url" label="{{ __('Repository URL (https://)') }}"
                    helper="{!! __('repository.url') !!}" autofocus />
                <x-forms.button type="submit">
                    {{ __('Check repository') }}
                </x-forms.button>
            </div>
            <div>
                {!! __('For example application deployments, check out <a class="underline dark:text-white" href=":url" target="_blank">Coolify Examples</a>.', ['url' => 'https://github.com/coollabsio/coolify-examples/']) !!}
            </div>
        </div>
    </form>

    @if ($branchFound)
        @if ($rate_limit_remaining && $rate_limit_reset)
            <div class="flex gap-2 py-2">
                <div>{{ __('Rate Limit') }}</div>
                <x-helper
                    helper="{{ __('Rate limit remaining: :remaining<br>Rate limit reset at: :reset UTC', ['remaining' => $rate_limit_remaining, 'reset' => $rate_limit_reset]) }}" />
            </div>
        @endif

        <!-- Application Configuration Form -->
        <form class="flex flex-col gap-2 pt-4" wire:submit='submit'>
            <div class="flex flex-col gap-2 pb-6">
                <div class="flex gap-2">
                    @if ($git_source === 'other')
                        <x-forms.input id="git_branch" label="{{ __('Branch') }}"
                            helper="{{ __('You can select other branches after configuration is done.') }}" />
                    @else
                        <x-forms.input disabled id="git_branch" label="{{ __('Branch') }}"
                            helper="{{ __('You can select other branches after configuration is done.') }}" />
                    @endif
                    <x-forms.select wire:model.live="build_pack" label="{{ __('Build Pack') }}" required>
                        <option value="nixpacks">Nixpacks</option>
                        <option value="railpack">Railpack (Beta)</option>
                        <option value="static">Static</option>
                        <option value="dockerfile">Dockerfile</option>
                        <option value="dockercompose">Docker Compose</option>
                    </x-forms.select>
                    @if ($isStatic)
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
                    <x-forms.input type="number" id="port" label="{{ __('Port') }}" :readonly="$isStatic || $build_pack === 'static'"
                        helper="{{ __('The port your application listens on.') }}" />
                    <div class="w-64">
                        <x-forms.checkbox instantSave id="isStatic" label="{{ __('Is it a static site?') }}"
                            helper="{{ __('If your application is a static site or the final build assets should be served as a static site, enable this.') }}" />
                    </div>
                @endif
            </div>
            <x-forms.button type="submit">
                {{ __('Continue') }}
            </x-forms.button>
        </form>
    @endif
</div>
