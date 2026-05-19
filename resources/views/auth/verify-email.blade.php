<x-layout>
    <section class="flex flex-col h-full lg:items-center lg:justify-center">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto max-w-7xl lg:py-0">
            <h1>{{ __('Verification Email Sent') }}</h1>
            <div class="flex justify-center gap-2 text-center">
                <br>{{ __('To activate your account, please open the email and follow the instructions.') }}
            </div>
            <livewire:verify-email />
        </div>
    </section>
</x-layout>
