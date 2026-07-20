<div>
    @if ($isVisible)
        <section class="py-16 bg-white" dir="rtl">
            <div class="mx-auto px-4 md:px-8">
                <div
                    class="rounded-[32px] overflow-hidden relative shadow-xl"
                    style="background: linear-gradient(90deg, {{ $themeColor }}, {{ $secondaryThemeColor }}); box-shadow: 0 20px 25px -5px color-mix(in srgb, {{ $themeColor }} 10%, transparent), 0 8px 10px -6px color-mix(in srgb, {{ $themeColor }} 10%, transparent);"
                >
                    <div
                        class="absolute -top-12 -left-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"
                    ></div>
                    <div
                        class="absolute -bottom-20 -right-10 w-72 h-72 bg-white/5 rounded-full pointer-events-none"
                    ></div>

                    <div
                        class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center p-4 md:p-8 relative z-10"
                    >
                        <div
                            class="lg:col-span-7 text-center lg:text-right space-y-6 order-2 lg:order-1"
                        >
                            <h2
                                class="text-2xl sm:text-3xl md:text-6xl font-extrabold text-white leading-tight"
                            >
                                جاهز للانطلاق نحو التفوق ؟
                            </h2>
                            <p
                                class="text-lg sm:text-xl text-purple-100 max-w-md mx-auto lg:mx-0 leading-relaxed"
                            >
                                انضم إلى آلاف الطلاب وابدأ رحلتك التعليمية الآن.
                            </p>

                            <div class="pt-2">
                                <a
                                    href="/login"
                                    class="inline-flex w-full sm:w-auto font-bold text-base px-8 py-4 rounded-[12px] shadow-lg transition-all active:scale-95 justify-center"
                                    style="background-color: {{ $secondaryThemeColor }}; color: white;"
                                >
                                    ابدأ رحلتك الآن
                                </a>
                            </div>
                        </div>

                        <div
                            class="lg:col-span-5 flex justify-center order-1 lg:order-2"
                        >
                            <div
                                class="w-full max-w-[280px] sm:max-w-[320px] lg:max-w-full aspect-square relative flex items-center justify-center"
                            >
                                <img
                                    src="{{ rtrim($assetPath, '/') }}/images/bag.png"
                                    alt="جاهز للتفوق"
                                    class="w-full h-auto object-contain drop-shadow-2xl animate-bounce-slow"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
