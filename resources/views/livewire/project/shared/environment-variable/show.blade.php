<div>
    @php
        $deleteEnvTitle = __('Confirm Environment Variable Deletion?');
        $deleteEnvAction = __('The selected environment variable will be permanently deleted.');
        $deleteEnvConfirmLabel = __('Please confirm the execution of the actions by entering the Environment Variable Name below');
        $deleteEnvShortLabel = __('Environment Variable Name');
        $deleteEnvStep2 = __('Permanently Delete');
        $commentLabel = __('Comment');
        $commentHelper = __('Add a note to document what this environment variable is used for.');
        $commentReadonlyHelper = __('Documentation for this environment variable.');
        $buildtimeLabel = __('Available at Buildtime');
        $buildtimeHelper = __('Make this variable available during Docker build process. Useful for build secrets and dependencies.');
        $runtimeLabel = __('Available at Runtime');
        $runtimeHelper = __('Make this variable available in the running container at runtime.');
        $multilineLabel = __('Is Multiline?');
        $literalLabel = __('Is Literal?');
        $literalHelper = __('This means that when you use $VARIABLES in a value, it should be interpreted as the actual characters \'$VARIABLES\' and not as the value of a variable named VARIABLE.<br><br>Useful if you have $ sign in your value and there are some characters after it, but you would not like to interpolate it from another value. In this case, you should set this to true.');
        $magicVariablePlaceholder = __('This env cannot be edited manually, it is handled by Coolify.');
    @endphp
    <form wire:submit='submit' @class([
        'flex flex-col items-center gap-4 p-4 bg-white border lg:items-start dark:bg-base',
        'border-error' => $is_really_required,
        'dark:border-coolgray-300 border-neutral-200' => !$is_really_required,
    ])>
        @if ($isLocked)
            <div class="flex flex-1 w-full gap-2">
                <x-forms.input disabled id="key" />
                <svg class="icon  my-1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M5 13a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6z" />
                        <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0-2 0m-3-5V7a4 4 0 1 1 8 0v4" />
                    </g>
                </svg>
                @can('delete', $this->env)
                    <x-modal-confirmation :title="$deleteEnvTitle" isErrorButton :buttonTitle="__('Delete')"
                        submitAction="delete" :actions="[$deleteEnvAction]"
                        confirmationText="{{ $env->key }}"
                        :confirmationLabel="$deleteEnvConfirmLabel"
                        :shortConfirmationLabel="$deleteEnvShortLabel" :confirmWithPassword="false"
                        :step2ButtonText="$deleteEnvStep2" />
                @endcan
            </div>
            @can('update', $this->env)
                <div class="flex flex-col w-full gap-2 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <x-forms.input id="comment" :label="$commentLabel"
                            placeholder="{{ $isMagicVariable ? $magicVariablePlaceholder : '' }}"
                            :helper="$commentHelper" maxlength="256" />
                    </div>
                    <x-forms.button type="submit">{{ __('Update') }}</x-forms.button>
                </div>
                <div class="flex flex-col w-full gap-3">
                    <div class="flex flex-wrap w-full items-center gap-4">
                        @if (!$is_redis_credential)
                            @if ($type === 'service')
                                @if (!$isMagicVariable)
                                    <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                    <x-forms.checkbox instantSave id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @endif
                            @else
                                @if ($is_shared)
                                    <x-forms.checkbox instantSave id="is_buildtime"
                                        :helper="$buildtimeHelper"
                                        :label="$buildtimeLabel" />
                                    <x-forms.checkbox instantSave id="is_runtime"
                                        :helper="$runtimeHelper"
                                        :label="$runtimeLabel" />
                                    <x-forms.checkbox instantSave id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @else
                                    @if ($isSharedVariable)
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                        @endif
                                    @else
                                        @if (!$env->is_buildpack_control)
                                            <x-forms.checkbox instantSave id="is_buildtime"
                                                :helper="$buildtimeHelper"
                                                :label="$buildtimeLabel" />
                                        @endif
                                        <x-forms.checkbox instantSave id="is_runtime"
                                            :helper="$runtimeHelper"
                                            :label="$runtimeLabel" />
                                        @if (!$isMagicVariable)
                                            @if (!$env->is_buildpack_control)
                                                <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                                @if ($is_multiline === false)
                                                    <x-forms.checkbox instantSave id="is_literal"
                                                        :helper="$literalHelper"
                                                        :label="$literalLabel" />
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col w-full gap-3">
                    <div class="flex flex-wrap w-full items-center gap-4">
                        @if (!$is_redis_credential)
                            @if ($type === 'service')
                                @if (!$isMagicVariable)
                                    <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                    <x-forms.checkbox disabled id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @endif
                            @else
                                @if ($is_shared)
                                    <x-forms.checkbox disabled id="is_buildtime"
                                        :helper="$buildtimeHelper"
                                        :label="$buildtimeLabel" />
                                    <x-forms.checkbox disabled id="is_runtime"
                                        :helper="$runtimeHelper"
                                        :label="$runtimeLabel" />
                                    <x-forms.checkbox disabled id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @else
                                    @if ($isSharedVariable)
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                        @endif
                                    @else
                                        <x-forms.checkbox disabled id="is_buildtime"
                                            :helper="$buildtimeHelper"
                                            :label="$buildtimeLabel" />
                                        <x-forms.checkbox disabled id="is_runtime"
                                            :helper="$runtimeHelper"
                                            :label="$runtimeLabel" />
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                            @if ($is_multiline === false)
                                                <x-forms.checkbox disabled id="is_literal"
                                                    :helper="$literalHelper"
                                                    :label="$literalLabel" />
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
                <div class="w-full">
                    <x-forms.input disabled id="comment" :label="$commentLabel" :helper="$commentReadonlyHelper"
                        maxlength="256" />
                </div>
            @endcan
        @else
            @can('update', $this->env)
                @if ($isDisabled)
                    <div class="flex flex-col w-full gap-2">
                        <div class="flex flex-col w-full gap-2 lg:flex-row">
                            <x-forms.input disabled id="key" />
                            <x-forms.env-var-input
                                disabled
                                type="password"
                                id="value"
                                :availableVars="$isSharedVariable ? [] : $this->availableSharedVariables"
                                :projectUuid="data_get($parameters, 'project_uuid')"
                                :environmentUuid="data_get($parameters, 'environment_uuid')"
                                :serverUuid="data_get($parameters, 'server_uuid')" />
                            @if ($is_shared)
                                <x-forms.input disabled type="password" id="real_value" />
                            @endif
                        </div>
                        <x-forms.input instantSave id="comment" :label="$commentLabel"
                            placeholder="{{ $isMagicVariable ? $magicVariablePlaceholder : '' }}"
                            :helper="$commentHelper" maxlength="256" />
                    </div>
                @else
                    <div class="flex flex-col w-full gap-2">
                        <div class="flex flex-col w-full gap-2 lg:flex-row">
                            @if ($is_multiline)
                                <x-forms.input :required="$is_redis_credential" isMultiline="{{ $is_multiline }}" id="key" />
                                <div class="flex-1" wire:key="env-show-value-textarea-{{ $env->id }}">
                                    <x-forms.textarea :required="$is_redis_credential" type="password" id="value" />
                                </div>
                            @else
                                <x-forms.input :disabled="$is_redis_credential" :required="$is_redis_credential" id="key" />
                                <div class="w-full" wire:key="env-show-value-input-{{ $env->id }}">
                                    <x-forms.env-var-input
                                        :required="$is_redis_credential"
                                        type="password"
                                        id="value"
                                        :availableVars="$isSharedVariable ? [] : $this->availableSharedVariables"
                                        :projectUuid="data_get($parameters, 'project_uuid')"
                                        :environmentUuid="data_get($parameters, 'environment_uuid')"
                                        :serverUuid="data_get($parameters, 'server_uuid')" />
                                </div>
                            @endif
                            @if ($is_shared)
                                <x-forms.input :disabled="$is_redis_credential" :required="$is_redis_credential" disabled
                                    type="password" id="real_value" />
                            @endif
                        </div>
                        <x-forms.input instantSave id="comment" :label="$commentLabel"
                            :helper="$commentHelper" maxlength="256" />
                    </div>
                @endif
            @else
                <div class="flex flex-col w-full gap-2">
                    <div class="flex flex-col w-full gap-2 lg:flex-row">
                        <x-forms.input disabled id="key" />
                        <x-forms.env-var-input
                            disabled
                            type="password"
                            id="value"
                            :availableVars="$isSharedVariable ? [] : $this->availableSharedVariables"
                            :projectUuid="data_get($parameters, 'project_uuid')"
                            :environmentUuid="data_get($parameters, 'environment_uuid')"
                            :serverUuid="data_get($parameters, 'server_uuid')" />
                        @if ($is_shared)
                            <x-forms.input disabled type="password" id="real_value" />
                        @endif
                    </div>
                    <x-forms.input disabled id="comment" :label="$commentLabel"
                        placeholder="{{ $isMagicVariable ? $magicVariablePlaceholder : '' }}"
                        :helper="$commentHelper" maxlength="256" />
                </div>
            @endcan
            @can('update', $this->env)
                <div class="flex flex-col w-full gap-3">
                    <div class="flex flex-wrap w-full items-center gap-4">
                        @if (!$is_redis_credential)
                            @if ($type === 'service')
                                @if (!$isMagicVariable)
                                    <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                    <x-forms.checkbox instantSave id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @endif
                            @else
                                @if ($is_shared)
                                    <x-forms.checkbox instantSave id="is_buildtime"
                                        :helper="$buildtimeHelper"
                                        :label="$buildtimeLabel" />
                                    <x-forms.checkbox instantSave id="is_runtime"
                                        :helper="$runtimeHelper"
                                        :label="$runtimeLabel" />
                                    <x-forms.checkbox instantSave id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @else
                                    @if ($isSharedVariable)
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                        @endif
                                    @else
                                        @if (!$env->is_buildpack_control)
                                            <x-forms.checkbox instantSave id="is_buildtime"
                                                :helper="$buildtimeHelper"
                                                :label="$buildtimeLabel" />
                                        @endif
                                        <x-forms.checkbox instantSave id="is_runtime"
                                            :helper="$runtimeHelper"
                                            :label="$runtimeLabel" />
                                        @if (!$isMagicVariable)
                                            @if (!$env->is_buildpack_control)
                                                <x-forms.checkbox instantSave id="is_multiline" :label="$multilineLabel" />
                                                @if ($is_multiline === false)
                                                    <x-forms.checkbox instantSave id="is_literal"
                                                        :helper="$literalHelper"
                                                        :label="$literalLabel" />
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                    <x-environment-variable-warning :problematic-variables="$problematicVariables" />
                    @if (!$isMagicVariable)
                        <div class="flex w-full justify-end gap-2">
                            @if ($isDisabled)
                            <x-forms.button disabled type="submit">{{ __('Update') }}</x-forms.button>
                            <x-forms.button wire:click='lock'>{{ __('Lock') }}</x-forms.button>
                            <x-modal-confirmation :title="$deleteEnvTitle" isErrorButton :buttonTitle="__('Delete')"
                                submitAction="delete" :actions="[$deleteEnvAction]"
                                confirmationText="{{ $key }}" buttonFullWidth="true"
                                :confirmationLabel="$deleteEnvConfirmLabel"
                                :shortConfirmationLabel="$deleteEnvShortLabel" :confirmWithPassword="false"
                                :step2ButtonText="$deleteEnvStep2" />
                        @else
                            <x-forms.button type="submit">{{ __('Update') }}</x-forms.button>
                            <x-forms.button wire:click='lock'>{{ __('Lock') }}</x-forms.button>
                            <x-modal-confirmation :title="$deleteEnvTitle" isErrorButton :buttonTitle="__('Delete')"
                                submitAction="delete" :actions="[$deleteEnvAction]"
                                confirmationText="{{ $key }}" buttonFullWidth="true"
                                :confirmationLabel="$deleteEnvConfirmLabel"
                                :shortConfirmationLabel="$deleteEnvShortLabel" :confirmWithPassword="false"
                                :step2ButtonText="$deleteEnvStep2" />
                            @endif
                        </div>
                    @elseif ($type === 'service')
                        <div class="flex w-full justify-end gap-2">
                            <x-forms.button wire:click='lock'>{{ __('Lock') }}</x-forms.button>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex flex-col w-full gap-3">
                    <div class="flex flex-wrap w-full items-center gap-4">
                        @if (!$is_redis_credential)
                            @if ($type === 'service')
                                @if (!$isMagicVariable)
                                    <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                    <x-forms.checkbox disabled id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @endif
                            @else
                                @if ($is_shared)
                                    <x-forms.checkbox disabled id="is_buildtime"
                                        :helper="$buildtimeHelper"
                                        :label="$buildtimeLabel" />
                                    <x-forms.checkbox disabled id="is_runtime"
                                        :helper="$runtimeHelper"
                                        :label="$runtimeLabel" />
                                    <x-forms.checkbox disabled id="is_literal"
                                        :helper="$literalHelper"
                                        :label="$literalLabel" />
                                @else
                                    @if ($isSharedVariable)
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                        @endif
                                    @else
                                        <x-forms.checkbox disabled id="is_buildtime"
                                            :helper="$buildtimeHelper"
                                            :label="$buildtimeLabel" />
                                        <x-forms.checkbox disabled id="is_runtime"
                                            :helper="$runtimeHelper"
                                            :label="$runtimeLabel" />
                                        @if (!$isMagicVariable)
                                            <x-forms.checkbox disabled id="is_multiline" :label="$multilineLabel" />
                                            @if ($is_multiline === false)
                                                <x-forms.checkbox disabled id="is_literal"
                                                    :helper="$literalHelper"
                                                    :label="$literalLabel" />
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            @endcan
        @endif

    </form>
</div>
