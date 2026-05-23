<div class="w-full">
    <form class="flex flex-col gap-2 {{ $modal_mode ? 'w-full' : '' }}" wire:submit='addToken'>
        @if ($modal_mode)
            {{-- Modal layout: vertical, compact --}}
            @if (!isset($provider) || empty($provider) || $provider === '')
                <x-forms.select required id="provider" :label="__('Provider')">
                    <option value="hetzner">Hetzner</option>
                    <option value="digitalocean">DigitalOcean</option>
                </x-forms.select>
            @else
                <input type="hidden" wire:model="provider" />
            @endif

            <x-forms.input required id="name" :label="__('Token Name')"
                :placeholder="__('e.g., Production Hetzner. Tip: add the Hetzner project name so it is easier to identify.')" />

            <x-forms.input required type="password" id="token" :label="__('API Token')"
                :placeholder="__('Enter your API token')" />

            @if (auth()->user()->currentTeam()->cloudProviderTokens->where('provider', $provider)->isEmpty())
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    {!! __('Create an API token in the <a href=":url" target="_blank" class="underline dark:text-white">:provider Console</a> and then choose Project → Security → API Tokens.', [
                        'url' => $provider === 'hetzner' ? 'https://console.hetzner.com/projects' : '#',
                        'provider' => ucfirst($provider),
                    ]) !!}
                    @if ($provider === 'hetzner')
                        <br><br>
                        {!! __('Do not have a Hetzner account? <a href=":url" target="_blank" class="underline dark:text-white">Sign up here</a>', ['url' => 'https://coolify.io/hetzner']) !!}
                        <br>
                        <span class="text-xs">{{ __('Coolify affiliate link. Only works for new accounts, supports us with €10, and gives you €20.') }}</span>
                    @endif
                </div>
            @endif

            <x-forms.button type="submit">{{ __('Validate & Add Token') }}</x-forms.button>
        @else
            {{-- Full page layout: horizontal, spacious --}}
            <div class="flex gap-2 items-end flex-wrap">
                <div class="w-64">
                    <x-forms.select required id="provider" :label="__('Provider')" disabled>
                        <option value="hetzner" selected>Hetzner</option>
                        <option value="digitalocean">DigitalOcean</option>
                    </x-forms.select>
                </div>
                <div class="flex-1 min-w-64">
                    <x-forms.input required id="name" :label="__('Token Name')"
                        :placeholder="__('e.g., Production Hetzner. Tip: add the Hetzner project name so it is easier to identify.')" />
                </div>
            </div>
            <div class="flex-1 min-w-64">
                <x-forms.input required type="password" id="token" :label="__('API Token')"
                    :placeholder="__('Enter your API token')" />
                @if (auth()->user()->currentTeam()->cloudProviderTokens->where('provider', $provider)->isEmpty())
                    <div class="text-sm text-neutral-500 dark:text-neutral-400 mt-2">
                        {!! __('Create an API token in the <a href=":url" target="_blank" class="underline dark:text-white">Hetzner Console</a> and then choose Project → Security → API Tokens.', ['url' => 'https://console.hetzner.com/projects']) !!}
                        <br><br>
                        {!! __('Do not have a Hetzner account? <a href=":url" target="_blank" class="underline dark:text-white">Sign up here</a>', ['url' => 'https://coolify.io/hetzner']) !!}
                        <br>
                        <span class="text-xs">{{ __('Coolify affiliate link. Only works for new accounts, supports us with €10, and gives you €20.') }}</span>
                    </div>
                @endif
            </div>
            <x-forms.button type="submit">{{ __('Validate & Add Token') }}</x-forms.button>
        @endif
    </form>
</div>
