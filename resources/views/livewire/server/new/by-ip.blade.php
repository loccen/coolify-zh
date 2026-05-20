<div class="w-full">
    @if ($limit_reached)
        <x-limit-reached name="servers" />
    @else
        <form class="flex flex-col w-full gap-2" wire:submit='submit'>
            <div class="flex w-full gap-2 flex-wrap sm:flex-nowrap">
                <x-forms.input id="name" :label="__('Name')" required />
                <x-forms.input id="description" :label="__('Description')" />
            </div>
            <div class="flex gap-2 flex-wrap sm:flex-nowrap">
                <x-forms.input id="ip" :label="__('IP Address/Domain')" required
                    :helper="__('An IP Address (127.0.0.1) or domain (example.com).')" />
                <x-forms.input type="number" id="port" :label="__('Port')" required />
            </div>
            <x-forms.input id="user" :label="__('User')" required />
            <div class="text-xs dark:text-warning text-coollabs ">{{ __('Non-root user is experimental:') }} <a
                    class="font-bold underline" target="_blank"
                    href="https://coolify.io/docs/knowledge-base/server/non-root-user">{{ __('docs') }}</a>.</div>
            <x-forms.select :label="__('Private Key')" id="private_key_id">
                <option disabled>{{ __('Select a private key') }}</option>
                @foreach ($private_keys as $key)
                    @if ($loop->first)
                        <option selected value="{{ $key->id }}">{{ $key->name }}</option>
                    @else
                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                    @endif
                @endforeach
            </x-forms.select>
            <div class="">
                <x-forms.checkbox instantSave type="checkbox" id="is_build_server"
                    :helper="__('Build servers are used to build your applications, so you cannot deploy applications to it.')"
                    :label="__('Use it as a build server?')" />
            </div>
            <x-forms.button type="submit">
                {{ __('Continue') }}
            </x-forms.button>
        </form>
    @endif
</div>
