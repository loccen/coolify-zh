<div>
    <form wire:submit="submit" class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <h2>{{ __('General') }}</h2>
            <x-forms.button type="submit" canGate="update" :canResource="$database">
                {{ __('Save') }}
            </x-forms.button>
        </div>
        <div class="flex gap-2">
            <x-forms.input :label="__('Name')" id="name" canGate="update" :canResource="$database" />
            <x-forms.input :label="__('Description')" id="description" canGate="update" :canResource="$database" />
            <x-forms.input :label="__('Image')" id="image" required canGate="update" :canResource="$database"
                :helper="__('For all available images, check here:<br><br><a target=\'_blank\' href=\'https://hub.docker.com/r/clickhouse/clickhouse-server/\'>https://hub.docker.com/r/clickhouse/clickhouse-server/</a>')" />
        </div>

        @if ($database->started_at)
            <div class="flex gap-2">
                <x-forms.input :label="__('Initial Username')" id="clickhouseAdminUser" placeholder="If empty: clickhouse"
                    readonly :helper="__('You can only change this in the database.')" canGate="update" :canResource="$database" />
                <x-forms.input :label="__('Initial Password')" id="clickhouseAdminPassword" type="password" required readonly
                    :helper="__('You can only change this in the database.')" canGate="update" :canResource="$database" />
            </div>
        @else
            <div class=" dark:text-warning">{{ __('Please verify these values. You can only modify them before the initial start. After that, you need to modify it in the database.') }}
            </div>
            <div class="flex gap-2">
                <x-forms.input :label="__('Username')" id="clickhouseAdminUser" required canGate="update" :canResource="$database" />
                <x-forms.input :label="__('Password')" id="clickhouseAdminPassword" type="password" required canGate="update"
                    :canResource="$database" />
            </div>
        @endif
        <x-forms.input
            :helper="__('You can add custom docker run options that will be used when your container is started.<br>Note: Not all options are supported, as they could mess up Coolify\\'s automation and could cause bad experience for users.<br><br>Check the <a class=\\'underline dark:text-white\\' href=\\'https://coolify.io/docs/knowledge-base/docker/custom-commands\\'>docs.</a>')"
            placeholder="--cap-add SYS_ADMIN --device=/dev/fuse --security-opt apparmor:unconfined --ulimit nofile=1024:1024 --tmpfs /run:rw,noexec,nosuid,size=65536k"
            id="customDockerRunOptions" :label="__('Custom Docker Options')" canGate="update" :canResource="$database" />
        <div class="flex flex-col gap-2">
            <h3 class="py-2">{{ __('Network') }}</h3>
            <div class="flex items-end gap-2">
                <x-forms.input placeholder="3000:5432" id="portsMappings" :label="__('Ports Mappings')"
                    :helper="__('A comma separated list of ports you would like to map to the host system.<br><span class=\'inline-block font-bold dark:text-warning\'>Example</span>3000:5432,3002:5433')"
                    canGate="update" :canResource="$database" />
            </div>
            <x-forms.input :label="__('Clickhouse URL (internal)')"
                :helper="__('If you change the user/password/port, this could be different. This is with the default values.')"
                type="password" readonly wire:model="dbUrl" canGate="update" :canResource="$database" />
            @if ($dbUrlPublic)
                <x-forms.input :label="__('Clickhouse URL (public)')"
                    :helper="__('If you change the user/password/port, this could be different. This is with the default values.')"
                    type="password" readonly wire:model="dbUrlPublic" canGate="update" :canResource="$database" />
            @else
                <x-forms.input :label="__('Clickhouse URL (public)')"
                    :helper="__('If you change the user/password/port, this could be different. This is with the default values.')"
                    readonly :value="__('Starting the database will generate this.')" canGate="update" :canResource="$database" />
            @endif
        </div>
            <div class="flex flex-col py-2 w-64">
                <div class="flex items-center gap-2 pb-2">
                    <div class="flex items-center">
                        <h3>{{ __('Proxy') }}</h3>
                        <x-loading wire:loading wire:target="instantSave" />
                    </div>
                    @if ($isPublic)
                        <x-slide-over fullScreen>
                            <x-slot:title>{{ __('Proxy Logs') }}</x-slot:title>
                            <x-slot:content>
                                <livewire:project.shared.get-logs :server="$server" :resource="$database"
                                    container="{{ data_get($database, 'uuid') }}-proxy" :collapsible="false" lazy />
                            </x-slot:content>
                            <x-forms.button disabled="{{ !$isPublic }}"
                                @click="slideOverOpen=true">{{ __('Logs') }}</x-forms.button>
                        </x-slide-over>
                    @endif
                </div>
                <x-forms.checkbox instantSave id="isPublic" :label="__('Make it publicly available')" canGate="update"
                    :canResource="$database" />
            </div>
            <div class="flex flex-col gap-2">
            <x-forms.input type="number" placeholder="5432" disabled="{{ $isPublic }}" id="publicPort" :label="__('Public Port')"
                canGate="update" :canResource="$database" />
            <x-forms.input type="number" placeholder="3600" disabled="{{ $isPublic }}" id="publicPortTimeout"
                :label="__('Proxy Timeout (seconds)')" :helper="__('Timeout for the public TCP proxy connection in seconds. Default: 3600 (1 hour).')" canGate="update" :canResource="$database" />
            </div>
    </form>
    <h3 class="pt-4">{{ __('Advanced') }}</h3>
    <div class="w-64">
        <x-forms.checkbox :helper="__('Drain logs to your configured log drain endpoint in your Server settings.')"
            instantSave="instantSaveAdvanced" id="isLogDrainEnabled" :label="__('Drain Logs')" canGate="update"
            :canResource="$database" />
    </div>
</div>
