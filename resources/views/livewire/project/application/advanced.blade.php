<div>
    @php
        $consistentNameHelper = __("The deployed container will have the same name (:name). <span class='font-bold dark:text-warning'>You will lose the rolling update feature!</span>", ['name' => $application->uuid]);
        $customContainerNameHelper = __("You can add a custom name for your container.<br><br>The name will be converted to slug format when you save it. <span class='font-bold dark:text-warning'>You will lose the rolling update feature!</span>");
        $previewDeploymentsHelper = __("Allow to automatically deploy Preview Deployments for all opened PR's.<br><br>Closing a PR will delete Preview Deployments.");
        $rawComposeHelper = __("WARNING: Advanced use cases only. Your docker compose file will be deployed as-is. Nothing is modified by Coolify. You need to configure the proxy parts. More info in the <a class='underline dark:text-white' href='https://coolify.io/docs/knowledge-base/docker/compose#raw-docker-compose-deployment'>documentation.</a>");
        $connectPredefinedNetworkHelper = __("By default, you do not reach the Coolify defined networks.<br>Starting a docker compose based resource will have an internal network. <br>If you connect to a Coolify defined network, you maybe need to use different internal DNS names to connect to a resource.<br><br>For more information, check <a class='underline dark:text-white' target='_blank' href='https://coolify.io/docs/knowledge-base/docker/compose#connect-to-predefined-networks'>this</a>.");
        $enableGpuHelper = __("Enable GPU usage for this application. More info <a href='https://docs.docker.com/compose/gpu-support/' class='underline dark:text-white' target='_blank'>here</a>.");
        $gpuDeviceIdsHelper = __("Comma separated list of device ids. More info <a href='https://docs.docker.com/compose/gpu-support/#access-specific-devices' class='underline dark:text-white' target='_blank'>here</a>.");
    @endphp
    <div class="flex flex-col md:w-96">
        <div class="flex items-center gap-2">
            <h2>{{ __('Advanced') }}</h2>
        </div>
        <div>{{ __('Advanced configuration for your application.') }}</div>
        <div class="flex flex-col gap-1 pt-4">
            <h3>{{ __('Build') }}</h3>
            <x-forms.checkbox :helper="__('Disable Docker build cache on every deployment.')" instantSave
                id="disableBuildCache" :label="__('Disable Build Cache')" canGate="update" :canResource="$application" />
            <x-forms.checkbox
                :helper="__('When enabled, Coolify automatically adds ARG statements to your Dockerfile for build-time variables. Disable this if you manage ARGs manually in your Dockerfile to preserve Docker build cache.')"
                instantSave id="injectBuildArgsToDockerfile" :label="__('Inject Build Args to Dockerfile')" canGate="update"
                :canResource="$application" />
            <x-forms.checkbox
                :helper="__('When enabled, SOURCE_COMMIT (git commit hash) is available during Docker build. Disable to preserve cache across different commits - SOURCE_COMMIT will still be available at runtime.')"
                instantSave id="includeSourceCommitInBuild" :label="__('Include Source Commit in Build')" canGate="update"
                :canResource="$application" />

            <h3 class="pt-4">{{ __('Container') }}</h3>
            <x-forms.checkbox
                :helper="$consistentNameHelper"
                instantSave id="isConsistentContainerNameEnabled" :label="__('Consistent Container Names')" canGate="update"
                :canResource="$application" />
            @if ($isConsistentContainerNameEnabled === false)
                <form class="flex items-end gap-2 " wire:submit.prevent='saveCustomName'>
                    <x-forms.input
                        :helper="$customContainerNameHelper"
                        instantSave id="customInternalName" :label="__('Custom Container Name')" canGate="update"
                        :canResource="$application" />
                    <x-forms.button canGate="update" :canResource="$application" type="submit">{{ __('Save') }}</x-forms.button>
                </form>
            @endif

            @if ($application->git_based())
                <h3 class="pt-4">{{ __('Deployment') }}</h3>
                <x-forms.checkbox :helper="__('Automatically deploy new commits based on Git webhooks.')" instantSave
                    id="isAutoDeployEnabled" :label="__('Auto Deploy')" canGate="update" :canResource="$application" />
                <x-forms.checkbox
                    :helper="$previewDeploymentsHelper"
                    instantSave id="isPreviewDeploymentsEnabled" :label="__('Preview Deployments')" canGate="update"
                    :canResource="$application" />
                <x-forms.checkbox
                    :helper="__('When enabled, anyone can trigger PR deployments. When disabled, only repository members, collaborators, and contributors can trigger PR deployments.')"
                    instantSave id="isPrDeploymentsPublicEnabled" :label="__('Allow Public PR Deployments')" canGate="update"
                    :canResource="$application" :disabled="!$isPreviewDeploymentsEnabled" />

                <h3 class="pt-4">{{ __('Git') }}</h3>
                <x-forms.checkbox instantSave id="isGitSubmodulesEnabled" :label="__('Submodules')"
                    :helper="__('Allow Git Submodules during build process.')" canGate="update" :canResource="$application" />
                <x-forms.checkbox instantSave id="isGitLfsEnabled" :label="__('LFS')"
                    :helper="__('Allow Git LFS during build process.')" canGate="update" :canResource="$application" />
                <x-forms.checkbox instantSave id="isGitShallowCloneEnabled" :label="__('Shallow Clone')"
                    :helper="__('Use shallow cloning (--depth=1) to speed up deployments by only fetching the latest commit history. This reduces clone time and resource usage, especially for large repositories.')"
                    canGate="update" :canResource="$application" />
            @endif

            @if ($application->build_pack === 'dockercompose')
                <h3 class="pt-4">{{ __('Docker Compose') }}</h3>
                <x-forms.checkbox instantSave id="isRawComposeDeploymentEnabled" :label="__('Raw Compose Deployment')"
                    :helper="$rawComposeHelper"
                    canGate="update" :canResource="$application" />
                <x-forms.checkbox instantSave id="isConnectToDockerNetworkEnabled" :label="__('Connect To Predefined Network')"
                    :helper="$connectPredefinedNetworkHelper"
                    canGate="update" :canResource="$application" />
            @endif

            <h3 class="pt-4">{{ __('Proxy') }}</h3>
            @if ($application->settings->is_container_label_readonly_enabled)
                <x-forms.checkbox
                    :helper="__('Your application will be available only on https if your domain starts with https://...')"
                    instantSave id="isForceHttpsEnabled" :label="__('Force Https')" canGate="update" :canResource="$application" />
                <x-forms.checkbox :label="__('Enable Gzip Compression')"
                    :helper="__('You can disable gzip compression if you want. Some services are compressing data by default. In this case, you do not need this.')"
                    instantSave id="isGzipEnabled" canGate="update" :canResource="$application" />
                <x-forms.checkbox :helper="__('Strip Prefix is used to remove prefixes from paths. Like /api/ to /api.')"
                    instantSave id="isStripprefixEnabled" :label="__('Strip Prefixes')" canGate="update" :canResource="$application" />
            @else
                <x-forms.checkbox disabled
                    :helper="__('Readonly labels are disabled. You need to set the labels in the labels section.')" instantSave
                    id="isForceHttpsEnabled" :label="__('Force Https')" canGate="update" :canResource="$application" />
                <x-forms.checkbox :label="__('Enable Gzip Compression')" disabled
                    :helper="__('Readonly labels are disabled. You need to set the labels in the labels section.')" instantSave
                    id="isGzipEnabled" canGate="update" :canResource="$application" />
                <x-forms.checkbox
                    :helper="__('Readonly labels are disabled. You need to set the labels in the labels section.')" disabled
                    instantSave id="isStripprefixEnabled" :label="__('Strip Prefixes')" canGate="update" :canResource="$application" />
            @endif
            <h3 class="pt-4">{{ __('Operations') }}</h3>
            <form class="flex items-end gap-2" wire:submit.prevent='saveStopGracePeriod'>
                <x-forms.input
                    type="number"
                    id="stopGracePeriod"
                    :label="__('Stop Grace Period (seconds)')"
                    placeholder="{{ DEFAULT_STOP_GRACE_PERIOD_SECONDS }}"
                    :helper="__('How long to wait for graceful shutdown during rolling updates, manual stops, and restarts. Applies to all containers for this application. Default: :default seconds. Range: :min-:max seconds (1 hour).', ['default' => DEFAULT_STOP_GRACE_PERIOD_SECONDS, 'min' => MIN_STOP_GRACE_PERIOD_SECONDS, 'max' => MAX_STOP_GRACE_PERIOD_SECONDS])"
                    min="{{ MIN_STOP_GRACE_PERIOD_SECONDS }}"
                    max="{{ MAX_STOP_GRACE_PERIOD_SECONDS }}"
                    canGate="update"
                    :canResource="$application"
                />
                <x-forms.button canGate="update" :canResource="$application" type="submit">{{ __('Save') }}</x-forms.button>
            </form>
            <h3 class="pt-4">{{ __('Logs') }}</h3>
            <x-forms.checkbox :helper="__('Drain logs to your configured log drain endpoint in your Server settings.')"
                instantSave id="isLogDrainEnabled" :label="__('Drain Logs')" canGate="update" :canResource="$application" />
        </div>

    </div>
    <form wire:submit="submit" class="flex flex-col gap-2">
        @if ($application->build_pack !== 'dockercompose')
            <div class="flex gap-2 items-end pt-4">
                    <h3>{{ __('GPU') }}</h3>
                    @if ($isGpuEnabled)
                        <x-forms.button canGate="update" :canResource="$application" type="submit">{{ __('Save') }}</x-forms.button>
                    @endif
                </div>
        @endif
        @if ($application->build_pack !== 'dockercompose')
            <div class="md:w-96 pb-4">
                <x-forms.checkbox
                    :helper="$enableGpuHelper"
                    instantSave id="isGpuEnabled" :label="__('Enable GPU')" canGate="update" :canResource="$application" />
            </div>
        @endif
        @if ($isGpuEnabled)
            <div class="flex flex-col w-full gap-2 ">
                <div class="flex gap-2 items-end">
                    <x-forms.input :label="__('GPU Driver')" id="gpuDriver" canGate="update" :canResource="$application">
                    </x-forms.input>
                    <x-forms.input :label="__('GPU Count')" placeholder="{{ __('empty means use all GPUs') }}" id="gpuCount"
                        canGate="update" :canResource="$application">
                    </x-forms.input>
                </div>
                <x-forms.input :label="__('GPU Device Ids')" placeholder="0,2"
                    :helper="$gpuDeviceIdsHelper"
                    id="gpuDeviceIds" canGate="update" :canResource="$application"> </x-forms.input>
                <x-forms.textarea rows="10" :label="__('GPU Options')" id="gpuOptions" canGate="update"
                    :canResource="$application"> </x-forms.textarea>
            </div>
        @endif
    </form>
</div>
