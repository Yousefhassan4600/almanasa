@props([
    'provider',
    'page' => 'index.html',
    'logoutOnly' => false,
])

@php
    $themeColor = $provider->websitePrimaryColor();
    $activePage = \Illuminate\Support\Str::beforeLast($page, '.html');
    $providerLogo = filled($provider->logo) ? (filter_var($provider->logo, FILTER_VALIDATE_URL) ? $provider->logo : asset('storage/'.$provider->logo)) : null;
    $selectedLocale = \App\Support\WebsiteUrl::locale();
    $otherLocale = $selectedLocale === 'ar' ? 'en' : 'ar';
    $switchUrl = \App\Support\WebsiteUrl::path(request()->getRequestUri(), $otherLocale);

    $navLinkClass = function (string $pageName) use ($activePage, $themeColor): string {
        return 'font-medium '.($activePage === $pageName ? '' : 'text-gray-700');
    };
@endphp

<header class="shadow-xl bg-white relative">
    <nav class="p-4 lg:p-6 my-container flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button
                id="openSidebarBtn"
                class="block lg:hidden text-gray-700 transition-colors"
                style="--hover-color: {{ $themeColor }}"
                onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')"
                onmouseout="this.style.color=''"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                </svg>
            </button>

            <a href="/{{ \App\Support\WebsiteUrl::locale() }}" class="font-bold text-xl lg:text-2xl whitespace-nowrap flex items-center gap-2" style="color: {{ $themeColor }}">
                @if ($providerLogo)
                    <img src="{{ $providerLogo }}" alt="{{ $provider->name }}" class="w-9 h-9 rounded-xl object-cover border border-gray-100 bg-white" />
                @endif
                {{ $provider->name }}
            </a>
        </div>

        <ul class="hidden lg:flex items-center gap-4 xl:gap-6 text-sm xl:text-base whitespace-nowrap">
            <li>
                <a href="/{{ \App\Support\WebsiteUrl::locale() }}" class="{{ $navLinkClass('index') }}" style="{{ $activePage === 'index' ? 'color: '.$themeColor : '' }}">
                    {{ __('الرئيسية') }}
                </a>
            </li>

            <li>
                <a href="/{{ \App\Support\WebsiteUrl::locale() }}/my_lessons" class="{{ $navLinkClass('my_lessons') }}" style="{{ $activePage === 'my_lessons' ? 'color: '.$themeColor : '' }}">
                    {{ __('دروسي') }}
                </a>
            </li>
            <li>
                <a href="/{{ \App\Support\WebsiteUrl::locale() }}/packages" class="{{ $navLinkClass('packages') }}" style="{{ $activePage === 'packages' ? 'color: '.$themeColor : '' }}">
                    {{ __('الباقات') }}
                </a>
            </li>
        </ul>

        <div class="flex items-center gap-2 lg:gap-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            <a href="{{ $switchUrl }}" hreflang="{{ $otherLocale }}" aria-label="{{ $otherLocale === 'ar' ? 'Switch to Arabic' : 'Switch to English' }}" class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold transition-colors hover:bg-sky-100" style="background-color: #e0f2fe; color: #0284c7" dir="ltr">
                <i class="fa-solid fa-earth-africa text-base" aria-hidden="true"></i>
                <span>{{ $otherLocale === 'en' ? 'EN' : 'ع' }}</span>
            </a>
            @livewire('website.auth-controls', ['providerId' => $provider->id, 'placement' => 'desktop', 'logoutOnly' => $logoutOnly], key('website-header-auth-desktop-'.$provider->id.'-'.$activePage))
        </div>
    </nav>

    <div
        id="sidebarOverlay"
        class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300 opacity-0"
    ></div>

    <div
        id="mobileSidebar"
        class="fixed top-0 {{ $selectedLocale === 'ar' ? 'right-0 translate-x-full' : 'left-0 -translate-x-full' }} bottom-0 w-[300px] bg-white z-50 p-6 shadow-2xl transform transition-transform duration-300 ease-in-out lg:hidden flex flex-col justify-between overflow-y-auto"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    >
        <div>
            <div class="flex items-center justify-between border-b pb-4 mb-6">
                <div class="flex items-center gap-2">
                    @if ($providerLogo)
                        <img src="{{ $providerLogo }}" alt="{{ $provider->name }}" class="w-9 h-9 rounded-xl object-cover border border-gray-100 bg-white" />
                    @endif
                    <p class="font-bold text-xl" style="color: {{ $themeColor }}">{{ $provider->name }}</p>
                </div>
                <button id="closeSidebarBtn" class="text-gray-500 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <ul class="space-y-4 text-right">
                <li>
                    <a href="/{{ \App\Support\WebsiteUrl::locale() }}" class="block py-2 font-semibold text-lg" style="{{ $activePage === 'index' ? 'color: '.$themeColor : '' }}">
                        {{ __('الرئيسية') }}
                    </a>
                </li>

                <li>
                    <a href="/{{ \App\Support\WebsiteUrl::locale() }}/my_lessons" class="block py-2 text-gray-700 font-semibold text-lg" style="{{ $activePage === 'my_lessons' ? 'color: '.$themeColor : '' }}">
                        {{ __('دروسي') }}
                    </a>
                </li>
                <li>
                    <a href="/{{ \App\Support\WebsiteUrl::locale() }}/packages" class="block py-2 text-gray-700 font-semibold text-lg" style="{{ $activePage === 'packages' ? 'color: '.$themeColor : '' }}">
                        {{ __('الباقات') }}
                    </a>
                </li>
            </ul>

            <div class="mt-8 border-t pt-4">
                @livewire('website.auth-controls', ['providerId' => $provider->id, 'placement' => 'mobile', 'logoutOnly' => $logoutOnly], key('website-header-auth-mobile-'.$provider->id.'-'.$activePage))
            </div>
        </div>
    </div>
</header>
