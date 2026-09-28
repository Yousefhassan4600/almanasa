@php
    use App\Enums\ProviderType;
    use App\Enums\QuestionType;
    use App\Models\Exam;

    $title = \App\Support\WebsiteTranslation::value($assessment, 'title') ?: $assessment?->title;
    $isExam = $assessment instanceof Exam;
    $isStandaloneTeacher = $provider?->type === ProviderType::StandaloneTeacher;
    $themeColor = $provider?->websitePrimaryColor() ?? '#5D3FD3';
    $themeHoverColor = $provider?->websiteSecondaryColor() ?? '#4c32b3';
    $themeSoftColor = $themeColor.'12';
    $label = $isExam ? __('الاختبار') : __('الواجب');
    $duration = $assessment?->duration_minutes;
    $questionCount = $questions->count();
    $answeredCount = collect($answers)->filter(fn ($answer) => ! blank($answer))->count();
    $progressPercentage = $questionCount > 0 ? round(($answeredCount / $questionCount) * 100) : 0;
    $currentNumber = $questionCount > 0 ? $currentQuestionIndex + 1 : 0;
    $staticDuration = $duration ? sprintf('%02d:00', $duration) : '—';
    $hasCountdown = $remainingSeconds !== null;
    $questionType = $currentQuestion?->type instanceof QuestionType ? $currentQuestion?->type : QuestionType::tryFrom((string) $currentQuestion?->type);
    $isStatement = $questionType === QuestionType::Statement;
    $optionLabels = [__('أ'), __('ب'), __('ج'), __('د'), __('هـ'), __('و')];
@endphp

