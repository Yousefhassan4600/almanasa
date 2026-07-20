@php
    use App\Enums\ProviderType;
    use App\Enums\LessonTypeEnum;
    use Illuminate\Support\Str;

    $lesson = $lessonItem?->lesson;
    $course = $lesson?->course;
    $teacher = $course?->academyTeacher;
    $isStandaloneTeacher = $course?->provider?->type === ProviderType::StandaloneTeacher;
    $accountSubject = $course?->accountSubject;
    $grade = $accountSubject?->gradeSubject?->grade;
    $subject = $accountSubject?->gradeSubject?->subject;
    $track = $subject?->track;
    $lessonTitle = $lesson?->getTranslation('title', 'ar', false) ?: $lesson?->title;
    $itemTitle = $lessonItem?->getTranslation('title', 'ar', false) ?: $lessonItem?->title;
    $itemDescription = $lessonItem?->getTranslation('description', 'ar', false) ?: $lessonItem?->description;
    $courseTitle = $course?->getTranslation('title', 'ar', false) ?: $course?->title;
    $subjectName = $subject?->getTranslation('name', 'ar', false) ?: $subject?->name;
    $trackName = $track?->getTranslation('name', 'ar', false) ?: $track?->name;
    $teacherName = $isStandaloneTeacher
        ? ($course?->provider?->owner?->name ?: 'المعلم')
        : ($teacher?->teacher?->owner?->name ?: 'المعلم');
    $activeColor = $course?->provider?->websitePrimaryColor() ?? '#5D3FD3';
    $activeHoverColor = $course?->provider?->websiteSecondaryColor() ?? '#4c32b3';
    $activeSoftColor = $activeColor.'12';
    $activeMutedTextColor = $isStandaloneTeacher ? '#FDE68A' : '#DDD6FE';
    $lessonItemType = $lessonItem?->type instanceof \App\Enums\LessonTypeEnum ? $lessonItem->type->value : (string) $lessonItem?->type;
    $lessonAssignments = collect([$lessonItem?->assignment])->filter();
    $lessonExams = collect([$lessonItem?->exam])->filter();

    $linkUrl = filled($lessonItem?->link_url)
        ? (Str::startsWith($lessonItem->link_url, ['http://', 'https://']) ? $lessonItem->link_url : url($lessonItem->link_url))
        : null;

    $contentType = match (true) {
        $lessonItemType === LessonTypeEnum::Assignments->value => 'assignment',
        $lessonItemType === LessonTypeEnum::Exams->value => 'exam',
        $lessonItemType === LessonTypeEnum::Link->value && filled($linkUrl) => 'link',
        filled($lessonItem?->file_url) => 'file',
        default => 'video',
    };

    $contentIcon = match ($contentType) {
        'assignment' => 'fa-regular fa-clipboard',
        'exam' => 'fa-regular fa-circle-question',
        'link' => 'fa-solid fa-link',
        'file' => 'fa-regular fa-file-pdf',
        default => 'fa-regular fa-circle-play',
    };
    $lessonIsOpen = $lesson?->isCurrentlyOpen() ?? false;
    $lessonAvailabilityText = match (true) {
        $lessonIsOpen => null,
        filled($lesson?->starts_at) && $lesson->starts_at->isFuture() => 'هذا الدرس سيفتح في '.$lesson->starts_at->format('Y-m-d H:i'),
        filled($lesson?->ends_at) && $lesson->ends_at->isPast() => 'انتهت مدة إتاحة هذا الدرس في '.$lesson->ends_at->format('Y-m-d H:i'),
        default => 'هذا الدرس مغلق حالياً.',
    };
    $lessonItemIsOpen = fn ($item): bool => filled($item) && $item->isCurrentlyOpen();
    $lessonItemAvailabilityText = function ($item, string $fallback = 'العنصر غير متاح حالياً.'): string {
        if (blank($item)) {
            return $fallback;
        }

        if (! $item->is_active) {
            return 'غير مفعل حالياً';
        }

        if (filled($item->starts_at) && $item->starts_at->isFuture()) {
            return 'يفتح في '.$item->starts_at->format('Y-m-d H:i');
        }

        if (filled($item->ends_at) && $item->ends_at->isPast()) {
            return 'انتهى في '.$item->ends_at->format('Y-m-d H:i');
        }

        return $fallback;
    };
    $activeLessonItemIsOpen = $lessonItemIsOpen($lessonItem);
    $activeLessonItemAvailabilityText = $lessonItemAvailabilityText($lessonItem);
    $attemptLimit = $attempts['limit'] ?? null;
    $usedAttempts = $attempts['used'] ?? 0;
    $remainingAttempts = $attempts['remaining'] ?? null;
    $attemptsText = $attemptLimit === null
        ? 'غير محدود'
        : $usedAttempts.' / '.$attemptLimit.($remainingAttempts === 0 ? ' — انتهت المحاولات' : ' — متبقي '.$remainingAttempts);
