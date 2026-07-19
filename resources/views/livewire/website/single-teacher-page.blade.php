@php
    use App\Enums\ProviderType;
    use App\Enums\CoursePeriodType;
    use App\Enums\LessonTypeEnum;
    use Illuminate\Support\Str;

    $isStandaloneTeacher = $provider?->type === ProviderType::StandaloneTeacher;
    $subject = $accountSubject?->gradeSubject?->subject;
    $grade = $accountSubject?->gradeSubject?->grade;
    $stage = $grade?->educationStage;
    $track = $subject?->track;
    $teacherName = $isStandaloneTeacher
        ? ($teacher?->owner?->name ?: $provider?->owner?->name ?: 'معلم')
        : ($teacher?->teacher?->owner?->name ?: 'معلم');
    $teacherImage = (! $isStandaloneTeacher && $teacher?->image)
        ? asset('storage/'.$teacher->image)
        : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200';
    $courseTitle = $course?->getTranslation('title', 'ar', false) ?: $course?->title ?: ($subject?->getTranslation('name', 'ar', false) ?: 'الكورس');
    $courseDescription = $course?->getTranslation('description', 'ar', false) ?: $course?->description;
    $subjectName = $subject?->getTranslation('name', 'ar', false) ?: $subject?->name;
    $trackName = $track?->getTranslation('name', 'ar', false) ?: $track?->name;
    $gradeName = $grade?->name;
    $stageName = $stage?->name;
    $lessons = $course?->lessons ?? collect();
    $termOneLessons = $lessons->filter(fn ($lesson) => $lesson->coursePeriod?->type === CoursePeriodType::Term1);
    $termTwoLessons = $lessons->filter(fn ($lesson) => $lesson->coursePeriod?->type === CoursePeriodType::Term2);
    $yearlyLessons = $lessons->filter(fn ($lesson) => $lesson->coursePeriod?->type === CoursePeriodType::Yearly || blank($lesson->coursePeriod));
    $themeColor = $isStandaloneTeacher ? '#FEB008' : '#5D3FD3';
    $themeColorDark = $isStandaloneTeacher ? '#E59B00' : '#4c32b3';
    $themeGradient = $isStandaloneTeacher
        ? 'background: linear-gradient(270deg, #FEB008 0%, #F59E0B 100%)'
        : 'background: linear-gradient(270deg, #5D3FD3 0%, #7048F4 100%)';
    $themeTextLight = $isStandaloneTeacher ? 'text-amber-50' : 'text-purple-100';
    $themeTextMuted = $isStandaloneTeacher ? 'text-amber-100' : 'text-purple-200';
    $themeSoftBg = $isStandaloneTeacher ? 'bg-amber-50' : 'bg-purple-50';
    $lessonItemIsOpen = fn ($item): bool => filled($item)
        && (blank($item->starts_at) || $item->starts_at->lte(now()))
        && (blank($item->ends_at) || $item->ends_at->gte(now()));
    $lessonItemAvailabilityText = function ($item): string {
        if (blank($item)) {
            return 'مغلق';
        }

        if (filled($item->starts_at) && $item->starts_at->isFuture()) {
            return 'يفتح في '.$item->starts_at->format('Y-m-d H:i');
        }

        if (filled($item->ends_at) && $item->ends_at->isPast()) {
            return 'انتهى في '.$item->ends_at->format('Y-m-d H:i');
        }

        return 'مغلق';
    };
@endphp

