<div class="sub-menu-wrapper">
    <a class="sub-menu-item {{ $activeMenu === 'general' ? 'menu-item-active' : '' }}" {{ wireNavigate() }}
        href="{{ route('settings.index') }}"><span class="menu-item-label">{{ __('settings.general') }}</span></a>
    <a class="sub-menu-item {{ $activeMenu === 'advanced' ? 'menu-item-active' : '' }}" {{ wireNavigate() }}
        href="{{ route('settings.advanced') }}"><span class="menu-item-label">{{ __('settings.advanced_page.title') }}</span></a>
    <a class="sub-menu-item {{ $activeMenu === 'updates' ? 'menu-item-active' : '' }}" {{ wireNavigate() }}
        href="{{ route('settings.updates') }}"><span class="menu-item-label">{{ __('settings.updates') }}</span></a>
</div>
