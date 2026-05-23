<div x-data="{
    initLoadingCompose: $wire.entangle('initLoadingCompose'),
    canUpdate: @js(auth()->user()->can('update', $application)),
    shouldDisable() {
        return this.initLoadingCompose || !this.canUpdate;
    }
}">
    <form wire:submit='submit' class="flex flex-col pb-32">
        <div class="flex items-center gap-2">
            <h2>{{ __('General') }}</h2>
            @if (isDev())
                <div>{{ $application->compose_parsing_version }}</div>
            @endif
            <x-forms.button canGate="update" :canResource="$application" type="submit">{{ __('Save') }}</x-forms.button>
            @if ($buildPack === 'dockercompose')
                <x-forms.button canGate="update" :canResource="$application" wire:target='initLoadingCompose'
                    x-on:click="$wire.dispatch('loadCompose', false)">
                    {{ $application->docker_compose_raw ? __('Reload Compose File') : __('Load Compose File') }}
                </x-forms.button>
            @endif
        </div>
        <div>{{ __('General configuration for your application.') }}</div>
        <div class="flex flex-col gap-2 py-4">
            <div class="flex flex-col items-end gap-2 xl:flex-row">
                <x-forms.input x-bind:disabled="shouldDisable()" id="name" :label="__('Name')" required />
                <x-forms.input x-bind:disabled="shouldDisable()" id="description" :label="__('Description')" />
            </div>

            @if (!$application->dockerfile && $application->build_pack !== 'dockerimage')
                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <x-forms.select x-bind:disabled="shouldDisable()" wire:model.live="buildPack" :label="__('Build Pack')"
                            required>
                            <option value="nixpacks">{{ __('Nixpacks') }}</option>
                            <option value="railpack">{{ __('Railpack (Beta)') }}</option>
                            <option value="static">{{ __('Static') }}</option>
                            <option value="dockerfile">Dockerfile</option>
                            <option value="dockercompose">{{ __('Docker Compose') }}</option>
                        </x-forms.select>
                        @if ($isStatic || $buildPack === 'static')
                            <x-forms.select x-bind:disabled="!canUpdate" id="staticImage" :label="__('Static Image')" required>
                                <option value="nginx:alpine">nginx:alpine</option>
                                <option disabled value="apache:alpine">apache:alpine</option>
                            </x-forms.select>
                        @endif
                    </div>

                    @if ($buildPack === 'dockercompose')
                        @if (
                            !is_null($parsedServices) &&
                                count($parsedServices) > 0 &&
                                !$application->settings->is_raw_compose_deployment_enabled)
                            @php
                                $hasNonDatabaseService = collect(data_get($parsedServices, 'services', []))
                                    ->contains(fn($service) => !isDatabaseImage(data_get($service, 'image')));
                            @endphp
                            @if ($hasNonDatabaseService)
                                <h3 class="pt-6">{{ __('Domains') }}</h3>
                            @endif
                            @foreach (data_get($parsedServices, 'services') as $serviceName => $service)
                                @if (!isDatabaseImage(data_get($service, 'image')))
                                    <div class="flex items-end gap-2">
                                        <x-forms.input
                                            :helper="__('You can specify one domain with path or more with comma. You can specify a port to bind the domain to.<br><br><span class=\'text-helper\'>Example</span><br>- https://app.coolify.io,https://cloud.coolify.io/dashboard<br>- https://app.coolify.io/api/v3<br>- https://app.coolify.io:3000 -> app.coolify.io will point to port 3000 inside the container.<br>- https://app.coolify.io:8080/api -> app.coolify.io/api will point to port 8080 inside the container.')"
                                            :label="__('Domains for :serviceName', ['serviceName' => $serviceName])"
                                            id="parsedServiceDomains.{{ str($serviceName)->replace('-', '_')->replace('.', '_') }}.domain"
                                            x-bind:disabled="shouldDisable()"></x-forms.input>
                                        @can('update', $application)
                                            <x-forms.button wire:click="generateDomain('{{ $serviceName }}')">{{ __('Generate Domain') }}</x-forms.button>
                                        @endcan
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endif

                </div>
            @endif
            @if ($isStatic || $buildPack === 'static')
                <x-forms.textarea id="customNginxConfiguration"
                    :placeholder="__('Empty means default configuration will be used.')" :label="__('Custom Nginx Configuration')"
                    :helper="__('You can add custom Nginx configuration here.')" x-bind:disabled="!canUpdate" />
                @can('update', $application)
                    <x-modal-confirmation :title="__('Confirm Nginx Configuration Generation?')"
                        :buttonTitle="__('Generate Default Nginx Configuration')" buttonFullWidth
                        submitAction="generateNginxConfiguration('{{ $application->settings->is_spa ? 'spa' : 'static' }}')"
                        :actions="[
                            __('This will overwrite your current custom Nginx configuration.'),
                            __('The default configuration will be generated based on your application type (:type).', ['type' => $application->settings->is_spa ? 'SPA' : 'static']),
                        ]" />
                @endcan
            @endif
            @if ($application->could_set_build_commands() || ($isStatic && $buildPack !== 'static'))
                <div class="w-96 pb-6">
                    @if ($application->could_set_build_commands())
                        <x-forms.checkbox instantSave id="isStatic" :label="__('Is it a static site?')"
                            :helper="__('If your application is a static site or the final build assets should be served as a static site, enable this.')"
                            x-bind:disabled="!canUpdate" />
                    @endif
                    @if ($isStatic && $buildPack !== 'static')
                        <x-forms.checkbox :label="__('Is it a SPA (Single Page Application)?')"
                            :helper="__('If your application is a SPA, enable this.')" id="isSpa" instantSave
                            x-bind:disabled="!canUpdate"></x-forms.checkbox>
                    @endif
                </div>
            @endif
            @if ($buildPack !== 'dockercompose')
                <div class="flex items-end gap-2">
                    @if ($application->settings->is_container_label_readonly_enabled == false)
                        <x-forms.input placeholder="https://coolify.io" wire:model="fqdn" :label="__('Domains')" readonly
                            :helper="__('Readonly labels are disabled. You can set the domains in the labels section.')"
                            x-bind:disabled="!canUpdate" />
                    @else
                        <x-forms.input placeholder="https://coolify.io" wire:model="fqdn" :label="__('Domains')"
                            :helper="__('You can specify one domain with path or more with comma. You can specify a port to bind the domain to.<br><br><span class=\'text-helper\'>Example</span><br>- https://app.coolify.io,https://cloud.coolify.io/dashboard<br>- https://app.coolify.io/api/v3<br>- https://app.coolify.io:3000 -> app.coolify.io will point to port 3000 inside the container.<br>- https://app.coolify.io:8080/api -> app.coolify.io/api will point to port 8080 inside the container.')"
                            x-bind:disabled="!canUpdate" />
                        @can('update', $application)
                            <x-forms.button wire:click="getWildcardDomain">{{ __('Generate Domain') }}
                            </x-forms.button>
                        @endcan
                    @endif
                </div>
                <div class="flex items-end gap-2">
                    @if ($application->settings->is_container_label_readonly_enabled == false)
                        @if ($application->redirect === 'both')
                            <x-forms.input :label="__('Direction')" :value="__('Allow www & non-www.')" readonly
                                :helper="__('Readonly labels are disabled. You can set the direction in the labels section.')"
                                x-bind:disabled="!canUpdate" />
                        @elseif ($application->redirect === 'www')
                            <x-forms.input :label="__('Direction')" :value="__('Redirect to www.')" readonly
                                :helper="__('Readonly labels are disabled. You can set the direction in the labels section.')"
                                x-bind:disabled="!canUpdate" />
                        @elseif ($application->redirect === 'non-www')
                            <x-forms.input :label="__('Direction')" :value="__('Redirect to non-www.')" readonly
                                :helper="__('Readonly labels are disabled. You can set the direction in the labels section.')"
                                x-bind:disabled="!canUpdate" />
                        @endif
                    @else
                        <x-forms.select :label="__('Direction')" id="redirect" required
                            :helper="__('You must need to add www and non-www as an A DNS record. Make sure the www domain is added under Domains.')"
                            x-bind:disabled="!canUpdate">
                            <option value="both">{{ __('Allow www & non-www.') }}</option>
                            <option value="www">{{ __('Redirect to www.') }}</option>
                            <option value="non-www">{{ __('Redirect to non-www.') }}</option>
                        </x-forms.select>
                        @if ($application->settings->is_container_label_readonly_enabled)
                            @can('update', $application)
                                <x-modal-confirmation :title="__('Confirm Redirection Setting?')" :buttonTitle="__('Set Direction')"
                                    submitAction="setRedirect" :actions="[__('All traffic will be redirected to the selected direction.')]"
                                    confirmationText="{{ $application->fqdn . '/' }}"
                                    :confirmationLabel="__('Please confirm the execution of the action by entering the Application URL below')"
                                    :shortConfirmationLabel="__('Application URL')" :confirmWithPassword="false"
                                    :step2ButtonText="__('Set Direction')">
                                    <x-slot:customButton>
                                        <div class="w-[7.2rem]">{{ __('Set Direction') }}</div>
                                    </x-slot:customButton>
                                </x-modal-confirmation>
                            @endcan
                        @endif
                    @endif
                </div>
            @endif

            @if ($buildPack !== 'dockercompose')
                <div class="flex items-center gap-2 pt-8">
                    <h3>{{ __('Docker Registry') }}</h3>
                    @if ($application->build_pack !== 'dockerimage' && !$application->destination->server->isSwarm())
                        <x-helper
                            :helper="__('Push the built image to a docker registry. More info <a class=\'underline\' href=\'https://coolify.io/docs/knowledge-base/docker/registry\' target=\'_blank\'>here</a>.')" />
                    @endif
                </div>
                @if ($application->destination->server->isSwarm())
                    @if ($application->build_pack !== 'dockerimage')
                        <div>{{ __('Docker Swarm requires the image to be available in a registry. More info') }} <a
                                class="underline" href="https://coolify.io/docs/knowledge-base/docker/registry"
                                target="_blank">{{ __('here') }}</a>.</div>
                    @endif
                @endif
                <div class="flex flex-col gap-2 xl:flex-row">
                    @if ($application->build_pack === 'dockerimage')
                        @if ($application->destination->server->isSwarm())
                            <x-forms.input required id="dockerRegistryImageName" :label="__('Docker Image')"
                                x-bind:disabled="!canUpdate" />
                            <x-forms.input id="dockerRegistryImageTag" :label="__('Docker Image Tag or Hash')"
                                :helper="__('Enter a tag (e.g., \'latest\', \'v1.2.3\') or SHA256 hash (e.g., \'sha256-59e02939b1bf39f16c93138a28727aec520bb916da021180ae502c61626b3cf0\')')"
                                x-bind:disabled="!canUpdate" />
                        @else
                            <x-forms.input id="dockerRegistryImageName" :label="__('Docker Image')"
                                x-bind:disabled="!canUpdate" />
                            <x-forms.input id="dockerRegistryImageTag" :label="__('Docker Image Tag or Hash')"
                                :helper="__('Enter a tag (e.g., \'latest\', \'v1.2.3\') or SHA256 hash (e.g., \'sha256-59e02939b1bf39f16c93138a28727aec520bb916da021180ae502c61626b3cf0\')')"
                                x-bind:disabled="!canUpdate" />
                        @endif
                    @else
                        @if (
                                $application->destination->server->isSwarm() ||
                                $application->additional_servers->count() > 0 ||
                                $application->settings->is_build_server_enabled)
                            <x-forms.input id="dockerRegistryImageName" required :label="__('Docker Image')"
                                :placeholder="__('Required!')" x-bind:disabled="!canUpdate" />
                            <x-forms.input id="dockerRegistryImageTag"
                                :helper="__('If set, it will tag the built image with this tag too. <br><br>Example: If you set it to \'latest\', it will push the image with the commit sha tag + with the latest tag.')"
                                :placeholder="__('Empty means latest will be used.')" :label="__('Docker Image Tag')"
                                x-bind:disabled="!canUpdate" />
                        @else
                            <x-forms.input id="dockerRegistryImageName"
                                :helper="__('Empty means it won\'t push the image to a docker registry. Pre-tag the image with your registry url if you want to push it to a private registry (default: Dockerhub). <br><br>Example: ghcr.io/myimage')"
                                :placeholder="__('Empty means it won\'t push the image to a docker registry.')"
                                :label="__('Docker Image')" x-bind:disabled="!canUpdate" />
                            <x-forms.input id="dockerRegistryImageTag"
                                :placeholder="__('Empty means only push commit sha tag.')"
                                :helper="__('If set, it will tag the built image with this tag too. <br><br>Example: If you set it to \'latest\', it will push the image with the commit sha tag + with the latest tag.')"
                                :label="__('Docker Image Tag')" x-bind:disabled="!canUpdate" />
                        @endif
                    @endif
                </div>
            @endif
            <div class="pt-6">
                <h3>{{ __('Build') }}</h3>
                @if ($application->build_pack === 'dockerimage')
                    <x-forms.input
                        :helper="__('You can add custom docker run options that will be used when your container is started.<br>Note: Not all options are supported, as they could mess up Coolify\'s automation and could cause bad experience for users.<br><br>Check the <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/custom-commands\'>docs.</a>')"
                        placeholder="--cap-add SYS_ADMIN --device=/dev/fuse --security-opt apparmor:unconfined --ulimit nofile=1024:1024 --tmpfs /run:rw,noexec,nosuid,size=65536k --hostname=myapp"
                        id="customDockerRunOptions" :label="__('Custom Docker Options')" x-bind:disabled="!canUpdate" />
                @else
                    @if ($application->could_set_build_commands())
                        @if ($buildPack === 'nixpacks' || $buildPack === 'railpack')
                            <div class="flex flex-col gap-2 xl:flex-row">
                                <x-forms.input :helper="__('If you modify this, you probably need to have a :file', ['file' => $buildPack === 'railpack' ? 'railpack.json' : 'nixpacks.toml'])"
                                    id="installCommand" :label="__('Install Command')" x-bind:disabled="!canUpdate" />
                                <x-forms.input :helper="__('If you modify this, you probably need to have a :file', ['file' => $buildPack === 'railpack' ? 'railpack.json' : 'nixpacks.toml'])"
                                    id="buildCommand" :label="__('Build Command')" x-bind:disabled="!canUpdate" />
                                <x-forms.input :helper="__('If you modify this, you probably need to have a :file', ['file' => $buildPack === 'railpack' ? 'railpack.json' : 'nixpacks.toml'])"
                                    id="startCommand" :label="__('Start Command')" x-bind:disabled="!canUpdate" />
                            </div>
                                @if ($buildPack === 'nixpacks')
                            <div class="pt-1 text-xs">

                                    <span class="font-medium">{{ __('Nixpacks') }}</span>
                                {{ __('will detect the required configuration automatically.') }}
                                <a class="underline" href="https://coolify.io/docs/applications/">{{ __('Framework Specific Docs') }}</a>
                            </div>
                                @endif
                        @endif

                    @endif
                    <div class="flex flex-col gap-2 pt-6 pb-10">
                        @if ($buildPack === 'dockercompose')
                            <div class="flex flex-col gap-2"
                                @can('update', $application) x-init="$wire.dispatch('loadCompose', true)" @endcan>
                                <div x-data="{
                                    baseDir: @entangle('baseDirectory'),
                                    composeLocation: @entangle('dockerComposeLocation'),
                                    normalizePath(path) {
                                        if (!path || path.trim() === '') return '/';
                                        path = path.trim();
                                        path = path.replace(/\/+$/, '');
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
                                }" class="flex gap-2">
                                    <x-forms.input x-bind:disabled="shouldDisable()" placeholder="/"
                                        :label="__('Base Directory')"
                                        :helper="__('Directory to use as root. Useful for monorepos.')" x-model="baseDir"
                                        @blur="normalizeBaseDir()" />
                                    <x-forms.input x-bind:disabled="shouldDisable()"
                                        placeholder="/docker-compose.yaml"
                                        :label="__('Docker Compose Location')"
                                        :helper="__('It is calculated together with the Base Directory:<br><span class=\'dark:text-warning\'>:path</span>', ['path' => Str::start($baseDirectory . $dockerComposeLocation, '/')])"
                                        x-model="composeLocation" @blur="normalizeComposeLocation()" />
                                </div>
                                <div class="w-full sm:w-96">
                                    <x-forms.checkbox instantSave id="isPreserveRepositoryEnabled"
                                        :label="__('Preserve Repository During Deployment')"
                                        :helper="__('Git repository (based on the base directory settings) will be copied to the deployment directory.')"
                                        x-bind:disabled="shouldDisable()" />
                                </div>
                                <div class="pt-4">{{ __('The following commands are for advanced use cases. Only modify them if you know what you are doing.') }}</div>
                                <div class="flex gap-2">
                                    <x-forms.input x-bind:disabled="shouldDisable()"
                                        placeholder="docker compose build" id="dockerComposeCustomBuildCommand"
                                        :helper="__('The compose file path (<span class=\'dark:text-warning\'>-f</span> flag) and environment variables (<span class=\'dark:text-warning\'>--env-file</span> flag) are automatically injected based on your Base Directory and Docker Compose Location settings. You can override by providing your own <span class=\'dark:text-warning\'>-f</span> or <span class=\'dark:text-warning\'>--env-file</span> flags.<br><br>If you use this, you need to specify paths relatively and should use the same compose file in the custom command, otherwise the automatically configured labels / etc won\'t work.<br><br>Example usage: <span class=\'dark:text-warning\'>docker compose build</span>')"
                                        :label="__('Custom Build Command')" />
                                    <x-forms.input x-bind:disabled="shouldDisable()"
                                        placeholder="docker compose up -d" id="dockerComposeCustomStartCommand"
                                        :helper="__('The compose file path (<span class=\'dark:text-warning\'>-f</span> flag) and environment variables (<span class=\'dark:text-warning\'>--env-file</span> flag) are automatically injected based on your Base Directory and Docker Compose Location settings. You can override by providing your own <span class=\'dark:text-warning\'>-f</span> or <span class=\'dark:text-warning\'>--env-file</span> flags.<br><br>If you use this, you need to specify paths relatively and should use the same compose file in the custom command, otherwise the automatically configured labels / etc won\'t work.<br><br>Example usage: <span class=\'dark:text-warning\'>docker compose up -d</span>')"
                                        :label="__('Custom Start Command')" />
                                </div>
                                @if ($this->dockerComposeCustomBuildCommand)
                                    <div wire:key="docker-compose-build-preview">
                                        <x-forms.input readonly value="{{ $this->dockerComposeBuildCommandPreview }}"
                                            :label="__('Final Build Command (Preview)')"
                                            :helper="__('This shows the actual command that will be executed with auto-injected flags.')" />
                                    </div>
                                @endif
                                @if ($this->dockerComposeCustomStartCommand)
                                    <div wire:key="docker-compose-start-preview">
                                        <x-forms.input readonly value="{{ $this->dockerComposeStartCommandPreview }}"
                                            :label="__('Final Start Command (Preview)')"
                                            :helper="__('This shows the actual command that will be executed with auto-injected flags.')" />
                                    </div>
                                @endif
                                @if ($this->application->is_github_based() && !$this->application->is_public_repository())
                                    <div class="pt-4">
                                        <x-forms.textarea
                                            :helper="__('Order-based pattern matching to filter Git webhook deployments. Supports wildcards (*, **, ?) and negation (!). Last matching pattern wins.')"
                                            placeholder="services/api/**" id="watchPaths" :label="__('Watch Paths')"
                                            x-bind:disabled="shouldDisable()" />
                                    </div>
                                @endif
                            </div>
                        @else
                            <div x-data="{
                                baseDir: @entangle('baseDirectory'),
                                dockerfileLocation: @entangle('dockerfileLocation'),
                                normalizePath(path) {
                                    if (!path || path.trim() === '') return '/';
                                    path = path.trim();
                                    path = path.replace(/\/+$/, '');
                                    if (!path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    return path;
                                },
                                normalizeBaseDir() {
                                    this.baseDir = this.normalizePath(this.baseDir);
                                },
                                normalizeDockerfileLocation() {
                                    this.dockerfileLocation = this.normalizePath(this.dockerfileLocation);
                                }
                            }" class="flex flex-col gap-2 xl:flex-row">
                                <x-forms.input placeholder="/"
                                    :label="__('Base Directory')" :helper="__('Directory to use as root. Useful for monorepos.')"
                                    x-bind:disabled="!canUpdate" x-model="baseDir" @blur="normalizeBaseDir()" />
                                @if ($buildPack === 'dockerfile' && !$application->dockerfile)
                                    <x-forms.input placeholder="/Dockerfile"
                                        :label="__('Dockerfile Location')"
                                        :helper="__('It is calculated together with the Base Directory:<br><span class=\'dark:text-warning\'>:path</span>', ['path' => Str::start($application->base_directory . $application->dockerfile_location, '/')])"
                                        x-bind:disabled="!canUpdate" x-model="dockerfileLocation"
                                        @blur="normalizeDockerfileLocation()" />
                                @endif

                                @if ($buildPack === 'dockerfile')
                                    <x-forms.input id="dockerfileTargetBuild" :label="__('Docker Build Stage Target')"
                                        :helper="__('Useful if you have multi-staged dockerfile.')"
                                        x-bind:disabled="!canUpdate" />
                                @endif
                                @if ($application->could_set_build_commands())
                                    @if ($application->settings->is_static)
                                        <x-forms.input placeholder="/dist" id="publishDirectory"
                                            :label="__('Publish Directory')" required x-bind:disabled="!canUpdate" />
                                    @else
                                        <x-forms.input placeholder="/" id="publishDirectory"
                                            :label="__('Publish Directory')" x-bind:disabled="!canUpdate" />
                                    @endif
                                @endif

                            </div>
                            @if ($this->application->is_github_based() && !$this->application->is_public_repository())
                                <div class="pb-4">
                                    <x-forms.textarea
                                        :helper="__('Order-based pattern matching to filter Git webhook deployments. Supports wildcards (*, **, ?) and negation (!). Last matching pattern wins.')"
                                        placeholder="src/pages/**" id="watchPaths" :label="__('Watch Paths')"
                                        x-bind:disabled="!canUpdate" />
                                </div>
                            @endif
                            <x-forms.input
                                :helper="__('You can add custom docker run options that will be used when your container is started.<br>Note: Not all options are supported, as they could mess up Coolify\'s automation and could cause bad experience for users.<br><br>Check the <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/custom-commands\'>docs.</a>')"
                                placeholder="--cap-add SYS_ADMIN --device=/dev/fuse --security-opt apparmor:unconfined --ulimit nofile=1024:1024 --tmpfs /run:rw,noexec,nosuid,size=65536k --hostname=myapp"
                                id="customDockerRunOptions" :label="__('Custom Docker Options')"
                                x-bind:disabled="!canUpdate" />

                            @if ($buildPack !== 'dockercompose')
                                <div class="pt-2 w-full sm:w-96">
                                    <x-forms.checkbox
                                        :helper="__('Use a build server to build your application. You can configure your build server in the Server settings. For more info, check the <a href=\'https://coolify.io/docs/knowledge-base/server/build-server\' class=\'underline\' target=\'_blank\'>documentation</a>.')"
                                        instantSave id="isBuildServerEnabled" :label="__('Use a Build Server?')"
                                        x-bind:disabled="!canUpdate" />
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
            @if ($buildPack === 'dockercompose')
                <div x-data="{ showRaw: true }">
                    <div class="flex items-center gap-2">
                        <h3>{{ __('Docker Compose') }}</h3>
                        <x-forms.button x-show="!($application->settings->is_raw_compose_deployment_enabled)"
                            @click.prevent="showRaw = !showRaw"
                            x-text="showRaw ? @js(__('Show Deployable Compose')) : @js(__('Show Raw Compose'))"></x-forms.button>
                    </div>
                    @if ($application->settings->is_raw_compose_deployment_enabled)
                        <x-forms.textarea rows="10" readonly id="dockerComposeRaw"
                            :label="__('Docker Compose Content (applicationId: :id)', ['id' => $application->id])"
                            :helper="__('You need to modify the docker compose file in the git repository.')"
                            monacoEditorLanguage="yaml" useMonacoEditor />
                    @else
                        @if ((int) $application->compose_parsing_version >= 3)
                            <div x-show="showRaw">
                                <x-forms.textarea rows="10" readonly id="dockerComposeRaw"
                                    :label="__('Docker Compose Content (raw)')"
                                    :helper="__('You need to modify the docker compose file in the git repository.')"
                                    monacoEditorLanguage="yaml" useMonacoEditor />
                            </div>
                        @endif
                        <div x-show="showRaw === false">
                            <x-forms.textarea rows="10" readonly id="dockerCompose"
                                :label="__('Docker Compose Content')"
                                :helper="__('You need to modify the docker compose file in the git repository.')"
                                monacoEditorLanguage="yaml" useMonacoEditor />
                        </div>
                    @endif
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox :label="__('Escape special characters in labels?')"
                            :helper="__('By default, $ (and other chars) is escaped. So if you write $ in the labels, it will be saved as $$.<br><br>If you want to use env variables inside the labels, turn this off.')"
                            id="isContainerLabelEscapeEnabled" instantSave
                            x-bind:disabled="!canUpdate"></x-forms.checkbox>
                        {{-- <x-forms.checkbox label="Readonly labels"
                            helper="Labels are readonly by default. Readonly means that edits you do to the labels could be lost and Coolify will autogenerate the labels for you. If you want to edit the labels directly, disable this option. <br><br>Be careful, it could break the proxy configuration after you restart the container as Coolify will now NOT autogenerate the labels for you (ofc you can always reset the labels to the coolify defaults manually)."
                            id="isContainerLabelReadonlyEnabled" instantSave></x-forms.checkbox> --}}
                    </div>
                </div>
            @endif
            @if ($application->dockerfile)
                <x-forms.textarea :label="__('Dockerfile')" id="dockerfile" monacoEditorLanguage="dockerfile"
                    useMonacoEditor rows="6" x-bind:disabled="!canUpdate"> </x-forms.textarea>
            @endif
            @if ($buildPack !== 'dockercompose')
                <h3 class="pt-8">{{ __('Network') }}</h3>
                @if ($this->detectedPortInfo)
                    @if ($this->detectedPortInfo['isEmpty'])
                        <div
                            class="flex items-start gap-2 p-4 mb-4 text-sm rounded-lg bg-warning-50 dark:bg-warning-900/20 text-warning-800 dark:text-warning-300 border border-warning-200 dark:border-warning-800">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <span class="font-semibold">{{ __('PORT environment variable detected (:port)', ['port' => $this->detectedPortInfo['port']]) }}</span>
                                <p class="mt-1">{!! __('Your Ports Exposes field is empty. Consider setting it to <strong>:port</strong> to ensure the proxy routes traffic correctly.', ['port' => $this->detectedPortInfo['port']]) !!}
                                </p>
                            </div>
                        </div>
                    @elseif (!$this->detectedPortInfo['matches'])
                        <div
                            class="flex items-start gap-2 p-4 mb-4 text-sm rounded-lg bg-warning-50 dark:bg-warning-900/20 text-warning-800 dark:text-warning-300 border border-warning-200 dark:border-warning-800">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <span class="font-semibold">{{ __('PORT mismatch detected') }}</span>
                                <p class="mt-1">{!! __('Your PORT environment variable is set to <strong>:port</strong>, but it is not in your Ports Exposes configuration. Ensure they match for proper proxy routing.', ['port' => $this->detectedPortInfo['port']]) !!}
                                </p>
                            </div>
                        </div>
                    @else
                        <div
                            class="flex items-start gap-2 p-4 mb-4 text-sm rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <span class="font-semibold">{{ __('PORT environment variable configured') }}</span>
                                <p class="mt-1">{{ __('Your PORT environment variable (:port) matches your Ports Exposes configuration.', ['port' => $this->detectedPortInfo['port']]) }}</p>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="flex flex-col gap-2 xl:flex-row">
                    @if ($isStatic || $buildPack === 'static')
                        <x-forms.input id="portsExposes" :label="__('Ports Exposes')" readonly
                            x-bind:disabled="!canUpdate" />
                    @else
                        @if ($application->settings->is_container_label_readonly_enabled === false)
                            <x-forms.input placeholder="3000,3001" id="portsExposes" :label="__('Ports Exposes')" readonly
                                :helper="__('Readonly labels are disabled. You can set the ports manually in the labels section.')"
                                x-bind:disabled="!canUpdate" />
                        @else
                            <x-forms.input placeholder="3000,3001" id="portsExposes" :label="__('Ports Exposes')" required
                                :helper="__('A comma separated list of ports your application uses. The first port will be used as default healthcheck port if nothing defined in the Healthcheck menu. Be sure to set this correctly.')"
                                x-bind:disabled="!canUpdate" />
                        @endif
                    @endif
                    @if (!$application->destination->server->isSwarm())
                        <x-forms.input placeholder="3000:3000" id="portsMappings" :label="__('Port Mappings')"
                            :helper="__('A comma separated list of ports you would like to map to the host system. Useful when you do not want to use domains.<br><br><span class=\'inline-block font-bold dark:text-warning\'>Format:</span> host:container<br><br><span class=\'inline-block font-bold dark:text-warning\'>Example:</span> 3000:3000,3002:3002<br><br>Rolling update is not supported if you have a port mapped to the host.')"
                            x-bind:disabled="!canUpdate" />
                    @endif
                    @if (!$application->destination->server->isSwarm())
                        <x-forms.input id="customNetworkAliases" :label="__('Network Aliases')"
                            :helper="__('A comma separated list of custom network aliases you would like to add for container in Docker network.<br><br><span class=\'inline-block font-bold dark:text-warning\'>Example:</span><br>api.internal,api.local')"
                            wire:model="customNetworkAliases" x-bind:disabled="!canUpdate" />
                    @endif
                </div>

                <h3 class="pt-8">{{ __('HTTP Basic Authentication') }}</h3>
                <div>
                    <div class="w-full sm:w-96">
                        <x-forms.checkbox :helper="__('This will add the proper proxy labels to the container.')" instantSave
                            :label="__('Enable')" id="isHttpBasicAuthEnabled" x-bind:disabled="!canUpdate" />
                    </div>
                    @if ($application->is_http_basic_auth_enabled)
                        <div class="flex gap-2 py-2">
                            <x-forms.input id="httpBasicAuthUsername" :label="__('Username')" required
                                x-bind:disabled="!canUpdate" />
                            <x-forms.input id="httpBasicAuthPassword" type="password" :label="__('Password')" required
                                x-bind:disabled="!canUpdate" />
                        </div>
                    @endif
                </div>

                <h3 class="pt-8">{{ __('Labels') }}</h3>
                @if ($application->settings->is_container_label_readonly_enabled)
                    <x-forms.textarea readonly disabled :label="__('Container Labels')" rows="15" id="customLabels"
                        monacoEditorLanguage="ini" useMonacoEditor x-bind:disabled="!canUpdate"></x-forms.textarea>
                @else
                    <x-forms.textarea :label="__('Container Labels')" rows="15" id="customLabels"
                        monacoEditorLanguage="ini" useMonacoEditor x-bind:disabled="!canUpdate"></x-forms.textarea>
                @endif
                <div class="w-full sm:w-96">
                    <x-forms.checkbox :label="__('Readonly labels')"
                        :helper="__('Labels are readonly by default. Readonly means that edits you do to the labels could be lost and Coolify will autogenerate the labels for you. If you want to edit the labels directly, disable this option. <br><br>Be careful, it could break the proxy configuration after you restart the container as Coolify will now NOT autogenerate the labels for you (ofc you can always reset the labels to the coolify defaults manually).')"
                        id="isContainerLabelReadonlyEnabled" instantSave
                        x-bind:disabled="!canUpdate"></x-forms.checkbox>
                    <x-forms.checkbox :label="__('Escape special characters in labels?')"
                        :helper="__('By default, $ (and other chars) is escaped. So if you write $ in the labels, it will be saved as $$.<br><br>If you want to use env variables inside the labels, turn this off.')"
                        id="isContainerLabelEscapeEnabled" instantSave
                        x-bind:disabled="!canUpdate"></x-forms.checkbox>
                </div>
                @can('update', $application)
                    <x-modal-confirmation :title="__('Confirm Labels Reset to Coolify Defaults?')"
                        :buttonTitle="__('Reset Labels to Defaults')" buttonFullWidth submitAction="resetDefaultLabels(true)"
                        :actions="[
                            __('All your custom proxy labels will be lost.'),
                            __('Proxy labels (traefik, caddy, etc) will be reset to the coolify defaults.'),
                        ]" confirmationText="{{ $application->fqdn . '/' }}"
                        :confirmationLabel="__('Please confirm the execution of the actions by entering the Application URL below')"
                        :shortConfirmationLabel="__('Application URL')" :confirmWithPassword="false"
                        :step2ButtonText="__('Permanently Reset Labels')" />
                @endcan
            @endif

            <h3 class="pt-8">{{ __('Pre/Post Deployment Commands') }}</h3>
            <div class="flex flex-col gap-2 xl:flex-row">
                <x-forms.input x-bind:disabled="shouldDisable()" placeholder="php artisan migrate"
                    id="preDeploymentCommand" :label="__('Pre-deployment')"
                    :helper="__('An optional script or command to execute in the existing container before the deployment begins.<br>It is always executed with \'sh -c\', so you do not need add it manually.')" />
                @if ($buildPack === 'dockercompose')
                    <x-forms.input x-bind:disabled="shouldDisable()" id="preDeploymentCommandContainer"
                        :label="__('Container Name')"
                        :helper="__('The name of the container to execute within. You can leave it blank if your application only has one container.')" />
                @endif
            </div>
            <div class="flex flex-col gap-2 xl:flex-row">
                <x-forms.input x-bind:disabled="shouldDisable()" placeholder="php artisan migrate"
                    id="postDeploymentCommand" :label="__('Post-deployment')"
                    :helper="__('An optional script or command to execute in the newly built container after the deployment completes.<br>It is always executed with \'sh -c\', so you do not need add it manually.')" />
                @if ($buildPack === 'dockercompose')
                    <x-forms.input x-bind:disabled="shouldDisable()" id="postDeploymentCommandContainer"
                        :label="__('Container Name')"
                        :helper="__('The name of the container to execute within. You can leave it blank if your application only has one container.')" />
                @endif
            </div>
        </div>
    </form>

    <x-domain-conflict-modal :conflicts="$domainConflicts" :showModal="$showDomainConflictModal" confirmAction="confirmDomainUsage" />

    @script
        <script>
            $wire.$on('loadCompose', (isInit = true) => {
                // Only load compose file if user has permission (this event should only be dispatched when authorized)
                $wire.initLoadingCompose = true;
                $wire.loadComposeFile(isInit);
            });
        </script>
    @endscript
</div>
