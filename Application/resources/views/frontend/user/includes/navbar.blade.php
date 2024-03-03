<div class="nav-bar">
    <div class="container-xxl">
        <div class="nav-bar-container">
            <a href="{{ route('home') }}" class="logo">
                <img class="logo-dark" src="{{ asset($settings->media->dark_logo) }}"
                    alt="{{ $settings->general->site_name }}" />
                <img class="logo-light d-none" src="{{ asset($settings->media->light_logo) }}"
                    alt="{{ $settings->general->site_name }}" />
            </a>
            <div class="nav-bar-menu">
                <div class="overlay"></div>
                <div class="nav-bar-links">
                    <div class="nav-bar-menu-header">
                        <a class="nav-bar-menu-close ms-auto">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                    <a href="{{ route('user.gallery.index') }}" class="link">
                        <div class="link-title">
                            <i class="fa-solid fa-images me-1"></i>
                            <span>{{ lang('Gallery', 'gallery') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('user.settings.index') }}" class="link">
                        <div class="link-title">
                            <i class="fa-solid fa-cog me-1"></i>
                            <span>{{ lang('Settings', 'account') }}</span>
                        </div>
                    </a>
                    @include('frontend.global.language-menu')
                </div>
            </div>
            <div class="nav-bar-actions">
                @include('frontend.global.user-menu')
                @if ($settings->theme->mode_switcher)
                    <button class="btn btn-theme ms-3">
                        <i class="fa fa-sun"></i>
                        <i class="fa fa-moon"></i>
                    </button>
                @endif
                <div class="nav-bar-menu-btn">
                    <i class="fa-solid fa-bars-staggered fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>
