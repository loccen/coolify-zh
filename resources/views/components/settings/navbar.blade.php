<div class="pb-5">
    <h1>{{ __('settings.title') }}</h1>
    <div class="subtitle">{{ __('settings.subtitle') }}</div>
    <div class="navbar-main">
        <nav class="flex items-center gap-6 min-h-10 whitespace-nowrap">
            <a class="{{ request()->routeIs('settings.index') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('settings.index') }}">
                {{ __('settings.configuration') }}
            </a>
            <a class="{{ request()->routeIs('settings.backup') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('settings.backup') }}">
                {{ __('settings.backup') }}
            </a>
            <a class="{{ request()->routeIs('settings.email') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('settings.email') }}">
                {{ __('settings.transactional_email') }}
            </a>
            <a class="{{ request()->routeIs('settings.oauth') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('settings.oauth') }}">
                {{ __('settings.oauth') }}
            </a>
            <a class="{{ request()->routeIs('settings.scheduled-jobs') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('settings.scheduled-jobs') }}">
                {{ __('settings.scheduled_jobs') }}
            </a>
            <div class="flex-1"></div>
        </nav>
    </div>
</div>
