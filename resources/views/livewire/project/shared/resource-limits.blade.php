<div>
    <form wire:submit='submit' class="flex flex-col">
        <div class="flex items-center gap-2 ">
            <h2>{{ __('Resource Limits') }}</h2>
            <x-forms.button canGate="update" :canResource="$resource" type='submit'>{{ __('Save') }}</x-forms.button>
        </div>
        <div class="">{{ __('Limit your container resources by CPU & memory.') }}</div>
        <h3 class="pt-4">{{ __('Limit CPUs') }}</h3>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$resource" placeholder="1.5"
                :helper="__('0 means use all CPUs. Floating point number, like 0.002 or 1.5. More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/engine/reference/run/#cpu-share-constraint\'>here</a>.')"
                label="{{ __('Number of CPUs') }}" id="limitsCpus" />
            <x-forms.input canGate="update" :canResource="$resource" placeholder="0-2"
                :helper="__('Empty means, use all CPU sets. 0-2 will use CPU 0, CPU 1 and CPU 2. More info <a class=\'underline dark:text-white\'  target=\'_blank\' href=\'https://docs.docker.com/engine/reference/run/#cpu-share-constraint\'>here</a>.')"
                label="{{ __('CPU sets to use') }}" id="limitsCpuset" />
            <x-forms.input canGate="update" :canResource="$resource" placeholder="1024"
                :helper="__('More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/engine/reference/run/#cpu-share-constraint\'>here</a>.')"
                label="{{ __('CPU Weight') }}" id="limitsCpuShares" />
        </div>
        <h3 class="pt-4">{{ __('Limit Memory') }}</h3>
        <div class="flex gap-2">
            <x-forms.input canGate="update" :canResource="$resource"
                :helper="__('<span class=\'text-helper\'>Examples</span><br>• 69b (byte)<br>• 420k (kilobyte)<br>• 1337m (megabyte)<br>• 1g (gigabyte)<br><br>More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/compose/compose-file/05-services/#mem_reservation\'>here</a>.')"
                label="{{ __('Soft Memory Limit') }}" id="limitsMemoryReservation" />
            <x-forms.input canGate="update" :canResource="$resource"
                :helper="__('Value between 0-100.<br><br>More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/compose/compose-file/05-services/#mem_swappiness\'>here</a>.')"
                type="number" min="0" max="100" label="{{ __('Swappiness') }}"
                id="limitsMemorySwappiness" />
            <x-forms.input canGate="update" :canResource="$resource"
                :helper="__('<span class=\'text-helper\'>Examples</span><br>• 69b (byte)<br>• 420k (kilobyte)<br>• 1337m (megabyte)<br>• 1g (gigabyte)<br><br>More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/compose/compose-file/05-services/#mem_limit\'>here</a>.')"
                label="{{ __('Maximum Memory Limit') }}" id="limitsMemory" />
            <x-forms.input canGate="update" :canResource="$resource"
                :helper="__('<span class=\'text-helper\'>Examples</span><br>• 69b (byte)<br>• 420k (kilobyte)<br>• 1337m (megabyte)<br>• 1g (gigabyte)<br><br>More info <a class=\'underline dark:text-white\' target=\'_blank\' href=\'https://docs.docker.com/compose/compose-file/05-services/#memswap_limit\'>here</a>.')"
                label="{{ __('Maximum Swap Limit') }}" id="limitsMemorySwap" />
        </div>
    </form>
</div>
