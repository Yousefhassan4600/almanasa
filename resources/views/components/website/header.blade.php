@props([
    'provider',
    'page' => 'index.html',
    'logoutOnly' => false,
])

@php
    $isTeacher = $provider->type === \App\Enums\ProviderType::StandaloneTeacher;
    $themeColor = $isTeacher ? '#FEB008' : '#5D3FD3';
    $activePage = \Illuminate\Support\Str::beforeLast($page, '.html');

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

            <a href="/" class="font-bold text-xl lg:text-2xl whitespace-nowrap" style="color: {{ $themeColor }}">
                {{ $provider->name }}
            </a>
        </div>

        <ul class="hidden lg:flex items-center gap-4 xl:gap-6 text-sm xl:text-base whitespace-nowrap">
            <li>
                <a href="/" class="{{ $navLinkClass('index') }}" style="{{ $activePage === 'index' ? 'color: '.$themeColor : '' }}">
                    الرئيسية
                </a>
            </li>

            <li>
                <a href="/my_lessons" class="{{ $navLinkClass('my_lessons') }}" style="{{ $activePage === 'my_lessons' ? 'color: '.$themeColor : '' }}">
                    دروسي
                </a>
            </li>
            <li>
                <a href="/packages" class="{{ $navLinkClass('packages') }}" style="{{ $activePage === 'packages' ? 'color: '.$themeColor : '' }}">
                    الباقات
                </a>
            </li>
        </ul>

        <div class="flex items-center gap-2 lg:gap-4" dir="rtl">
            @livewire('website.auth-controls', ['providerId' => $provider->id, 'placement' => 'desktop', 'logoutOnly' => $logoutOnly], key('website-header-auth-desktop-'.$provider->id.'-'.$activePage))
        </div>
    </nav>

    <div
        id="sidebarOverlay"
        class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300 opacity-0"
    ></div>

    <div
        id="mobileSidebar"
        class="fixed top-0 right-0 bottom-0 w-[300px] bg-white z-50 p-6 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out lg:hidden flex flex-col justify-between overflow-y-auto"
        dir="rtl"
    >
        <div>
            <div class="flex items-center justify-between border-b pb-4 mb-6">
                <p class="font-bold text-xl" style="color: {{ $themeColor }}">{{ $provider->name }}</p>
                <button id="closeSidebarBtn" class="text-gray-500 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <ul class="space-y-4 text-right">
                <li>
                    <a href="/" class="block py-2 font-semibold text-lg" style="{{ $activePage === 'index' ? 'color: '.$themeColor : '' }}">
                        الرئيسية
                    </a>
                </li>

                <li>
                    <a href="/my_lessons" class="block py-2 text-gray-700 font-semibold text-lg" style="{{ $activePage === 'my_lessons' ? 'color: '.$themeColor : '' }}">
                        دروسي
                    </a>
                </li>
                <li>
                    <a href="/packages" class="block py-2 text-gray-700 font-semibold text-lg" style="{{ $activePage === 'packages' ? 'color: '.$themeColor : '' }}">
                        الباقات
                    </a>
                </li>
            </ul>

            <div class="mt-8 border-t pt-4">
                @livewire('website.auth-controls', ['providerId' => $provider->id, 'placement' => 'mobile', 'logoutOnly' => $logoutOnly], key('website-header-auth-mobile-'.$provider->id.'-'.$activePage))
            </div>
        </div>
    </div>
</header>
