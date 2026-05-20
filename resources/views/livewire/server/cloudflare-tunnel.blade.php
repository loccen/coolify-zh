<div>
    <x-slot:title>
        {{ data_get_str($server, 'name')->limit(10) }} > {{ __('Cloudflare Tunnel') }} | Coolify
    </x-slot>
    <livewire:server.navbar :server="$server" />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-server.sidebar :server="$server" activeMenu="cloudflare-tunnel" />
        <div class="w-full">
            <div class="flex flex-col">
                <div class="flex gap-2 items-center">
                    <h2>{{ __('Cloudflare Tunnel') }}</h2>
                    <x-helper class="inline-flex"
                        :helper="__('If you are using Cloudflare Tunnel, enable this. It will proxy all SSH requests to your server through Cloudflare.<br> You then can close your server\\'s SSH port in the firewall of your hosting provider.<br><span class=\\'dark:text-warning\\'>If you choose manual configuration, Coolify does not install or set up Cloudflare (cloudflared) on your server.</span>')" />
                    @if ($isCloudflareTunnelsEnabled)
                        <span
                            class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded dark:text-green-100 dark:bg-green-800">
                            {{ __('Enabled') }}
                        </span>
                    @endif
                </div>
                <div>{{ __('Secure your servers with Cloudflare Tunnel.') }}</div>
            </div>
            <div class="flex flex-col gap-2 pt-6">
                @if ($isCloudflareTunnelsEnabled)
                    <div class="flex flex-col gap-4">
                        <x-callout type="warning" :title="__('Warning!')">
                            {{ __('If you disable Cloudflare Tunnel, you will need to update the server\\'s IP address back to its real IP address in the server \"General\" settings. The server may become inaccessible if the IP address is not updated correctly.') }}
                        </x-callout>
                        <div class="w-64">
                            @if ($server->ip_previous)
                                <x-modal-confirmation :title="__('Disable Cloudflare Tunnel?')"
                                    :buttonTitle="__('Disable Cloudflare Tunnel')" isErrorButton
                                    submitAction="toggleCloudflareTunnels" :actions="[
                                        __('Cloudflare Tunnel will be disabled for this server.'),
                                        __('The server IP address will be updated to its previous IP address.'),
                                    ]"
                                    confirmationText="DISABLE CLOUDFLARE TUNNEL"
                                    :confirmationLabel="__('Please type the confirmation text to disable Cloudflare Tunnel.')"
                                    :shortConfirmationLabel="__('Confirmation text')" />
                            @else
                                <x-modal-confirmation :title="__('Disable Cloudflare Tunnel?')"
                                    :buttonTitle="__('Disable Cloudflare Tunnel')" isErrorButton
                                    submitAction="toggleCloudflareTunnels" :actions="[
                                        __('Cloudflare Tunnel will be disabled for this server.'),
                                        __('You will need to update the server IP address to its real IP address.'),
                                        __('The server may become inaccessible if the IP address is not updated correctly.'),
                                        __('SSH access will revert to the standard port configuration.'),
                                    ]"
                                    confirmationText="DISABLE CLOUDFLARE TUNNEL"
                                    :confirmationLabel="__('Please type the confirmation text to disable Cloudflare Tunnel.')"
                                    :shortConfirmationLabel="__('Confirmation text')" />
                            @endif

                        </div>
                    </div>
                @elseif (!$server->isFunctional())
                    <x-callout type="info" :title="__('Configuration Options')" class="mb-4">
                        {{ __('To') }} <span class="font-semibold">{{ __('automatically') }}</span> {{ __('configure Cloudflare Tunnel, please validate your server first. Then you will need a Cloudflare token and an SSH domain configured.') }}
                        <br />
                        {{ __('To') }} <span class="font-semibold">{{ __('manually') }}</span> {{ __('configure Cloudflare Tunnel, please click') }} <span wire:click="manualCloudflareConfig" class="underline cursor-pointer">{{ __('here') }}</span>,
                        {{ __('then you should validate the server.') }}
                        <br /><br />
                        {{ __('For more information, please read our') }} <a
                            href="https://coolify.io/docs/knowledge-base/cloudflare/tunnels/server-ssh" target="_blank"
                            class="underline">{{ __('documentation') }}</a>.
                    </x-callout>
                @endif
                @if (!$isCloudflareTunnelsEnabled && $server->isFunctional())
                    <div class="flex  flex-col pb-2">
                        <h3>{{ __('Automated') }}</h3>
                        <a href="https://coolify.io/docs/knowledge-base/cloudflare/tunnels/server-ssh" target="_blank"
                            class="text-xs underline hover:text-warning-600 dark:hover:text-warning-200">{{ __('Docs') }}<x-external-link /></a>
                    </div>
                    <div class="flex gap-2">
                        <x-slide-over @automated.window="slideOverOpen = true" fullScreen>
                            <x-slot:title>{{ __('Cloudflare Tunnel Configuration') }}</x-slot:title>
                            <x-slot:content>
                                <livewire:activity-monitor header="Logs" fullHeight />
                            </x-slot:content>
                        </x-slide-over>
                        @can('update', $server)
                            <form @submit.prevent="$wire.dispatch('automatedCloudflareConfig')"
                                class="flex flex-col gap-2 w-full">
                                <x-forms.input id="cloudflare_token" required :label="__('Cloudflare Token')" type="password" />
                                <x-forms.input id="ssh_domain" :label="__('Configured SSH Domain')" required
                                    :helper="__('The SSH domain you configured in Cloudflare. Make sure there is no protocol like http(s):// so you provide a FQDN not a URL. <a class=\\'underline dark:text-white\\' href=\\'https://coolify.io/docs/knowledge-base/cloudflare/tunnels/server-ssh\\' target=\\'_blank\\'>Documentation</a>')" />
                                <x-forms.button type="submit" isHighlighted>{{ __('Continue') }}</x-forms.button>
                            </form>
                        @else
                            <x-callout type="warning" :title="__('Permission Required')" class="mb-4">
                                {{ __("You don't have permission to configure Cloudflare Tunnel for this server.") }}
                            </x-callout>
                        @endcan
                    </div>
                    @script
                        <script>
                            $wire.$on('automatedCloudflareConfig', () => {
                                try {
                                    window.dispatchEvent(new CustomEvent('automated'));
                                    $wire.$call('automatedCloudflareConfig');
                                } catch (error) {
                                    console.error(error);
                                }
                            });
                        </script>
                    @endscript
            </div>
            <h3 class="pt-6 pb-2">{{ __('Manual') }}</h3>
            <div class="pl-2">
                @can('update', $server)
                    <x-modal-confirmation buttonFullWidth :title="__('I manually configured Cloudflare Tunnel?')"
                        :buttonTitle="__('I manually configured Cloudflare Tunnel')" submitAction="manualCloudflareConfig"
                        :actions="[
                            __('You set everything up manually, including in Cloudflare and on the server (cloudflared is running).'),
                            __('If you missed something, the connection will not work.'),
                        ]" confirmationText="I manually configured Cloudflare Tunnel"
                        :confirmationLabel="__('Please type the confirmation text to confirm that you manually configured Cloudflare Tunnel.')"
                        :shortConfirmationLabel="__('Confirmation text')" />
                @else
                    <x-callout type="warning" :title="__('Permission Required')" class="mb-4">
                        {{ __("You don't have permission to configure Cloudflare Tunnel for this server.") }}
                    </x-callout>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