<div class="bg-white" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <section class="min-h-screen p-4 md:p-8 flex flex-col items-center justify-center">
        @if (! $assessment)
            <div class="w-full max-w-4xl bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                <p class="text-sm font-bold text-blue-950">{{ __('لم يتم العثور على') }} {{ $label }} {{ __('المطلوب.') }}</p>
                <a href="/{{ \App\Support\WebsiteUrl::locale() }}/my_lessons" class="inline-flex mt-4 text-sm font-bold" style="color: {{ $themeColor }}">{{ __('العودة لدروسي') }}</a>
            </div>
        @elseif ($existingAttempt)
            <div class="w-full max-w-4xl bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h1 class="text-2xl font-black text-blue-950">{{ __('تم تسليم') }} {{ $label }} {{ __('من قبل') }}</h1>
                <p class="text-sm text-gray-500 font-semibold mt-3">{{ __('يمكنك مراجعة النتيجة والإجابات من صفحة النتيجة.') }}</p>
                <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ $resultUrl }}" class="w-full sm:w-auto inline-flex justify-center text-white text-sm font-bold py-3 px-8 rounded-xl transition-all" style="background-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $themeHoverColor }}'" onmouseout="this.style.backgroundColor='{{ $themeColor }}'">
                        {{ __('عرض النتيجة') }}
                    </a>

                    @if ($canRetry)
                        <a href="{{ $retryUrl }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-bold py-3 px-8 rounded-xl bg-white border transition-all" style="color: {{ $themeColor }}; border-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $themeSoftColor }}'" onmouseout="this.style.backgroundColor='white'">
                            {{ __('إعادة') }} {{ $isExam ? __('الامتحان') : __('الواجب') }}
                        </a>
                    @endif
                </div>
            </div>
        @elseif (! $isOpen)
            <div class="w-full max-w-4xl bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4 text-xl">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 class="text-xl font-black text-blue-950">{{ $label }} {{ __('مغلق حالياً') }}</h2>
                <p class="text-sm text-gray-500 font-semibold mt-3">{{ __('سيظهر المحتوى هنا داخل فترة الإتاحة المحددة من لوحة التحكم.') }}</p>
            </div>
        @elseif ($questions->isEmpty() || ! $currentQuestion)
            <div class="w-full max-w-4xl bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                <p class="text-sm font-bold text-blue-950">{{ __('لا توجد أسئلة متاحة حالياً.') }}</p>
            </div>
        @else
            <form wire:submit="submit" class="w-full max-w-4xl bg-white rounded-[2rem] border border-gray-100 p-6 md:p-10 shadow-sm space-y-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-b border-gray-50 pb-6">
                    <div class="flex items-center gap-2" style="color: {{ $themeColor }}">
                        <span class="text-xs font-bold text-gray-400">{{ __('الوقت المتبقي') }}</span>
                        @if ($hasCountdown)
                            <span
                                class="text-2xl md:text-3xl font-black tracking-wider"
                                x-data="{
                                    remaining: @js($remainingSeconds),
                                    submitting: false,
                                    timer: null,
                                    format() {
                                        const minutes = Math.floor(this.remaining / 60).toString().padStart(2, '0');
                                        const seconds = (this.remaining % 60).toString().padStart(2, '0');

                                        return `${minutes}:${seconds}`;
                                    },
                                    finishIfExpired() {
                                        if (this.remaining > 0 || this.submitting) {
                                            return;
                                        }

                                        this.submitting = true;

                                        if (this.timer) {
                                            clearInterval(this.timer);
                                        }

                                        this.$wire.submit(true);
                                    },
                                }"
                                x-init="
                                    finishIfExpired();
                                    timer = setInterval(() => {
                                        if (remaining > 0) {
                                            remaining--;
                                        }

                                        finishIfExpired();
                                    }, 1000);
                                "
                                x-text="format()"
                            >{{ sprintf('%02d:%02d', intdiv($remainingSeconds, 60), $remainingSeconds % 60) }}</span>
                        @else
                            <span class="text-2xl md:text-3xl font-black tracking-wider">{{ $staticDuration }}</span>
                        @endif
                    </div>

                    <div class="w-full sm:w-1/2 space-y-1">
                        <div class="flex justify-between items-center text-xs font-bold text-gray-400">
                            <span class="text-emerald-500 font-black">{{ $progressPercentage }}%</span>
                            <span>{{ __('التقدم:') }} {{ $answeredCount }} / {{ $questionCount }} {{ __('سؤال') }}</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                @if ($isExam)
                    <div class="flex flex-col sm:flex-row justify-between items-center bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 gap-3 text-sm font-bold">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                            <p class="text-center sm:text-right">{{ __('تنبيه: في حال الخروج من الصفحة قبل التسليم قد تفقد إجاباتك الحالية.') }}</p>
                        </div>
                        <button type="submit" class="text-white px-5 py-2 rounded-xl text-xs font-black whitespace-nowrap transition-all hidden sm:block" style="background-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $themeHoverColor }}'" onmouseout="this.style.backgroundColor='{{ $themeColor }}'">
                            {{ __('إنهاء الاختبار') }}
                        </button>
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    <span class="text-white text-xs font-black px-4 py-1.5 rounded-full shadow-sm" style="background-color: {{ $themeColor }}; box-shadow: 0 4px 12px {{ $themeColor }}1A">
                        {{ __('السؤال') }} {{ $currentNumber }}
                    </span>
                    <button type="button" class="text-xs font-bold text-red-400 hover:text-red-500 transition-all flex items-center gap-1.5">
                        <i class="fa-regular fa-flag"></i>
                        <span>{{ __('تبليغ عن خطأ') }}</span>
                    </button>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 text-center shadow-sm">
                    <h1 wire:key="assessment-question-title-{{ $currentQuestion->id }}" data-question-math x-data x-init="$nextTick(() => renderStudentMath())" class="text-lg md:text-xl font-black text-blue-950 leading-relaxed max-w-2xl mx-auto">
                        {{ $currentQuestion->title }}
                    </h1>
                </div>

                @if ($isStatement)
                    <textarea
                        wire:model.live="answers.{{ $currentQuestion->id }}"
                        rows="6"
                        class="w-full rounded-2xl border border-gray-100 bg-gray-50 p-5 text-sm font-semibold text-blue-950 outline-none transition-colors"
                        style="--tw-ring-color: {{ $themeColor }}"
                        placeholder="{{ __('اكتب إجابتك هنا...') }}"
                    ></textarea>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="options-container">
                        @foreach ($currentQuestion->options as $optionIndex => $option)
                            @php
                                $isSelected = (string) ($answers[$currentQuestion->id] ?? '') === (string) $option->id;
                            @endphp
                            <label
                                wire:key="assessment-option-{{ $option->id }}"
                                class="option-card relative border-2 {{ $isSelected ? '' : 'border-gray-100 bg-white' }} rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all select-none"
                                style="{{ $isSelected ? 'border-color: '.$themeColor.'; background-color: '.$themeSoftColor : '' }}"
                            >
                                <input type="radio" wire:model.live="answers.{{ $currentQuestion->id }}" value="{{ $option->id }}" class="sr-only">
                                <div class="flex items-center gap-3">
                                    <span class="badge w-8 h-8 rounded-xl {{ $isSelected ? 'text-white' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-black text-xs transition-all" style="{{ $isSelected ? 'background-color: '.$themeColor : '' }}">
                                        {{ $optionLabels[$optionIndex] ?? $optionIndex + 1 }}
                                    </span>
                                    <span data-question-math x-data x-init="$nextTick(() => renderStudentMath())" class="text-sm font-black text-blue-950">{{ $option->title }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">{{ __('الخيار') }} {{ $optionIndex + 1 }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                @error("answers.{$currentQuestion->id}")
                    <p class="text-xs font-bold text-rose-600">{{ $message }}</p>
                @enderror

                @error('assessment')
                    <p class="text-xs font-bold text-rose-600">{{ $message }}</p>
                @enderror

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2 border-t border-gray-50">
                    <button
                        type="submit"
                        class="order-2 sm:order-1 w-full sm:w-auto text-white px-8 py-3 rounded-xl text-xs font-black transition-all"
                        style="background-color: {{ $themeColor }}"
                        onmouseover="this.style.backgroundColor='{{ $themeHoverColor }}'"
                        onmouseout="this.style.backgroundColor='{{ $themeColor }}'"
                    >
                        <span wire:loading.remove wire:target="submit">{{ __('إنهاء') }} {{ $isExam ? __('الاختبار') : __('الواجب') }}</span>
                        <span wire:loading wire:target="submit">{{ __('جاري التسليم...') }}</span>
                    </button>

                    <div class="order-1 sm:order-2 flex gap-3 w-full sm:w-auto">
                        <button
                            type="button"
                            wire:click="previousQuestion"
                            @disabled($currentQuestionIndex === 0)
                            class="flex-1 sm:flex-none px-6 py-3 rounded-xl text-xs font-black transition-all {{ $currentQuestionIndex === 0 ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-gray-50 hover:bg-gray-100 text-blue-950' }}"
                        >
                            {{ __('السابق') }}
                            <i class="fa-solid fa-arrow-right mr-1"></i>
                        </button>

                        <button
                            type="button"
                            wire:click="nextQuestion"
                            @disabled($currentQuestionIndex >= $questionCount - 1)
                            class="flex-1 sm:flex-none px-6 py-3 rounded-xl text-xs font-black transition-all {{ $currentQuestionIndex >= $questionCount - 1 ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-gray-50 hover:bg-gray-100 text-blue-950' }}"
                        >
                            <i class="fa-solid fa-arrow-left ml-1"></i>
                            {{ __('التالي') }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </section>
@once
    <link rel="stylesheet" href="{{ asset('css/katex.min.css') }}">
    <script defer src="{{ asset('js/katex.min.js') }}" onload="renderStudentMath()"></script>
    <script>
        function renderStudentMath() {
            if (typeof katex === 'undefined') {
                return;
            }

            document.querySelectorAll('[data-question-math]').forEach(function (element) {
                if (element.dataset.mathRendered === 'true') {
                    return;
                }

                const text = element.textContent;
                const expression = /\\\((.*?)\\\)|\\frac\{[^{}]+\}\{[^{}]+\}/g;
                const fragments = document.createDocumentFragment();
                let cursor = 0;

                for (const match of text.matchAll(expression)) {
                    fragments.append(document.createTextNode(text.slice(cursor, match.index)));

                    const math = document.createElement('span');
                    katex.render(match[1] ?? match[0], math, { throwOnError: false });
                    fragments.append(math);
                    cursor = match.index + match[0].length;
                }

                fragments.append(document.createTextNode(text.slice(cursor)));
                element.replaceChildren(fragments);
                element.dataset.mathRendered = 'true';
            });
        }
    </script>
@endonce
</div>
