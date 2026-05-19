<div class="pb-6">
    <h1>{{ __('Notifications') }}</h1>
    <div class="subtitle">{{ __('Get notified about your infrastructure.') }}</div>
    <div class="navbar-main">
        <nav class="flex items-center gap-3.5 min-h-10">
            <a class="{{ request()->routeIs('notifications.email') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.email') }}">
                <button>{{ __('Email') }}</button>
            </a>
            <a class="{{ request()->routeIs('notifications.discord') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.discord') }}">
                <button>{{ __('Discord') }}</button>
            </a>
            <a class="{{ request()->routeIs('notifications.telegram') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.telegram') }}">
                <button>{{ __('Telegram') }}</button>
            </a>
            <a class="{{ request()->routeIs('notifications.slack') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.slack') }}">
                <button>{{ __('Slack') }}</button>
            </a>
            <a class="{{ request()->routeIs('notifications.pushover') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.pushover') }}">
                <button>{{ __('Pushover') }}</button>
            </a>
            <a class="{{ request()->routeIs('notifications.webhook') ? 'dark:text-white' : '' }}" {{ wireNavigate() }}
                href="{{ route('notifications.webhook') }}">
                <button>{{ __('Webhook') }}</button>
            </a>
        </nav>
    </div>
</div>
