<form wire:submit='submit' class="flex flex-col">
    <div class="flex items-center gap-2">
        <h2>{{ __('Healthchecks') }}</h2>
        <x-forms.button canGate="update" :canResource="$resource" type="submit">{{ __('Save') }}</x-forms.button>
        @if (!$healthCheckEnabled)
            <x-modal-confirmation :title="__('Confirm Healthcheck Enable?')" :buttonTitle="__('Enable Healthcheck')"
                submitAction="toggleHealthcheck" :actions="[__('Enable healthcheck for this resource.')]"
                :warningMessage="__('If the health check fails, your application will become inaccessible. Please review the <a href=\'https://coolify.io/docs/knowledge-base/health-checks\' target=\'_blank\' class=\'underline text-white\'>Health Checks</a> guide before proceeding!')"
                :step2ButtonText="__('Enable Healthcheck')" :confirmWithText="false" :confirmWithPassword="false"
                isHighlightedButton>
            </x-modal-confirmation>
        @else
            <x-forms.button canGate="update" :canResource="$resource" wire:click="toggleHealthcheck">{{ __('Disable Healthcheck') }}</x-forms.button>
        @endif
    </div>
    <div class="mt-1 pb-4">{{ __("Define how your resource's health should be checked.") }}</div>
    <div class="flex flex-col gap-4">
        @if ($customHealthcheckFound)
            <x-callout type="warning" :title="__('Caution')">
                <p>{{ __('A custom health check has been detected. If you enable this health check, it will disable the custom one and use this instead.') }}</p>
            </x-callout>
        @endif

        {{-- Healthcheck Type Selector --}}
        <div class="flex gap-2">
            <x-forms.select canGate="update" :canResource="$resource" id="healthCheckType" label="{{ __('Type') }}" required wire:model.live="healthCheckType">
                <option value="http">HTTP</option>
                <option value="cmd">CMD</option>
            </x-forms.select>
        </div>

        @if ($healthCheckType === 'http')
            {{-- HTTP Healthcheck Fields --}}
            <div class="flex gap-2">
                <x-forms.select canGate="update" :canResource="$resource" id="healthCheckMethod" label="{{ __('Method') }}" required>
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                </x-forms.select>
                <x-forms.select canGate="update" :canResource="$resource" id="healthCheckScheme" label="{{ __('Scheme') }}" required>
                    <option value="http">http</option>
                    <option value="https">https</option>
                </x-forms.select>
                <x-forms.input canGate="update" :canResource="$resource" id="healthCheckHost" placeholder="localhost" label="{{ __('Host') }}" required />
                <x-forms.input canGate="update" :canResource="$resource" type="number" id="healthCheckPort"
                    helper="{{ __('If no port is defined, the first exposed port will be used.') }}" placeholder="80" label="{{ __('Port') }}" />
                <x-forms.input canGate="update" :canResource="$resource" id="healthCheckPath" placeholder="/health" label="{{ __('Path') }}" required />
            </div>
            <div class="flex gap-2">
                <x-forms.input canGate="update" :canResource="$resource" type="number" id="healthCheckReturnCode" placeholder="200" label="{{ __('Return Code') }}"
                    required />
                <x-forms.input canGate="update" :canResource="$resource" id="healthCheckResponseText" placeholder="OK" label="{{ __('Response Text') }}" />
            </div>
        @else
            {{-- CMD Healthcheck Fields --}}
            <x-callout type="warning" :title="__('Caution')">
                <p>{{ __('This command runs inside the container on every health check interval. Shell operators (;, |, &amp;, $, &gt;, &lt;) are not allowed.') }}</p>
            </x-callout>
            <div class="flex flex-col gap-2">
                <x-forms.input canGate="update" :canResource="$resource" id="healthCheckCommand"
                    label="{{ __('Command') }}"
                    placeholder="pg_isready -U postgres"
                    helper="{{ __('A simple command to run inside the container. Must exit with code 0 on success. Shell operators like ;, |, &&, $() are not allowed.') }}"
                    :required="$healthCheckType === 'cmd'" />
            </div>
        @endif

        {{-- Common timing fields (used by both types) --}}
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$resource" min="1" type="number" id="healthCheckInterval" placeholder="30"
                label="{{ __('Interval (s)') }}" required />
            <x-forms.input canGate="update" :canResource="$resource" type="number" id="healthCheckTimeout" placeholder="30" label="{{ __('Timeout (s)') }}"
                required />
            <x-forms.input canGate="update" :canResource="$resource" type="number" id="healthCheckRetries" placeholder="3" label="{{ __('Retries') }}" required />
            <x-forms.input canGate="update" :canResource="$resource" min=1 type="number" id="healthCheckStartPeriod" placeholder="30"
                label="{{ __('Start Period (s)') }}" required />
        </div>
    </div>
</form>