@endphp

<div class="bg-white" dir="rtl">
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-6">
        @if (! $lessonItem)
            <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                <p class="text-sm font-bold text-blue-950">لم يتم العثور على عنصر الدرس المطلوب.</p>
                <a href="/subjects" class="inline-flex mt-4 text-sm font-bold" style="color: {{ $activeColor }}">العودة للمواد</a>
            </div>
        @else
            <nav class="flex flex-wrap items-center gap-1.5 text-xs text-gray-400 mb-6 font-bold">
                <a href="/" style="--active-color: {{ $activeColor }}" class="hover:text-[var(--active-color)]">الرئيسية</a>
                <span>/</span>
                <a href="/subjects" style="--active-color: {{ $activeColor }}" class="hover:text-[var(--active-color)]">المواد</a>
                @if ($subjectName)
                    <span>/</span>
                    <a href="/teachers?subject={{ $accountSubject?->id }}" style="--active-color: {{ $activeColor }}" class="hover:text-[var(--active-color)]">{{ $subjectName }}</a>
                @endif
                @if ($lessonTitle)
                    <span>/</span>
                    <span>{{ $lessonTitle }}</span>
                @endif
                <span>/</span>
                <span class="text-gray-600">{{ $itemTitle }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8 space-y-5">
                    @if (! $lessonIsOpen)
                        <div class="bg-slate-50 border border-slate-100 rounded-[24px] p-8 text-center shadow-sm">
                            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4 text-xl">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-black text-blue-950">{{ $itemTitle }}</h1>
                            <p class="text-sm text-gray-500 font-semibold mt-3">{{ $lessonAvailabilityText }}</p>
                            <p class="text-xs text-gray-400 font-medium mt-2">العنصر ظاهر في قائمة الدروس، لكن المحتوى لا يمكن فتحه خارج فترة الإتاحة.</p>
                        </div>
                    @elseif (! $activeLessonItemIsOpen)
                        <div class="bg-slate-50 border border-slate-100 rounded-[24px] p-8 text-center shadow-sm">
                            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4 text-xl">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-black text-blue-950">{{ $itemTitle }}</h1>
                            <p class="text-sm text-gray-500 font-semibold mt-3">{{ $activeLessonItemAvailabilityText }}</p>
                            <p class="text-xs text-gray-400 font-medium mt-2">العنصر ظاهر في قائمة الدروس، لكن المحتوى لا يمكن فتحه خارج فترة الإتاحة أو أثناء إيقافه.</p>
                        </div>
                    @elseif ($contentType === 'video')
                        <div class="relative bg-black rounded-3xl overflow-hidden aspect-video shadow-lg">
                            @if (filled($lessonItem->video_url))
                                <video class="w-full h-full" src="{{ $lessonItem->video_url }}" controls controlsList="nodownload"></video>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-white gap-3">
                                    <i class="fa-regular fa-circle-play text-5xl text-white/70"></i>
                                    <p class="text-sm font-bold">لم يتم إضافة رابط الفيديو بعد.</p>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
                                <span>مدة العنصر:</span>
                                <span class="text-gray-700">{{ $lessonItem->duration_minutes ? $lessonItem->duration_minutes.' دقيقة' : 'غير محددة' }}</span>
                            </div>
                            <div class="flex items-center gap-3 flex-1 sm:max-w-md">
                                <span class="text-xs font-black" style="color: {{ $activeColor }}">0%</span>
                                <div class="w-full h-2 rounded-full overflow-hidden" style="background-color: {{ $activeSoftColor }}">
                                    <div class="h-full rounded-full" style="width: 0%; background-color: {{ $activeColor }}"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-400 whitespace-nowrap">تقدم المشاهدة الفعلي</span>
                            </div>
                        </div>
                    @elseif ($contentType === 'assignment')
                        <div class="border-r-[6px] rounded-[24px] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm" style="background-color: {{ $activeSoftColor }}; border-color: {{ $activeColor }}">
                            <div class="flex items-center gap-4 text-right">
                                <div class="w-12 h-12 rounded-2xl text-white flex items-center justify-center text-lg shrink-0" style="background-color: {{ $activeColor }}">
                                    <i class="fa-regular fa-clipboard"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl sm:text-2xl font-black text-gray-800">{{ $itemTitle }}</h1>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">
                                        مدة الحل: {{ $lessonAssignments->max('duration_minutes') ?? $lessonItem->duration_minutes ?? '—' }} دقيقة
                                    </span>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">
                                        عدد المحاولات: {{ $attemptsText }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                @foreach ($lessonAssignments as $assignment)
                                    <a href="/home_work?assignment={{ $assignment->id }}&item={{ $lessonItem->id }}" class="w-full sm:w-auto text-white text-sm font-bold py-3 px-8 rounded-xl transition-all text-center" style="background-color: {{ $activeColor }}" onmouseover="this.style.backgroundColor='{{ $activeHoverColor }}'" onmouseout="this.style.backgroundColor='{{ $activeColor }}'">
                                        {{ $assignment->getTranslation('title', 'ar', false) ?: $assignment->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($contentType === 'exam')
                        <div class="bg-[#FFF1F2] border-r-[6px] border-[#E11D48] rounded-[24px] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
                            <div class="flex items-center gap-4 text-right">
                                <div class="w-12 h-12 rounded-2xl bg-[#E11D48] text-white flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-regular fa-circle-question"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl sm:text-2xl font-black text-gray-800">{{ $itemTitle }}</h1>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">
                                        مدة الاختبار: {{ $lessonExams->max('duration_minutes') ?? $lessonItem->duration_minutes ?? '—' }} دقيقة
                                    </span>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">
                                        عدد المحاولات: {{ $attemptsText }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                @foreach ($lessonExams as $exam)
                                    @php
                                        $currentExamIsOpen = $lessonItemIsOpen($lessonItem);
                                    @endphp

                                    @if ($currentExamIsOpen)
                                        <a href="/quiz?exam={{ $exam->id }}&item={{ $lessonItem->id }}" class="w-full sm:w-auto bg-[#E11D48] hover:bg-[#be123c] text-white text-sm font-bold py-3 px-8 rounded-xl transition-all text-center">
                                            {{ $exam->getTranslation('title', 'ar', false) ?: $exam->title }}
                                        </a>
                                    @else
                                        <div class="w-full sm:w-auto bg-gray-100 text-gray-400 text-sm font-bold py-3 px-8 rounded-xl text-center cursor-not-allowed border border-gray-200">
                                            <span class="block">{{ $exam->getTranslation('title', 'ar', false) ?: $exam->title }}</span>
                                            <span class="block text-[10px] mt-1 font-semibold">{{ $lessonItemAvailabilityText($lessonItem, 'الاختبار مغلق حالياً.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif ($contentType === 'link')
                        <div class="bg-[#EFF6FF] border-r-[6px] border-[#2563EB] rounded-[24px] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
                            <div class="flex items-center gap-4 text-right">
                                <div class="w-12 h-12 rounded-2xl bg-[#2563EB] text-white flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl sm:text-2xl font-black text-gray-800">{{ $itemTitle }}</h1>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">رابط خارجي للدرس</span>
                                </div>
                            </div>
                            <a href="{{ $linkUrl }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-[#2563EB] hover:bg-[#1d4ed8] text-white text-sm font-bold py-3 px-8 rounded-xl transition-all text-center">
                                فتح الرابط
                            </a>
                        </div>
                    @else
                        <div class="bg-[#FCF6ED] border-r-[6px] border-[#D97706] rounded-[24px] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
                            <div class="flex items-center gap-4 text-right">
                                <div class="w-12 h-12 rounded-2xl bg-[#D97706] text-white flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-regular fa-file-pdf"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl sm:text-2xl font-black text-gray-800">{{ $itemTitle }}</h1>
                                    <span class="text-xs text-gray-400 block mt-1 font-semibold">ملف مرفق للدرس</span>
                                </div>
                            </div>
                            <a href="{{ asset('storage/'.$lessonItem->file_url) }}" target="_blank" class="w-full sm:w-auto bg-[#D97706] hover:bg-[#b45309] text-white text-sm font-bold py-3 px-8 rounded-xl transition-all text-center">
                                تحميل الملف
                            </a>
                        </div>
                    @endif

                    <div class="pt-2 flex items-center justify-between">
                        <div class="text-right space-y-1">
                            <h2 class="text-lg sm:text-xl font-black text-blue-950">{{ $itemTitle }}</h2>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 font-semibold">
                                <span class="flex items-center gap-1"><i class="fa-regular fa-user"></i> {{ $teacherName }}</span>
                                @if ($grade?->name)
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-graduation-cap"></i> {{ $grade->name }}</span>
                                @endif
                                @if ($trackName)
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-book-open"></i> {{ $trackName }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($itemDescription)
                        <div class="bg-amber-50 border-r-4 border-amber-500 rounded-2xl p-4 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-amber-600 text-lg mt-0.5"></i>
                            <div class="text-right space-y-1">
                                <h5 class="text-xs font-black text-amber-900">ملاحظة:</h5>
                                <p class="text-xs text-amber-800/90 font-medium leading-relaxed">{{ $itemDescription }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <aside class="lg:col-span-4 space-y-4">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="text-white p-5 text-right space-y-1" style="background-color: {{ $activeColor }}">
                            <h3 class="text-base font-black tracking-wide">قائمة محتوى الدرس</h3>
                            <p class="text-xs font-medium" style="color: {{ $activeMutedTextColor }}">{{ $lessonTitle ?: $courseTitle }}</p>
                        </div>

                        <div class="p-2 space-y-1.5 max-h-[520px] overflow-y-auto">
                            @foreach ($lessonItems as $playlistItem)
                                @php
                                    $playlistTitle = $playlistItem->getTranslation('title', 'ar', false) ?: $playlistItem->title;
                                    $playlistItemType = $playlistItem->type instanceof LessonTypeEnum ? $playlistItem->type->value : (string) $playlistItem->type;
                                    $playlistIsLink = $playlistItemType === LessonTypeEnum::Link->value && filled($playlistItem->link_url);
                                    $playlistUrl = $playlistIsLink
                                        ? (Str::startsWith($playlistItem->link_url, ['http://', 'https://']) ? $playlistItem->link_url : url($playlistItem->link_url))
                                        : "/lesson?item={$playlistItem->id}";
                                    $playlistType = match (true) {
                                        $playlistItemType === LessonTypeEnum::Assignments->value => 'assignment',
                                        $playlistItemType === LessonTypeEnum::Exams->value => 'exam',
                                        $playlistIsLink => 'link',
                                        filled($playlistItem->file_url) => 'file',
                                        default => 'video',
                                    };
                                    $playlistItemIsOpen = $lessonItemIsOpen($playlistItem);
                                    $playlistAvailabilityText = ! $playlistItemIsOpen
                                        ? $lessonItemAvailabilityText($playlistItem)
                                        : null;
                                    $playlistIcon = match ($playlistType) {
                                        'assignment' => 'fa-regular fa-clipboard',
                                        'exam' => 'fa-regular fa-circle-question',
                                        'link' => 'fa-solid fa-link',
                                        'file' => 'fa-regular fa-file-pdf',
                                        default => 'fa-solid fa-play',
                                    };
                                    $isActive = $playlistItem->is($lessonItem);
                                    $playlistIsLocked = ! $lessonIsOpen || ! $playlistItem->is_free || ! $playlistItemIsOpen;
                                    $playlistClass = 'p-3.5 flex items-center justify-between rounded-2xl transition-all '.($isActive ? 'border' : 'bg-white hover:bg-gray-50 border border-transparent text-gray-600');
                                    $playlistActiveStyle = $isActive ? 'background-color: '.$activeSoftColor.'; border-color: '.$activeColor.'33; color: '.$activeColor : '';
                                @endphp

                                @if ($playlistIsLocked)
                                    <div wire:key="lesson-playlist-item-{{ $playlistItem->id }}" class="{{ $playlistClass }}" style="{{ $playlistActiveStyle }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg {{ $isActive ? '' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center text-xs shrink-0" style="{{ $isActive ? 'background-color: '.$activeColor.'1A; color: '.$activeColor : '' }}">
                                                <i class="{{ $playlistIcon }}"></i>
                                            </span>
                                            <div class="text-right">
                                                <h4 class="text-xs font-bold {{ $isActive ? '' : 'text-blue-950' }}" style="{{ $isActive ? 'color: '.$activeColor : '' }}">{{ $playlistTitle }}</h4>
                                                <span class="text-[10px] {{ $isActive ? '' : 'text-gray-400' }} block mt-0.5" style="{{ $isActive ? 'color: '.$activeColor : '' }}">
                                                    {{ ! $lessonIsOpen ? 'غير متاح الآن' : ($playlistAvailabilityText ?: ($playlistItem->duration_minutes ? $playlistItem->duration_minutes.' دقيقة' : 'مغلق')) }}
                                                </span>
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-lock text-gray-300 text-xs"></i>
                                    </div>
                                @else
                                    <a
                                        href="{{ $playlistUrl }}"
                                        @if ($playlistIsLink) target="_blank" rel="noopener noreferrer" @endif
                                        wire:key="lesson-playlist-item-{{ $playlistItem->id }}"
                                        class="{{ $playlistClass }}"
                                        style="{{ $playlistActiveStyle }}"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg {{ $isActive ? '' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center text-xs shrink-0" style="{{ $isActive ? 'background-color: '.$activeColor.'1A; color: '.$activeColor : '' }}">
                                                <i class="{{ $playlistIcon }}"></i>
                                            </span>
                                            <div class="text-right">
                                                <h4 class="text-xs font-bold {{ $isActive ? '' : 'text-blue-950' }}" style="{{ $isActive ? 'color: '.$activeColor : '' }}">{{ $playlistTitle }}</h4>
                                                <span class="text-[10px] {{ $isActive ? '' : 'text-gray-400' }} block mt-0.5" style="{{ $isActive ? 'color: '.$activeColor : '' }}">
                                                    {{ $playlistItem->duration_minutes ? $playlistItem->duration_minutes.' دقيقة' : 'مجاني' }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach

                            <a href="/packages" class="w-full border font-bold text-sm py-3.5 rounded-xl transition-colors bg-transparent flex items-center justify-center gap-2" style="border-color: {{ $activeColor }}33; color: {{ $activeColor }}" onmouseover="this.style.backgroundColor='{{ $activeSoftColor }}'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fa-solid fa-bolt text-xs"></i>
                                اشترك الآن
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-5 border border-gray-100 flex items-start justify-between gap-4">
                        <div class="text-right space-y-1">
                            <h4 class="text-xs font-black text-blue-950">واجهتك مشكلة؟</h4>
                            <p class="text-[11px] text-gray-400 font-medium">تواصل مع الدعم الفني لحل أي مشكلة تقنية في المشاهدة.</p>
                            <a href="#" class="text-[11px] font-bold inline-block pt-1 hover:underline" style="color: {{ $activeColor }}">مركز المساعدة ←</a>
                        </div>
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs" style="background-color: {{ $activeColor }}1A; color: {{ $activeColor }}">
                            <i class="fa-regular fa-circle-question"></i>
                        </span>
                    </div>
                </aside>
            </div>
        @endif
    </section>
</div>
