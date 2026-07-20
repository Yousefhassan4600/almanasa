@php
    use App\Enums\ProviderType;

    $assessment = $attempt?->attemptable;
    $title = $assessment?->getTranslation('title', 'ar', false) ?: $assessment?->title;
    $isExam = $assessmentType === 'exam';
    $isStandaloneTeacher = $provider?->type === ProviderType::StandaloneTeacher;
    $themeColor = $provider?->websitePrimaryColor() ?? '#5D3FD3';
    $themeHoverColor = $provider?->websiteSecondaryColor() ?? '#4a32b0';
    $themeSoftColor = $themeColor.'12';
    $label = $isExam ? 'الاختبار' : 'الواجب';
    $statusSlug = $attempt?->currentStatus?->type?->slug;
    $hasPendingManualAnswers = $attempt?->studentAnswers?->contains(fn ($answer) => $answer->requires_manual_grading && $answer->score === null) ?? false;
    $score = $attempt ? (float) $attempt->score : 0;
    $maxScore = $attempt ? (float) $attempt->max_score : 0;
    $percentage = $attempt ? (float) $attempt->percentage : 0;
    $scoreText = rtrim(rtrim(number_format($score, 2), '0'), '.');
    $maxScoreText = rtrim(rtrim(number_format($maxScore, 2), '0'), '.');
    $answersCount = $attempt?->studentAnswers?->count() ?? 0;
    $correctAnswersCount = $attempt?->studentAnswers?->filter(fn ($answer) => (bool) $answer->is_correct)->count() ?? 0;
    $examPercentage = $answersCount > 0 ? round(($correctAnswersCount / $answersCount) * 100) : 0;
    $summaryScoreText = $isExam ? (string) $correctAnswersCount : $scoreText;
    $summaryMaxScoreText = $isExam ? (string) $answersCount : $maxScoreText;
    $summaryPercentage = $isExam ? $examPercentage : $percentage;
    $durationSeconds = $assessment?->duration_minutes ? (int) $assessment->duration_minutes * 60 : null;
    $timeSpentSeconds = $attempt?->time_spent_seconds !== null
        ? (int) $attempt->time_spent_seconds
        : null;
    $timeSpentSeconds = $timeSpentSeconds !== null && $durationSeconds !== null
        ? min($timeSpentSeconds, $durationSeconds)
        : $timeSpentSeconds;
    $timeSpentText = $timeSpentSeconds !== null
        ? sprintf('%02d:%02d', intdiv((int) $timeSpentSeconds, 60), ((int) $timeSpentSeconds) % 60)
        : '—';
    $durationText = $assessment?->duration_minutes
        ? sprintf('%02d:00', (int) $assessment->duration_minutes)
        : '—';
    $statusText = $hasPendingManualAnswers
        ? 'في انتظار التصحيح اليدوي'
        : ($statusSlug === 'graded' ? 'تم التصحيح' : 'تم التسليم');
    $resultBadge = $hasPendingManualAnswers ? 'بانتظار التصحيح' : ($summaryPercentage >= 85 ? 'ممتاز!' : ($summaryPercentage >= 50 ? 'جيد!' : 'حاول مرة أخرى'));
    $resultHeading = match (true) {
        $hasPendingManualAnswers => 'تم تسليم الواجب',
        $isExam && $summaryPercentage >= 50 => 'أحسنت! لقد اجتزت الاختبار',
        $isExam => 'لم تجتز الاختبار',
        $summaryPercentage >= 50 => 'أحسنت! لقد أنهيت الواجب',
        default => 'تم تسليم الواجب',
    };
    $resultMessage = match (true) {
        $hasPendingManualAnswers => 'تم حفظ إجاباتك وسيتم تصحيح الأسئلة المقالية من المعلم.',
        $summaryPercentage >= 50 => 'لقد قدمت أداءً رائعاً. أنت تسير على الطريق الصحيح لتحقيق التميز الدراسي.',
        $isExam => 'راجع إجاباتك وحاول مرة أخرى بعد مذاكرة الدرس جيداً.',
        default => 'يمكنك مراجعة إجاباتك والمحاولة مرة أخرى إذا كان لديك محاولات متاحة.',
    };
    $course = $attempt?->course;
    $courseUrl = match (true) {
        $isStandaloneTeacher && filled($course?->account_subject_id) => "/single_teacher?subject={$course->account_subject_id}",
        filled($course?->academy_teacher_id) && filled($course?->account_subject_id) => "/single_teacher?teacher={$course->academy_teacher_id}&subject={$course->account_subject_id}",
        $isStandaloneTeacher => '/single_teacher',
        default => '/my_lessons',
    };
    $reviewUrl = $attempt
        ? ($isExam ? "/quiz_review?attempt={$attempt->id}" : "/home_work_done?attempt={$attempt->id}&review=1")
        : '/my_lessons';
