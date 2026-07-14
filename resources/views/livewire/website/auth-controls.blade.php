<div class="{{ $isDesktop ? 'hidden lg:flex items-center gap-2 lg:gap-4' : 'w-full flex flex-col gap-3' }}">
    @if ($logoutOnly || $hasCompletedProfile)
        @if (! $logoutOnly)
            <div class="{{ $isDesktop ? 'hidden lg:flex items-center gap-2' : 'flex items-center justify-center gap-3' }}">
                <a
                    href="/profile"
                    aria-label="الملف الشخصي"
                    class="text-gray-700 hover:text-[{{ $themeColor }}] transition-colors p-2 shrink-0"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                        ></path>
                    </svg>
                </a>

                <a
                    href="/cart"
                    aria-label="السلة"
                    class="text-gray-700 hover:text-[{{ $themeColor }}] transition-colors p-2 shrink-0"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                        ></path>
                    </svg>
                </a>
            </div>
        @endif

        <button
            type="button"
            wire:click="logout"
            class="{{ $isDesktop ? 'text-sm lg:text-base py-2.5 px-4 whitespace-nowrap' : 'w-full text-center py-3 px-6' }} bg-transparent text-red-600 border-2 border-red-100 font-semibold rounded-[12px] transition-all hover:bg-red-50 active:scale-95"
        >
            <span wire:loading.remove wire:target="logout">تسجيل الخروج</span>
            <span wire:loading wire:target="logout">جاري الخروج...</span>
        </button>
    @else
        <a
            href="/login"
            class="{{ $isDesktop ? 'hidden lg:flex items-center justify-center whitespace-nowrap text-sm lg:text-base py-2.5 px-4' : 'w-full text-center py-3 px-6' }} bg-transparent font-semibold rounded-[12px] border-2 transition-all hover:bg-gray-50 active:scale-95"
            style="color: {{ $themeColor }}; border-color: {{ $themeColor }}"
        >
            تسجيل الدخول
        </a>
    @endif
</div>
