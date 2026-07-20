@php
    $themeColor = $provider->websitePrimaryColor();
    $secondaryThemeColor = $provider->websiteSecondaryColor();
    $styles = [
        'رياضيات' => ['icon' => 'fa-calculator', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'Mathematics' => ['icon' => 'fa-calculator', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'كيمياء' => ['icon' => 'fa-flask', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'Chemistry' => ['icon' => 'fa-flask', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'فيزياء' => ['icon' => 'fa-atom', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
        'Physics' => ['icon' => 'fa-atom', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
        'عربي' => ['icon' => 'fa-book-open-reader', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
        'Arabic' => ['icon' => 'fa-book-open-reader', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
        'جغرافيا' => ['icon' => 'fa-earth-africa', 'bg' => 'bg-teal-50', 'text' => 'text-teal-600'],
        'Geography' => ['icon' => 'fa-earth-africa', 'bg' => 'bg-teal-50', 'text' => 'text-teal-600'],
        'تاريخ' => ['icon' => 'fa-landmark', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
        'History' => ['icon' => 'fa-landmark', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
    ];

    $styleFor = function (string $name) use ($styles): array {
        foreach ($styles as $needle => $style) {
            if (str_contains($name, $needle)) {
                return $style;
            }
        }

        return ['icon' => 'fa-book-open', 'bg' => 'bg-purple-50', 'text' => ''];
    };

    $displayGrade = $gradeName ?: 'كل الصفوف';
@endphp

<div>
    <section class="relative bg-white pb-12" dir="rtl">
        <div
            class="pt-16 pb-24 px-4 md:px-8 text-center text-white relative overflow-hidden"
            style="background: linear-gradient(90deg, {{ $themeColor }}, {{ $secondaryThemeColor }});"
        >
            <div class="absolute left-10 bottom-6 w-24 h-24 opacity-20 hidden lg:block">
                <i class="fa-solid fa-flask-vial text-7xl"></i>
            </div>
            <div class="absolute right-10 bottom-6 w-24 h-24 opacity-20 hidden lg:block">
                <i class="fa-solid fa-books text-7xl">📚</i>
            </div>

            <div class="max-w-4xl mx-auto space-y-4 relative z-10">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">
                    تصفح المواد الدراسية
                </h1>
                <p class="text-purple-100 text-sm sm:text-base max-w-md mx-auto opacity-90">
                    @if ($hasGradeFilter)
                        المواد المتاحة في صفك الدراسي داخل هذه الأكاديمية.
                    @else
                        استكشف جميع المواد المتاحة داخل هذه الأكاديمية.
                    @endif
                </p>

                <div class="max-w-xl mx-auto bg-white rounded-full p-1.5 shadow-md flex items-center mt-6">
                    <div class="flex-1 pr-4 flex items-center gap-2 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="ابحث عن مادة..."
                            class="w-full bg-transparent border-none outline-none text-gray-700 placeholder-gray-400 py-2 text-sm focus:ring-0"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-10">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-blue-950">
                        المواد المتاحة في <span style="color: {{ $themeColor }}">{{ $displayGrade }}</span>
                    </h2>
                    @if ($stageName)
                        <p class="text-xs text-gray-400 mt-2">{{ $stageName }}</p>
                    @endif
                </div>
                <span
                    class="text-xs sm:text-sm text-gray-400 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100"
                >
                    {{ $subjects->count() }} مادة متوفرة
                </span>
            </div>

            @if ($subjects->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($subjects as $accountSubject)
                        @php
                            $subject = $accountSubject->gradeSubject?->subject;
                            $subjectName = $subject
                                ? ($subject->getTranslation('name', 'ar', false) ?: $subject->name)
                                : $accountSubject->name;
                            $subjectDescription = $subject
                                ? ($subject->getTranslation('description', 'ar', false) ?: $subject->description)
                                : null;
                            $trackName = $subject?->track
                                ? ($subject->track->getTranslation('name', 'ar', false) ?: $subject->track->name)
                                : null;
                            $style = $styleFor($subjectName);
                        @endphp

                        <div
                            wire:key="subject-card-{{ $accountSubject->id }}"
                            class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between relative group"
                        >
                            <button class="absolute top-4 left-4 text-gray-400 transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">
                                <i class="fa-regular fa-bookmark text-sm"></i>
                            </button>

                            <div class="flex flex-col items-center text-center mt-2">
                                <div
                                    class="w-14 h-14 rounded-full {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center text-xl font-bold mb-4"
                                    @if (blank($style['text'])) style="color: {{ $themeColor }}" @endif
                                >
                                    <i class="fa-solid {{ $subject?->icon ?: $style['icon'] }}"></i>
                                </div>
                                <h3 class="font-extrabold text-blue-950 text-base mb-1">{{ $subjectName }}</h3>
                                <span class="text-[11px] text-gray-400 mb-3 flex items-center gap-1">
                                    <i class="fa-solid fa-user-tie text-[10px]"></i>
                                    عدد {{ $accountSubject->active_teachers_count }} مدرسين
                                </span>
                                <p class="text-xs text-gray-400 leading-relaxed max-w-[200px] min-h-[36px] mb-4">
                                    {{ $subjectDescription ?: 'مادة متاحة ضمن صفك الدراسي داخل الأكاديمية.' }}
                                </p>
                                @if ($trackName)
                                    <span
                                        class="{{ $style['bg'] }} {{ $style['text'] }} text-[10px] font-bold px-3 py-1 rounded-full mb-5"
                                    >
                                        {{ $trackName }}
                                    </span>
                                @endif
                            </div>

                            <a
                                href="/teachers?subject={{ $accountSubject->id }}"
                                class="w-full border text-center border-gray-200 text-gray-600 font-bold text-xs py-3 rounded-xl transition-colors bg-transparent"
                                style="--hover-color: {{ $themeColor }}"
                                onmouseover="this.style.color=this.style.getPropertyValue('--hover-color'); this.style.borderColor=this.style.getPropertyValue('--hover-color')"
                                onmouseout="this.style.color=''; this.style.borderColor=''"
                            >
                                عرض المدرسين <i class="fa-solid fa-chevron-left text-[9px] mr-1"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                    <p class="text-sm font-bold text-blue-950">
                        لا توجد مواد مطابقة{{ $hasGradeFilter ? ' في صفك الدراسي' : '' }} حالياً.
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