@endphp

<div class="bg-white" dir="rtl">
    <section class="max-w-5xl mx-auto px-4 md:px-8 py-8">
        @if (! $isExam)
            <nav class="flex flex-wrap items-center gap-1.5 text-xs text-gray-400 mb-6 font-bold">
                <a href="/" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الرئيسية</a>
                <span>/</span>
                <a href="/my_lessons" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">دروسي</a>
                <span>/</span>
                <span class="text-gray-600">نتيجة {{ $label }}</span>
            </nav>
        @endif

        @if (! $attempt)
            <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                <p class="text-sm font-bold text-blue-950">لم يتم العثور على المحاولة المطلوبة.</p>
                <a href="/my_lessons" class="inline-flex mt-4 text-sm font-bold" style="color: {{ $themeColor }}">العودة لدروسي</a>
            </div>
        @elseif (! $showReview)
            <div class="min-h-screen p-4 md:p-8 flex flex-col items-center justify-center">
                <div class="w-full max-w-4xl bg-white p-6 md:p-10 space-y-8">
                    <section class="w-full max-w-2xl mx-auto flex flex-col items-center text-center space-y-8 bg-white border border-gray-100 rounded-[2rem] p-8 md:p-12 shadow-sm">
                        <div class="relative w-44 h-44 rounded-full flex flex-col items-center justify-center text-white shadow-xl" style="background-color: {{ $themeColor }}; box-shadow: 0 20px 35px {{ $themeColor }}33">
                            <span class="absolute -top-2 right-1 bg-[#46F0B4] text-blue-950 font-black text-xs px-3 py-1.5 rounded-full shadow-md">
                                {{ $resultBadge }}
                            </span>
                            <span class="text-4xl font-black tracking-tight">{{ $summaryScoreText }}/{{ $summaryMaxScoreText }}</span>
                            <span class="text-lg font-bold opacity-90 mt-1">{{ number_format($summaryPercentage, 0) }}%</span>
                        </div>

                        <div class="space-y-2">
                            <h2 class="text-3xl md:text-4xl font-black text-blue-950">{{ $resultHeading }}</h2>
                            <p class="text-sm md:text-base font-bold text-gray-400">{{ $resultMessage }}</p>
                            <p class="text-xs font-black text-gray-400">{{ $statusText }}</p>
                        </div>

                        <div class="w-full max-w-md bg-[#FAFAFA] border border-gray-100 rounded-3xl p-6 space-y-2">
                            <div class="text-2xl flex items-center justify-center" style="color: {{ $themeColor }}">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-400">الوقت المستغرق</p>
                            <h3 class="text-4xl font-black tracking-tight" style="color: {{ $themeColor }}">{{ $timeSpentText }}</h3>
                            <p class="text-[11px] font-bold text-gray-400">من أصل {{ $durationText }} دقيقة</p>
                        </div>

                        <div class="w-full max-w-md flex flex-col sm:flex-row items-center gap-4 pt-4">
                            <a
                                href="{{ $courseUrl }}"
                                class="w-full text-white font-black text-sm py-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg"
                                style="background-color: {{ $themeColor }}; box-shadow: 0 10px 24px {{ $themeColor }}26"
                                onmouseover="this.style.backgroundColor='{{ $themeHoverColor }}'"
                                onmouseout="this.style.backgroundColor='{{ $themeColor }}'"
                            >
                                <i class="fa-solid fa-shapes"></i>
                                <span>العودة للمادة</span>
                            </a>

                            <a
                                href="{{ $reviewUrl }}"
                                class="w-full bg-white border-2 font-black text-sm py-4 rounded-xl transition-all flex items-center justify-center gap-2"
                                style="border-color: {{ $themeColor }}; color: {{ $themeColor }}"
                                onmouseover="this.style.backgroundColor='{{ $themeSoftColor }}'"
                                onmouseout="this.style.backgroundColor='white'"
                            >
                                <i class="fa-regular fa-square-check"></i>
                                <span>مراجعة الإجابات الصحيحة</span>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        @endif

        @if ($attempt && $showReview)
            <div class="flex justify-start mb-6">
                <a
                    href="{{ $courseUrl }}"
                    class="inline-flex items-center justify-center gap-2 text-white font-black text-sm py-3 px-6 rounded-xl transition-all shadow-lg"
                    style="background-color: {{ $themeColor }}; box-shadow: 0 10px 24px {{ $themeColor }}26"
                    onmouseover="this.style.backgroundColor='{{ $themeHoverColor }}'"
                    onmouseout="this.style.backgroundColor='{{ $themeColor }}'"
                >
                    <i class="fa-solid fa-shapes"></i>
                    <span>العودة للمادة</span>
                </a>
            </div>
        @endif

        @if ($attempt && $showReview)
            <div class="space-y-4">
                @foreach ($attempt->studentAnswers as $answer)
                    @php
                        $question = $answer->question;
                        $isManual = $answer->requires_manual_grading;
                        $selectedOption = $answer->question_option;
                        $isCorrect = $answer->is_correct === true;
                        $questionMaxDegree = $answer->question_max_degree;
                    @endphp

                    <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-5 md:p-6" wire:key="attempt-answer-{{ $answer->id }}">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="space-y-3">
                                <h2 class="text-base md:text-lg font-black text-blue-950 leading-relaxed">{{ $question?->title }}</h2>

                                @if ($isManual)
                                    <div class="rounded-2xl bg-amber-50 border border-amber-100 p-4">
                                        <span class="block text-xs font-black text-amber-700 mb-2">إجابتك</span>
                                        <p class="text-sm text-amber-900 font-semibold leading-relaxed">{{ $answer->answer_text ?: '—' }}</p>
                                    </div>
                                @else
                                    <p class="text-sm font-bold text-gray-600">
                                        إجابتك:
                                        <span class="{{ $isCorrect ? 'text-emerald-600' : 'text-rose-600' }}">{{ $selectedOption?->title ?: '—' }}</span>
                                    </p>

                                    @if (! $isCorrect)
                                        <p class="text-xs font-bold text-emerald-600">الإجابة الصحيحة: {{ $answer->correct_answer ?: '—' }}</p>
                                    @endif
                                @endif
                            </div>

                            <div class="shrink-0 text-right md:text-left">
                                @if ($isManual && $answer->score === null)
                                    <span class="inline-flex bg-amber-50 text-amber-700 text-xs font-black px-3 py-1 rounded-full">بانتظار التصحيح</span>
                                @else
                                    <span class="inline-flex {{ $isCorrect || $answer->score > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }} text-xs font-black px-3 py-1 rounded-full">
                                        {{ number_format((float) ($answer->score ?? 0), 2) }} / {{ $questionMaxDegree !== null ? number_format($questionMaxDegree, 2) : '—' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
