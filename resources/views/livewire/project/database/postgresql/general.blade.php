<div>
    <dialog id="newInitScript" class="modal">
        <form method="dialog" class="flex flex-col gap-2 rounded-sm modal-box" wire:submit='save_new_init_script'>
            <h3 class="text-lg font-bold">{{ __('Add Init Script') }}</h3>
            <x-forms.input placeholder="create_test_db.sql" id="new_filename" :label="__('Filename')" required />
            <x-forms.textarea placeholder="CREATE DATABASE test;" id="new_content" :label="__('Content')" required />
            <x-forms.button onclick="newInitScript.close()" type="submit">
                {{ __('Save') }}
            </x-forms.button>
        </form>
        <form method="dialog" class="modal-backdrop">
            <button>{{ __('Close') }}</button>
        </form>
    </dialog>

    <form wire:submit="submit" class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <h2>{{ __('General') }}</h2>
            <x-forms.button type="submit" canGate="update" :canResource="$database">
                {{ __('Save') }}
            </x-forms.button>
        </div>
        <div class="flex flex-wrap gap-2 sm:flex-nowrap">
            <x-forms.input :label="__('Name')" id="name" canGate="update" :canResource="$database" />
            <x-forms.input :label="__('Description')" id="description" canGate="update" :canResource="$database" />
            <x-forms.input :label="__('Image')" id="image" required canGate="update" :canResource="$database"
                :helper="__('For all available images, check here:<br><br><a target=\'_blank\' href=\'https://hub.docker.com/_/postgres\'>https://hub.docker.com/_/postgres</a>')" />
        </div>
        <div class="pt-2 dark:text-warning">{{ __('If you change the values in the database, please sync it here, otherwise automations (like backups) won\'t work.') }}
        </div>
        @if ($database->started_at)
            <div class="flex xl:flex-row flex-col gap-2">
                <x-forms.input :label="__('Username')" id="postgresUser" :placeholder="__('If empty: postgres')" canGate="update"
                    :canResource="$database"
                    :helper="__('If you change this in the database, please sync it here, otherwise automations (like backups) won\'t work.')" />
                <x-forms.input :label="__('Password')" id="postgresPassword" type="password" required canGate="update"
                    :canResource="$database"
                    :helper="__('If you change this in the database, please sync it here, otherwise automations (like backups) won\'t work.')" />
                <x-forms.input :label="__('Initial Database')" id="postgresDb"
                    :placeholder="__('If empty, it will be the same as Username.')" readonly
                    :helper="__('You can only change this in the database.')" />
            </div>
        @else
            <div class="flex xl:flex-row flex-col gap-2 pb-2">
                <x-forms.input :label="__('Username')" id="postgresUser" :placeholder="__('If empty: postgres')" canGate="update"
                    :canResource="$database" />
                <x-forms.input :label="__('Password')" id="postgresPassword" type="password" required canGate="update"
                    :canResource="$database" />
                <x-forms.input :label="__('Initial Database')" id="postgresDb"
                    :placeholder="__('If empty, it will be the same as Username.')" canGate="update" :canResource="$database" />
            </div>
        @endif
        <div class="flex gap-2">
            <x-forms.input :label="__('Initial Database Arguments')" canGate="update" :canResource="$database" id="postgresInitdbArgs"
                :placeholder="__('If empty, use default. See in docker docs.')" />
            <x-forms.input :label="__('Host Auth Method')" canGate="update" :canResource="$database" id="postgresHostAuthMethod"
                :placeholder="__('If empty, use default. See in docker docs.')" />
        </div>
        <x-forms.input
            :helper="__('You can add custom docker run options that will be used when your container is started.<br>Note: Not all options are supported, as they could mess up Coolify\'s automation and could cause bad experience for users.<br><br>Check the <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/knowledge-base/docker/custom-commands\'>docs.</a>')"
            placeholder="--cap-add SYS_ADMIN --device=/dev/fuse --security-opt apparmor:unconfined --ulimit nofile=1024:1024 --tmpfs /run:rw,noexec,nosuid,size=65536k"
            id="customDockerRunOptions" :label="__('Custom Docker Options')" canGate="update" :canResource="$database" />
        <div class="flex flex-col gap-2">
            <h3 class="py-2">{{ __('Network') }}</h3>
            <div class="flex items-end gap-2">
                <x-forms.input placeholder="3000:5432" id="portsMappings" :label="__('Ports Mappings')"
                    :helper="__('A comma separated list of ports you would like to map to the host system.<br><span class=\'inline-block font-bold dark:text-warning\'>Example</span>3000:5432,3002:5433')"
                    canGate="update" :canResource="$database" />
            </div>

            <x-forms.input :label="__('Postgres URL (internal)')"
                :helper="__('If you change the user/password/port, this could be different. This is with the default values.')"
                type="password" readonly wire:model="db_url" />
            @if ($db_url_public)
                <x-forms.input :label="__('Postgres URL (public)')"
                    :helper="__('If you change the user/password/port, this could be different. This is with the default values.')"
                    type="password" readonly wire:model="db_url_public" />
            @endif
        </div>
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2 py-2">
                <h3>{{ __('SSL Configuration') }}</h3>
                @if ($enableSsl && $certificateValidUntil)
                    <x-modal-confirmation :title="__('Regenerate SSL Certificates')" :buttonTitle="__('Regenerate SSL Certificates')"
                        :actions="[
                            __('The SSL certificate of this database will be regenerated.'),
                            __('You must restart the database after regenerating the certificate to start using the new certificate.'),
                        ]" submitAction="regenerateSslCertificate" :confirmWithText="false"
                        :confirmWithPassword="false" />
                @endif
            </div>
            @if ($enableSsl && $certificateValidUntil)
                <span class="text-sm">{{ __('Valid until:') }}
                    @if (now()->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} - {{ __('Expired') }}</span>
                    @elseif(now()->addDays(30)->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} - {{ __('Expiring soon') }}</span>
                    @else
                        <span>{{ $certificateValidUntil->format('d.m.Y H:i:s') }}</span>
                    @endif
                </span>
            @endif
        </div>
        <div class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <div class="w-64" wire:key='enable_ssl'>
                    @if ($database->isExited())
                        <x-forms.checkbox id="enableSsl" :label="__('Enable SSL')" wire:model.live="enableSsl"
                            instantSave="instantSaveSSL" canGate="update" :canResource="$database" />
                    @else
                        <x-forms.checkbox id="enableSsl" :label="__('Enable SSL')" wire:model.live="enableSsl"
                            instantSave="instantSaveSSL" disabled
                            :helper="__('Database should be stopped to change this settings.')" />
                    @endif
                </div>
                @if ($enableSsl)
                    <div class="mx-2">
                        @if ($database->isExited())
                            <x-forms.select id="sslMode" :label="__('SSL Mode')" wire:model.live="sslMode"
                                instantSave="instantSaveSSL"
                                :helper="__('Choose the SSL verification mode for PostgreSQL connections')" canGate="update"
                                :canResource="$database">
                                <option value="allow" title="{{ __('Allow insecure connections') }}">{{ __('allow (insecure)') }}</option>
                                <option value="prefer" title="{{ __('Prefer secure connections') }}">{{ __('prefer (secure)') }}</option>
                                <option value="require" title="{{ __('Require secure connections') }}">{{ __('require (secure)') }}</option>
                                <option value="verify-ca" title="{{ __('Verify CA certificate') }}">{{ __('verify-ca (secure)') }}</option>
                                <option value="verify-full" title="{{ __('Verify full certificate') }}">{{ __('verify-full (secure)') }}
                                </option>
                            </x-forms.select>
                        @else
                            <x-forms.select id="sslMode" :label="__('SSL Mode')" instantSave="instantSaveSSL" disabled
                                :helper="__('Database should be stopped to change this settings.')">
                                <option value="allow" title="{{ __('Allow insecure connections') }}">{{ __('allow (insecure)') }}</option>
                                <option value="prefer" title="{{ __('Prefer secure connections') }}">{{ __('prefer (secure)') }}</option>
                                <option value="require" title="{{ __('Require secure connections') }}">{{ __('require (secure)') }}</option>
                                <option value="verify-ca" title="{{ __('Verify CA certificate') }}">{{ __('verify-ca (secure)') }}</option>
                                <option value="verify-full" title="{{ __('Verify full certificate') }}">{{ __('verify-full (secure)') }}
                                </option>
                            </x-forms.select>
                        @endif
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2 py-2">
                        <h3>{{ __('Proxy') }}</h3>
                        <x-loading wire:loading wire:target="instantSave" />
                        @if (data_get($database, 'is_public'))
                            <x-slide-over fullScreen>
                                <x-slot:title>{{ __('Proxy Logs') }}</x-slot:title>
                                <x-slot:content>
                                    <livewire:project.shared.get-logs :server="$server" :resource="$database"
                                        container="{{ data_get($database, 'uuid') }}-proxy" :collapsible="false" lazy />
                                </x-slot:content>
                                <x-forms.button disabled="{{ !data_get($database, 'is_public') }}"
                                    @click="slideOverOpen=true">{{ __('Logs') }}</x-forms.button>
                            </x-slide-over>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2 w-64">
                        <x-forms.checkbox instantSave id="isPublic" :label="__('Make it publicly available')"
                            canGate="update" :canResource="$database" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <x-forms.input type="number" placeholder="5432" disabled="{{ $isPublic }}" id="publicPort"
                            :label="__('Public Port')" canGate="update" :canResource="$database" />
                        <x-forms.input type="number" placeholder="3600" disabled="{{ $isPublic }}" id="publicPortTimeout"
                            :label="__('Proxy Timeout (seconds)')" :helper="__('Timeout for the public TCP proxy connection in seconds. Default: 3600 (1 hour).')" canGate="update" :canResource="$database" />
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <x-forms.textarea :label="__('Custom PostgreSQL Configuration')" rows="10" id="postgresConf"
                        canGate="update" :canResource="$database" />
                </div>
            </div>
        </div>
    </form>

    <div class="flex flex-col gap-4 pt-4">
        <h3>{{ __('Advanced') }}</h3>
        <div class="flex flex-col">
            <x-forms.checkbox :helper="__('Drain logs to your configured log drain endpoint in your Server settings.')"
                instantSave="instantSaveAdvanced" id="isLogDrainEnabled" :label="__('Drain Logs')" canGate="update"
                :canResource="$database" />
        </div>

        <div class="pb-16">
            <div class="flex items-center gap-2 pb-2">

                <h3>{{ __('Initialization scripts') }}</h3>
                @can('update', $database)
                    <x-modal-input :buttonTitle="__('+ Add')" :title="__('New Init Script')">
                        <form class="flex flex-col w-full gap-2 rounded-sm" wire:submit='save_new_init_script'>
                            <x-forms.input placeholder="create_test_db.sql" id="new_filename" :label="__('Filename')"
                                required />
                            <x-forms.textarea rows="20" placeholder="CREATE DATABASE test;" id="new_content"
                                :label="__('Content')" required />
                            <x-forms.button type="submit">
                                {{ __('Save') }}
                            </x-forms.button>
                        </form>
                    </x-modal-input>
                @endcan
            </div>
            <div class="flex flex-col gap-2">
                @forelse($initScripts ?? [] as $script)
                    <livewire:project.database.init-script :script="$script" :wire:key="$script['index']" />
                @empty
                    <div>{{ __('No initialization scripts found.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