<div class="bg-white" dir="rtl">
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-6">
        <nav class="flex items-center gap-2 text-xs font-bold text-gray-400 mb-6">
            <a href="/" style="--theme-color: {{ $themeColor }}" class="hover:text-[var(--theme-color)]">الرئيسية</a>
            <span>/</span>
            <a href="/subjects" style="--theme-color: {{ $themeColor }}" class="hover:text-[var(--theme-color)]">المواد</a>
            @if ($subjectName)
                <span>/</span>
                <a href="/teachers?subject={{ $accountSubject?->id }}" style="--theme-color: {{ $themeColor }}" class="hover:text-[var(--theme-color)]">{{ $subjectName }}</a>
            @endif
            @if ($gradeName)
                <span>/</span>
                <span>{{ $gradeName }}</span>
            @endif
        </nav>

        <div class="relative overflow-hidden rounded-[32px] text-white p-6 md:p-10 shadow-xl" style="{{ $themeGradient }}; box-shadow: 0 20px 45px {{ $themeColor }}33">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-7 space-y-5 text-center lg:text-right">
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black leading-tight">{{ $courseTitle }}</h1>
                        <p class="text-sm md:text-base {{ $themeTextLight }} mt-3">
                            {{ collect([$gradeName, $trackName])->filter()->join(' - ') ?: $stageName }}
                        </p>
                    </div>

                    @if ($courseDescription)
                        <p class="text-sm {{ $themeTextLight }} leading-relaxed max-w-2xl mx-auto lg:mx-0">{{ $courseDescription }}</p>
                    @endif

                    <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                        <div class="bg-white/10 rounded-2xl px-5 py-3 min-w-28">
                            <span class="block text-[10px] {{ $themeTextMuted }}">الدروس</span>
                            <span class="block text-lg font-black">{{ $course?->num_of_lessons ?? $lessons->count() }}</span>
                        </div>
                        <div class="bg-white/10 rounded-2xl px-5 py-3 min-w-28">
                            <span class="block text-[10px] {{ $themeTextMuted }}">ساعة محتوى</span>
                            <span class="block text-lg font-black">{{ $course?->num_of_hours ?? '—' }}</span>
                        </div>
                        <div class="bg-white/10 rounded-2xl px-5 py-3 min-w-28">
                            <span class="block text-[10px] {{ $themeTextMuted }}">محاضرات أسبوعياً</span>
                            <span class="block text-lg font-black">{{ $course?->weekly_lectures_count ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 flex items-center justify-center lg:justify-end gap-5">
                    <div class="text-right">
                        <h2 class="text-xl md:text-2xl font-bold">{{ $teacherName }}</h2>
                        @if (! $isStandaloneTeacher)
                            <p class="text-xs {{ $themeTextLight }} mt-1">خبرة {{ $teacher?->experience_years ?? '—' }} سنوات</p>
                        @endif
                    </div>
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full p-1 bg-white/20 border border-white/40 overflow-hidden">
                        <img src="{{ $teacherImage }}" alt="{{ $teacherName }}" class="w-full h-full object-cover rounded-full" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        @if (! $teacher || ! $accountSubject)
            <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                <p class="text-sm font-bold text-blue-950">لم يتم العثور على المعلم أو المادة المطلوبة.</p>
                <a href="/subjects" class="inline-flex mt-4 text-sm font-bold" style="color: {{ $themeColor }}">العودة لاختيار مادة</a>
            </div>
        @elseif (! $course)
            <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                <p class="text-sm font-bold text-blue-950">لا يوجد كورس منشأ لهذا المعلم في هذه المادة حالياً.</p>
                <a href="/teachers?subject={{ $accountSubject->id }}" class="inline-flex mt-4 text-sm font-bold" style="color: {{ $themeColor }}">{{ $isStandaloneTeacher ? 'العودة للمادة' : 'اختيار معلم آخر' }}</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8 space-y-6">
                    <div class="border-b border-gray-100 flex items-center justify-start gap-8">
                        <span class="pb-3 text-sm font-bold border-b-2" style="border-color: {{ $themeColor }}; color: {{ $themeColor }}">الدروس والوحدات</span>
                        <span class="pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400">مستوى الطالب</span>
                    </div>

                    @if ($lessons->isNotEmpty())
                        <div class="space-y-8">
                            @foreach ([
                                'الترم الأول' => $termOneLessons,
                                'الترم الثاني' => $termTwoLessons,
                                'عام' => $yearlyLessons,
                            ] as $periodLabel => $periodLessons)
                                @if ($periodLessons->isNotEmpty())
                                    <div class="space-y-4" wire:key="period-{{ $periodLabel }}">
                                        <h3 class="text-sm font-black text-blue-950">{{ $periodLabel }}</h3>

                                        @foreach ($periodLessons as $lesson)
                                            @php
                                                $lessonTitle = $lesson->getTranslation('title', 'ar', false) ?: $lesson->title;
                                                $lessonItems = $lesson->items;
                                                $lessonIsOpen = $lesson->isCurrentlyOpen();
                                                $lessonAvailabilityText = match (true) {
                                                    $lessonIsOpen => null,
                                                    filled($lesson->starts_at) && $lesson->starts_at->isFuture() => 'تفتح في '.$lesson->starts_at->format('Y-m-d H:i'),
                                                    filled($lesson->ends_at) && $lesson->ends_at->isPast() => 'انتهت في '.$lesson->ends_at->format('Y-m-d H:i'),
                                                    default => 'مغلقة الآن',
                                                };
                                            @endphp

                                            <details class="group bg-gray-50/50 rounded-2xl border {{ $lessonIsOpen ? 'border-gray-100' : 'border-gray-200 opacity-80' }} overflow-hidden" wire:key="lesson-{{ $lesson->id }}">
                                                <summary class="p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition-colors select-none list-none">
                                                    <div class="flex items-center gap-3">
                                                        <span class="w-8 h-8 rounded-xl {{ $lessonIsOpen ? $themeSoftBg : 'bg-gray-100' }} flex items-center justify-center text-sm" style="{{ $lessonIsOpen ? 'color: '.$themeColor : '' }}">
                                                            <i class="fa-solid {{ $lessonIsOpen ? 'fa-book-open' : 'fa-lock' }}"></i>
                                                        </span>
                                                        <div class="text-right">
                                                            <h4 class="text-sm font-bold text-blue-950">{{ $lessonTitle }}</h4>
                                                            <span class="text-[10px] text-gray-400 block mt-0.5">
                                                                {{ $lessonItems->count() }} عناصر
                                                                @if ($lessonAvailabilityText)
                                                                    <span class="mx-1">•</span>
                                                                    <span class="text-rose-500">{{ $lessonAvailabilityText }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300 group-open:rotate-180"></i>
                                                </summary>

                                                <div class="border-t border-gray-100 bg-white">
                                                    @if ($lessonItems->isNotEmpty())
                                                        <div class="p-2 divide-y divide-gray-50">
                                                            @foreach ($lessonItems as $item)
                                                                @php
                                                                    $itemTitle = $item->getTranslation('title', 'ar', false) ?: $item->title;
                                                                    $itemType = $item->type instanceof LessonTypeEnum ? $item->type->value : (string) $item->type;
                                                                    $isLink = $itemType === LessonTypeEnum::Link->value && filled($item->link_url);
                                                                    $itemUrl = $isLink
                                                                        ? (Str::startsWith($item->link_url, ['http://', 'https://']) ? $item->link_url : url($item->link_url))
                                                                        : "/lesson?item={$item->id}";
                                                                    $icon = match (true) {
                                                                        $itemType === LessonTypeEnum::Assignments->value => 'fa-regular fa-clipboard',
                                                                        $itemType === LessonTypeEnum::Exams->value => 'fa-regular fa-circle-question',
                                                                        $isLink => 'fa-solid fa-link',
                                                                        filled($item->file_url) => 'fa-regular fa-file-pdf',
                                                                        default => 'fa-regular fa-circle-play',
                                                                    };
                                                                    $itemExamIsOpen = $itemType !== LessonTypeEnum::Exams->value || $lessonItemIsOpen($item);
                                                                    $itemAvailabilityText = $itemType === LessonTypeEnum::Exams->value && ! $itemExamIsOpen
                                                                        ? $lessonItemAvailabilityText($item)
                                                                        : null;
                                                                    $isLocked = ! $lessonIsOpen || ! $item->is_free || ! $itemExamIsOpen;
                                                                @endphp

                                                                <div class="p-3.5 flex items-center justify-between text-xs {{ $isLocked ? 'opacity-75' : '' }}" wire:key="lesson-item-{{ $item->id }}">
                                                                    <div class="flex items-center gap-3">
                                                                        <i class="{{ $icon }} text-gray-400 text-sm"></i>
                                                                        @if (! $isLocked)
                                                                            <a
                                                                                href="{{ $itemUrl }}"
                                                                                @if ($isLink) target="_blank" rel="noopener noreferrer" @endif
                                                                                class="font-semibold text-gray-700"
                                                                                style="--theme-color: {{ $themeColor }}"
                                                                                onmouseover="this.style.color=this.style.getPropertyValue('--theme-color')"
                                                                                onmouseout="this.style.color=''"
                                                                            >
                                                                                {{ $itemTitle }}
                                                                            </a>
                                                                            <span class="bg-emerald-50 text-emerald-600 text-[9px] font-bold px-2 py-0.5 rounded">مجاني</span>
                                                                        @else
                                                                            <span class="font-medium text-gray-600">{{ $itemTitle }}</span>
                                                                            <span class="bg-gray-100 text-gray-500 text-[9px] font-bold px-2 py-0.5 rounded">{{ ! $lessonIsOpen ? 'غير متاح الآن' : ($itemAvailabilityText ?: 'مغلق') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    @if ($isLocked)
                                                                        <i class="fa-solid fa-lock text-gray-300 text-xs"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="p-4 text-xs text-gray-500">لا توجد عناصر داخل هذه الحصة حالياً.</div>
                                                    @endif
                                                </div>
                                            </details>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl bg-gray-50 p-8 text-center text-sm font-semibold text-gray-400">
                            لا توجد دروس مضافة لهذا الكورس حالياً.
                        </div>
                    @endif
                </div>

                <aside class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex flex-wrap gap-2 mb-5">
                            @if ($gradeName)
                                <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">{{ $gradeName }}</span>
                            @endif
                            @if ($trackName)
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">{{ $trackName }}</span>
                            @endif
                        </div>

                        @if ($course->outcomes->isNotEmpty())
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 mb-6">
                                <p class="text-xs font-bold text-emerald-700 leading-relaxed">
                                    <i class="fa-solid fa-circle-check ml-2"></i>
                                    {{ $course->outcomes->first()->getTranslation('title', 'ar', false) ?: $course->outcomes->first()->title }}
                                </p>
                            </div>
                        @endif

                        <div class="border-t border-gray-100 pt-5 space-y-2">
                            <span class="block text-xs text-gray-400 font-bold">سعر الاشتراك الشهري</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-blue-950">{{ $monthlyPrice ?? '—' }}</span>
                                <span class="text-sm font-bold text-gray-500">جنيه</span>
                            </div>
                        </div>

                        <div class="grid gap-3 pt-6">
                            <a href="/cart?course={{ $course->id }}" class="w-full text-white font-bold text-sm py-4 rounded-2xl transition-all text-center" style="background-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $themeColorDark }}'" onmouseout="this.style.backgroundColor='{{ $themeColor }}'">
                                <i class="fa-solid fa-cart-plus ml-2"></i>
                                أضف إلى سلة التعلم
                            </a>
                            <a href="/checkout?course={{ $course->id }}" class="w-full border font-bold text-sm py-4 rounded-2xl transition-all text-center" style="border-color: {{ $themeColor }}66; color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $themeColor }}14'" onmouseout="this.style.backgroundColor=''">
                                <i class="fa-solid fa-bolt ml-2"></i>
                                اشترك الآن
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-base font-black text-blue-950 mb-4">ماذا ستتعلم في هذا الكورس؟</h3>
                        @if ($course->outcomes->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($course->outcomes as $outcome)
                                    <li class="flex items-start gap-2 text-xs text-gray-600 font-semibold" wire:key="outcome-{{ $outcome->id }}">
                                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                        <span>{{ $outcome->getTranslation('title', 'ar', false) ?: $outcome->title }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-400 font-semibold">سيتم إضافة أهداف التعلم قريباً.</p>
                        @endif
                    </div>
                </aside>
            </div>
        @endif
    </section>
</div>
