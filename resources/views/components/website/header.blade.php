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

            <li class="group">
                <button
                    id="dropdownNvbarButton"
                    class="flex items-center gap-1 py-2 font-medium text-gray-700"
                >
                    المواد
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    id="dropdownNavbar"
                    class="absolute left-0 right-0 top-full mt-2 z-50 hidden bg-white border border-gray-100 rounded-2xl shadow-xl p-8 w-full max-w-4xl mx-auto"
                >
                    <div class="grid grid-cols-3 gap-8 text-right" dir="rtl">
                        @foreach (['المرحلة الابتدائية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'], 'المرحلة الإعدادية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث'], 'المرحلة الثانوية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث']] as $stage => $grades)
                            <div>
                                <h3 class="font-bold text-lg text-blue-900 mb-4">{{ $stage }}</h3>
                                <ul class="space-y-3 text-gray-500 text-sm">
                                    @foreach ($grades as $grade)
                                        <li>
                                            <a href="/subjects" class="block">{{ $grade }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </li>

            <li>
                <a href="/teachers" class="{{ $navLinkClass('teachers') }}" style="{{ $activePage === 'teachers' ? 'color: '.$themeColor : '' }}">
                    المدرسون
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
                    <button
                        id="mobileDropdownBtn"
                        class="flex items-center justify-between w-full py-2 text-gray-700 font-semibold text-lg text-right"
                    >
                        <span>المواد</span>
                        <svg id="mobileArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="mobileDropdownContent" class="hidden pr-4 mt-2 space-y-4 border-r-2 border-gray-100">
                        @foreach (['المرحلة الابتدائية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'], 'المرحلة الإعدادية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث'], 'المرحلة الثانوية' => ['الصف الأول', 'الصف الثاني', 'الصف الثالث']] as $stage => $grades)
                            <div>
                                <h4 class="font-bold text-sm text-blue-900 mb-2">{{ $stage }}</h4>
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                                    @foreach ($grades as $grade)
                                        <a href="/subjects">{{ $grade }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </li>

                <li>
                    <a href="/teachers" class="block py-2 text-gray-700 font-semibold text-lg" style="{{ $activePage === 'teachers' ? 'color: '.$themeColor : '' }}">
                        المدرسون
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
