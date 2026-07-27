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
    $track = $accountSubject?->gradeSubject?->track;
    $lessonTitle = $lesson?->getTranslation('title', 'ar', false) ?: $lesson?->title;
    $itemTitle = $lessonItem?->getTranslation('title', 'ar', false) ?: $lessonItem?->title;
    $itemDescription = $lessonItem?->getTranslation('description', 'ar', false) ?: $lessonItem?->description;
    $courseTitle = $course?->getTranslation('title', 'ar', false) ?: $course?->title;
    $subjectName = $subject?->name;
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
    $formatDurationSeconds = function (?int $seconds): ?string {
        if (blank($seconds) || $seconds <= 0) {
            return null;
        }

        if ($seconds < 60) {
            return $seconds.' ثانية';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds === 0) {
            return $minutes.' دقيقة';
        }

        return sprintf('%d:%02d دقيقة', $minutes, $remainingSeconds);
    };
    $formatDurationMinutes = fn (?int $minutes): ?string => filled($minutes) && $minutes > 0
        ? $minutes.' دقيقة'
        : null;

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
    $isAuthenticated = auth()->check();
    $hasCourseAccess = (bool) ($hasCourseSubscription ?? false);
    $activeLessonItemHasAccess = $isAuthenticated && (bool) ($lessonItem?->is_free || $hasCourseAccess);
    $attemptLimit = $attempts['limit'] ?? null;
    $usedAttempts = $attempts['used'] ?? 0;
    $remainingAttempts = $attempts['remaining'] ?? null;
    $attemptsText = $attemptLimit === null
        ? 'غير محدود'
        : $usedAttempts.' / '.$attemptLimit.($remainingAttempts === 0 ? ' — انتهت المحاولات' : ' — متبقي '.$remainingAttempts);
    $videoProgressPercentage = (int) ($studentVideoProgress?->progress_percentage ?? 0);
    $videoProgressId = $studentVideoProgress?->id;
    $videoLastPositionSeconds = (int) ($studentVideoProgress?->last_position_seconds ?? 0);
    $videoWatchedSeconds = (int) ($studentVideoProgress?->watched_seconds ?? 0);
    $videoCompletedWatchCount = (int) ($completedVideoWatchCount ?? 0);
    $videoViewLimit = filled($lesson?->num_of_video_views) && $lesson->num_of_video_views > 0 ? (int) $lesson->num_of_video_views : null;
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
                    @elseif (! $activeLessonItemHasAccess)
                        <div class="bg-slate-50 border border-slate-100 rounded-[24px] p-8 text-center shadow-sm">
                            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4 text-xl">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-black text-blue-950">{{ $itemTitle }}</h1>
                            <p class="text-sm text-gray-500 font-semibold mt-3">
                                {{ $isAuthenticated ? 'هذا العنصر متاح للمشتركين في الكورس فقط.' : 'يجب تسجيل الدخول أولاً لمشاهدة محتوى الدرس.' }}
                            </p>
                            <a href="{{ $isAuthenticated ? '/checkout?course='.$course?->id : '/login' }}" class="inline-flex mt-4 text-white text-sm font-bold py-3 px-8 rounded-xl transition-all" style="background-color: {{ $activeColor }}" onmouseover="this.style.backgroundColor='{{ $activeHoverColor }}'" onmouseout="this.style.backgroundColor='{{ $activeColor }}'">
                                {{ $isAuthenticated ? 'اشترك الآن' : 'تسجيل الدخول' }}
                            </a>
                        </div>
                    @elseif ($contentType === 'video')
                        @once
                            <script src="https://assets.mediadelivery.net/playerjs/playerjs-latest.min.js"></script>
                        @endonce

                        <div
                            x-data="{
                                progress: {{ $videoProgressPercentage }},
                                progressId: @js($videoProgressId),
                                lastPosition: {{ $videoLastPositionSeconds }},
                                watchedSeconds: {{ $videoWatchedSeconds }},
                                watchedSinceLastSave: 0,
                                previousPosition: null,
                                duration: {{ (int) ($lessonItem->duration_seconds ?? 0) }},
                                player: null,
                                playerInitAttempts: 0,
                                saving: false,
                                saveTimer: null,
                                viewLimitReached: @js((bool) ($videoViewLimitReached ?? false)),
                                init() {
                                    this.$nextTick(() => this.initBunnyPlayer());

                                    window.addEventListener('beforeunload', () => {
                                        this.saveProgress(true);
                                    });
                                },
                                initBunnyPlayer() {
                                    const iframe = this.$refs.bunnyPlayer;

                                    if (! iframe) {
                                        return;
                                    }

                                    if (! window.playerjs) {
                                        this.playerInitAttempts += 1;

                                        if (this.playerInitAttempts <= 40) {
                                            window.setTimeout(() => this.initBunnyPlayer(), 250);
                                        }

                                        return;
                                    }

                                    const player = new playerjs.Player(iframe);
                                    this.player = player;

                                    player.on('ready', () => {
                                        player.getDuration((duration) => {
                                            const parsedDuration = Number(duration);

                                            if (Number.isFinite(parsedDuration) && parsedDuration > 0) {
                                                this.duration = Math.ceil(parsedDuration);
                                            }

                                            if (this.lastPosition > 3 && this.duration > 0 && this.lastPosition < this.duration - 5) {
                                                player.setCurrentTime(this.lastPosition);
                                            }
                                        });
                                    });

                                    player.on('timeupdate', (payload) => {
                                        let data = payload;

                                        if (typeof payload === 'string') {
                                            try {
                                                data = JSON.parse(payload);
                                            } catch (error) {
                                                return;
                                            }
                                        }
                                        const currentPosition = Number(data?.seconds ?? 0);
                                        const duration = Number(data?.duration ?? this.duration ?? 0);

                                        if (! Number.isFinite(currentPosition) || currentPosition < 0) {
                                            return;
                                        }

                                        if (Number.isFinite(duration) && duration > 0) {
                                            this.duration = Math.ceil(duration);
                                        }

                                        if (this.previousPosition !== null) {
                                            const delta = currentPosition - this.previousPosition;

                                            if (delta > 0 && delta <= 5) {
                                                this.watchedSinceLastSave += delta;
                                                this.watchedSeconds += delta;
                                                this.updateLocalProgress();
                                            }
                                        }

                                        this.previousPosition = currentPosition;
                                        this.lastPosition = Math.ceil(currentPosition);

                                        if (this.watchedSinceLastSave >= 10) {
                                            this.saveProgress();
                                        }
                                    });

                                    player.on('pause', () => this.saveProgress(true));
                                    player.on('ended', () => this.saveProgress(true, true));
                                },
                                updateLocalProgress() {
                                    if (! Number.isFinite(this.duration) || this.duration <= 0) {
                                        return;
                                    }

                                    this.progress = Math.min(100, Math.floor((this.watchedSeconds / this.duration) * 100));
                                },
                                saveProgress(force = false, ended = false) {
                                    if (this.saving || (! force && this.watchedSinceLastSave < 10)) {
                                        return;
                                    }

                                    const watchedDelta = Math.ceil(this.watchedSinceLastSave);
                                    this.watchedSinceLastSave = 0;
                                    this.saving = true;

                                    this.$wire.$call(
                                        'saveVideoProgress',
                                        {{ (int) $lessonItem->id }},
                                        this.lastPosition,
                                        this.duration,
                                        watchedDelta,
                                        ended,
                                        this.progressId,
                                    ).then((response) => {
                                        this.progressId = response.progressId ?? this.progressId;
                                        this.progress = response.progressPercentage ?? this.progress;
                                        this.lastPosition = response.lastPositionSeconds ?? this.lastPosition;
                                        this.watchedSeconds = response.watchedSeconds ?? this.watchedSeconds;
                                        this.viewLimitReached = response.viewLimitReached ?? false;
                                    }).finally(() => {
                                        this.saving = false;
                                    });
                                },
                            }"
                            class="space-y-4"
                        >
                        <div class="relative bg-black rounded-3xl overflow-hidden aspect-video shadow-lg">
                            @if (filled($signedVideoUrl ?? null))
                                <iframe
                                    x-ref="bunnyPlayer"
                                    id="bunny-player-{{ $lessonItem->id }}"
                                    src="{{ $signedVideoUrl }}"
                                    class="absolute inset-0 w-full h-full"
                                    style="border: 0;"
                                    allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
                                    allowfullscreen
                                ></iframe>
                            @elseif ($videoViewLimitReached ?? false)
                                <div class="w-full h-full flex flex-col items-center justify-center text-white gap-3 text-center px-6">
                                    <i class="fa-solid fa-circle-check text-5xl text-white/70"></i>
                                    <p class="text-sm font-bold">تم استهلاك عدد مرات مشاهدة هذا الفيديو.</p>
                                    @if ($videoViewLimit)
                                        <p class="text-xs text-white/60">عدد المشاهدات المكتملة: {{ $videoCompletedWatchCount }} / {{ $videoViewLimit }}</p>
                                    @endif
                                </div>
                            @elseif (filled($lessonItem->video_url))
                                <div class="w-full h-full flex flex-col items-center justify-center text-white gap-3 text-center px-6">
                                    <i class="fa-solid fa-shield-halved text-5xl text-white/70"></i>
                                    <p class="text-sm font-bold">تعذر تجهيز رابط تشغيل الفيديو المحمي حالياً.</p>
                                </div>
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
                                <span class="text-gray-700">{{ $formatDurationSeconds($lessonItem->duration_seconds) ?? 'غير محددة' }}</span>
                            </div>
                            <div class="flex items-center gap-3 flex-1 sm:max-w-md">
                                <span class="text-xs font-black" style="color: {{ $activeColor }}" x-text="`${progress}%`">{{ $videoProgressPercentage }}%</span>
                                <div class="w-full h-2 rounded-full overflow-hidden" style="background-color: {{ $activeSoftColor }}">
                                    <div class="h-full rounded-full transition-all" x-bind:style="`width: ${progress}%; background-color: {{ $activeColor }}`"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-400 whitespace-nowrap">تقدم المشاهدة الفعلي</span>
                            </div>
                            @if ($videoViewLimit)
                                <div class="text-xs font-bold text-gray-400">
                                    المشاهدات المكتملة:
                                    <span class="text-gray-700">{{ $videoCompletedWatchCount }} / {{ $videoViewLimit }}</span>
                                </div>
                            @endif
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
                                        مدة الحل: {{ $formatDurationMinutes($lessonAssignments->max('duration_minutes')) ?? $formatDurationSeconds($lessonItem->duration_seconds) ?? '—' }}
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
                                        مدة الاختبار: {{ $formatDurationMinutes($lessonExams->max('duration_minutes')) ?? $formatDurationSeconds($lessonItem->duration_seconds) ?? '—' }}
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
                                    $playlistHasAccess = $playlistItem->is_free || $hasCourseAccess;
                                    $playlistIsLocked = ! $lessonIsOpen || ! $playlistHasAccess || ! $playlistItemIsOpen;
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
                                                    {{ ! $lessonIsOpen ? 'غير متاح الآن' : ($playlistAvailabilityText ?: ($formatDurationSeconds($playlistItem->duration_seconds) ?? 'مغلق')) }}
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
                                                    {{ $formatDurationSeconds($playlistItem->duration_seconds) ?? 'مجاني' }}
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
