<form class="flex flex-col w-full gap-2 rounded-sm" wire:submit='submit'
    x-data="{ isMultiline: $wire.entangle('is_multiline') }">
    @php
        $literalHelper = __('This means that when you use $VARIABLES in a value, it should be interpreted as the actual characters \'$VARIABLES\' and not as the value of a variable named VARIABLE.<br><br>Useful if you have $ sign in your value and there are some characters after it, but you would not like to interpolate it from another value. In this case, you should set this to true.');
    @endphp
    <x-forms.input placeholder="NODE_ENV" id="key" :label="__('Name')" required />
    <template x-if="isMultiline">
        <div wire:key="env-value-textarea">
            <x-forms.textarea id="value" :label="__('Value')" required class="font-sans" spellcheck />
        </div>
    </template>
    <template x-if="!isMultiline">
        <div wire:key="env-value-input">
            <x-forms.env-var-input placeholder="production" id="value" :label="__('Value')" required
                :availableVars="$shared ? [] : $this->availableSharedVariables"
                :projectUuid="data_get($parameters, 'project_uuid')"
                :environmentUuid="data_get($parameters, 'environment_uuid')"
                :serverUuid="data_get($parameters, 'server_uuid')" />
        </div>
    </template>

    @if (!$shared)
        <div x-cloak x-show="!isMultiline" wire:key="env-value-tip" class="text-xs text-neutral-500 dark:text-neutral-400 -mt-1">
            {{ __('Tip: Type') }} <span class="font-mono dark:text-warning text-coollabs">&#123;&#123;</span> {{ __('to reference a shared environment variable') }}
        </div>
    @endif

    <x-forms.input id="comment" :label="__('Comment')"
        :helper="__('Add a note to document what this environment variable is used for.')" maxlength="256" />

    @if (!$shared)
        <x-forms.checkbox id="is_buildtime"
            :helper="__('Make this variable available during Docker build process. Useful for build secrets and dependencies.')"
            :label="__('Available at Buildtime')" />

        <x-environment-variable-warning :problematic-variables="$problematicVariables" />

        <x-forms.checkbox id="is_runtime" :helper="__('Make this variable available in the running container at runtime.')"
            :label="__('Available at Runtime')" />
        <x-forms.checkbox id="is_literal"
            :helper="$literalHelper"
            :label="__('Is Literal?')" />
    @endif

    <x-forms.checkbox id="is_multiline" x-model="isMultiline" :label="__('Is Multiline?')" />
    <x-forms.button type="submit" @click="slideOverOpen=false">
        {{ __('Save') }}
    </x-forms.button>
</form>
