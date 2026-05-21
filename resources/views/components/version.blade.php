<a {{ $attributes->merge(['class' => 'text-xs cursor-pointer opacity-90 hover:opacity-100 dark:hover:text-white hover:text-black']) }}
    href="{{ config('constants.coolify.releases_page_url') }}/tag/v{{ config('constants.coolify.version') }}" target="_blank">
    v{{ config('constants.coolify.version') }}
</a>
