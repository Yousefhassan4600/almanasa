@props([
    'provider',
])

@php
    $themeColor = $provider->websitePrimaryColor();
    $providerLogo = filled($provider->logo) ? (filter_var($provider->logo, FILTER_VALIDATE_URL) ? $provider->logo : asset('storage/'.$provider->logo)) : null;
    $providerBio = $provider->getTranslation('bio', 'ar', false)
        ?: $provider->getTranslation('bio', 'en', false)
        ?: 'منصة تعليمية متكاملة توفر أفضل المحتوى التعليمي للطلاب في جميع المراحل الدراسية.';
    $socialLinks = [
        ['url' => $provider->facebook_link, 'icon' => 'fa-brands fa-facebook-f', 'label' => 'Facebook'],
        ['url' => $provider->instagram_link, 'icon' => 'fa-brands fa-instagram', 'label' => 'Instagram'],
        ['url' => $provider->linkedin_link, 'icon' => 'fa-brands fa-linkedin-in', 'label' => 'LinkedIn'],
        ['url' => $provider->x_link, 'icon' => 'fa-brands fa-x-twitter', 'label' => 'X'],
        ['url' => $provider->snapchat_link, 'icon' => 'fa-brands fa-snapchat', 'label' => 'Snapchat'],
    ];
@endphp

<footer class="w-full bg-white pt-16 pb-8 border-t border-gray-100" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 pb-12 text-center md:text-right">
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <a href="/" class="flex items-center gap-2">
                    @if ($providerLogo)
                        <img src="{{ $providerLogo }}" alt="{{ $provider->name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100 bg-white" />
                    @else
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-sm" style="background-color: {{ $themeColor }}">
                            {{ \Illuminate\Support\Str::of($provider->name)->substr(0, 1) }}
                        </div>
                    @endif
                    <span class="text-2xl font-black tracking-tight" style="color: {{ $themeColor }}">{{ $provider->name }}</span>
                </a>

                <p class="text-xs font-bold text-gray-400 leading-relaxed max-w-[260px]">
                    {{ $providerBio }}
                </p>

                <div class="flex items-center gap-2.5 pt-2">
                    @foreach ($socialLinks as $link)
                        @if (filled($link['url']))
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $link['label'] }}"
                                class="w-8 h-8 rounded-full bg-[#F1F5F9] hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center text-xs transition-all"
                            >
                                <i class="{{ $link['icon'] }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="text-base font-black text-[#1E3A8A]">الدعم والمساعدة</h4>
                <ul class="space-y-2.5 text-xs font-bold text-gray-400">
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">تواصل معنا</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">مركز المساعدة</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الشروط والأحكام</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-base font-black text-[#1E3A8A]">عن المنصة</h4>
                <ul class="space-y-2.5 text-xs font-bold text-gray-400">
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">من نحن</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">كيف نعمل</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">سياسة الخصوصية</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">اتفاقية الاستخدام</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-base font-black text-[#1E3A8A]">روابط سريعة</h4>
                <ul class="space-y-2.5 text-xs font-bold text-gray-400">
                    <li><a href="/subjects" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">المواد الدراسية</a></li>
                    <li><a href="/my_lessons" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">دروسي</a></li>
                    <li><a href="/packages" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الباقات</a></li>
                    <li><a href="#" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الأسئلة الشائعة</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 text-center">
            <p class="text-[11px] font-bold text-gray-400 tracking-wide">
                جميع الحقوق محفوظة. © {{ $provider->name }} {{ now()->year }}
            </p>
        </div>
    </div>
</footer>
