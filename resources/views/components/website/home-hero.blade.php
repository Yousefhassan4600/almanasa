@props([
    'provider',
    'banner' => null,
])

@php
    $isTeacher = $provider->type === \App\Enums\ProviderType::StandaloneTeacher;
    $themeColor = $isTeacher ? '#FEB008' : '#5D3FD3';
    $title = $banner?->getTranslation('title', 'ar', false)
        ?: $banner?->getTranslation('title', 'en', false)
        ?: 'تعلم من أفضل المعلمين في جميع المواد الدراسية';
    $subtitle = $banner?->getTranslation('subtitle', 'ar', false)
        ?: $banner?->getTranslation('subtitle', 'en', false)
        ?: 'فيديوهات تفاعلية، تمارين وامتحانات ذكية، تقارير متابعة تفصيلية لتحقيق أفضل النتائج.';
    $bannerImage = filled($banner?->cover) ? (filter_var($banner->cover, FILTER_VALIDATE_URL) ? $banner->cover : asset('storage/'.$banner->cover)) : '/academy/assets/images/herostudent.png';
    $exploreUrl = \Illuminate\Support\Facades\Auth::check() ? '/subjects' : '/login';
@endphp

<section class="relative bg-gradient-to-b from-[#F3F0FF] to-white pt-12 pb-6 px-4 md:px-8 overflow-hidden" dir="rtl">
    <div class="my-container max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-16">
            <div class="lg:col-span-7 text-center lg:text-right space-y-6">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-blue-950 leading-tight lg:leading-[1.2]">
                    {{ $title }}
                </h1>

                <p class="text-gray-500 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    {{ $subtitle }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    @guest
                        <a href="/login" class="w-full sm:w-auto text-white font-semibold text-lg px-8 py-4 rounded-[12px] shadow-lg transition-all hover:shadow-xl active:scale-95 text-center" style="background-color: {{ $themeColor }}">
                            ابدأ رحلتك الآن
                        </a>
                    @endguest

                    <a href="{{ $exploreUrl }}" class="w-full sm:w-auto bg-transparent font-semibold text-lg px-8 py-4 rounded-[12px] transition-all active:scale-95 text-center border-2" style="color: {{ $themeColor }}; border-color: {{ $themeColor }}">
                        استكشف المواد
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center relative">
                <div class="relative w-full max-w-[450px] lg:max-w-full aspect-square md:aspect-[4/5] lg:aspect-auto flex items-center justify-center">
                    <img src="{{ $bannerImage }}" alt="{{ $title }}" class="w-full h-auto object-contain z-10" />
                    <div class="absolute w-72 h-72 rounded-full blur-3xl -top-10 -left-10 z-0" style="background-color: {{ $themeColor }}1A"></div>
                    <div class="absolute w-60 h-60 bg-blue-400/10 rounded-full blur-3xl -bottom-10 -right-10 z-0"></div>
                </div>
            </div>
        </div>
    </div>
</section>
