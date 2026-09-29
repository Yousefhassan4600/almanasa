@php
    use App\Enums\ProviderType;

    $themeColor = $provider->websitePrimaryColor();
    $secondaryColor = $provider->websiteSecondaryColor();
    $studentName = $student?->name ?: __('طالب');
    $gradeName = \App\Support\WebsiteTranslation::value($studentProfile?->grade, 'name');
    $avatar = $studentProfile?->avatar
        ? (filter_var($studentProfile->avatar, FILTER_VALIDATE_URL) ? $studentProfile->avatar : asset('storage/'.ltrim($studentProfile->avatar, '/')))
        : null;

    $statusFor = function ($subscription): array {
        if ($subscription->is_active) {
            return [
                'label' => __('نشط'),
                'class' => 'bg-emerald-50 text-emerald-600',
                'border' => '#059669',
                'description' => filled($subscription->ends_at)
                    ? __('ينتهي في ').$subscription->ends_at->format('Y-m-d')
                    : __('اشتراك مفتوح'),
            ];
        }

        if (filled($subscription->starts_at) && $subscription->starts_at->isFuture()) {
            return [
                'label' => __('لم يبدأ'),
                'class' => 'bg-amber-50 text-amber-600',
                'border' => '#F59E0B',
                'description' => __('يبدأ في ').$subscription->starts_at->format('Y-m-d'),
            ];
        }

        return [
            'label' => __('غير نشط'),
            'class' => 'bg-rose-50 text-rose-600',
            'border' => '#E11D48',
            'description' => filled($subscription->ends_at)
                ? __('انتهى في ').$subscription->ends_at->format('Y-m-d')
                : __('الاشتراك غير نشط حالياً'),
        ];
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 min-h-screen bg-white" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <aside class="col-span-1 lg:col-span-2 bg-white border-b lg:border-b-0 lg:border-l border-gray-100 p-6 flex flex-col justify-between order-1 lg:order-1">
        <div class="space-y-8">
                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="relative shrink-0">
                            @if ($avatar)
                                <img src="{{ $avatar }}" alt="{{ $studentName }}" class="w-16 h-16 lg:w-20 lg:h-20 rounded-full object-cover ring-4 ring-slate-50">
                            @else
                                <div role="img" aria-label="{{ $studentName }}" class="w-16 h-16 lg:w-20 lg:h-20 rounded-full ring-4 ring-slate-50 bg-slate-100 text-blue-950 flex items-center justify-center text-2xl font-black">
                                    {{ mb_substr($studentName, 0, 1) }}
                                </div>
                            @endif
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-blue-950">{{ __('أهلاً بك،') }} {{ $studentName }}</h2>
                            <p class="text-xs font-bold text-gray-400 mt-1">{{ $gradeName ? __('طالب في ').$gradeName : __('طالب') }}</p>
                        </div>
                    </div>

                    <nav class="space-y-1">
                        <a href="/{{ \App\Support\WebsiteUrl::locale() }}/profile" class="flex items-center gap-3 text-gray-400 hover:bg-gray-50 hover:text-blue-950 px-4 py-3 rounded-xl text-xs font-bold transition-all">
                            <i class="fa-regular fa-bookmark text-base w-5 text-center"></i>
                            <span>{{ __('لوحة التحكم') }}</span>
                        </a>
                        <a href="/{{ \App\Support\WebsiteUrl::locale() }}/my_lessons" class="flex items-center gap-3 text-white px-4 py-3 rounded-xl text-xs font-black shadow-sm transition-all" style="background-color: {{ $themeColor }}; box-shadow: 0 8px 20px {{ $themeColor }}26">
                            <i class="fa-solid fa-grip text-base w-5 text-center"></i>
                            <span>{{ __('دروسي') }}</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 text-gray-400 hover:bg-gray-50 hover:text-blue-950 px-4 py-3 rounded-xl text-xs font-bold transition-all">
                            <i class="fa-regular fa-file-lines text-base w-5 text-center"></i>
                            <span>{{ __('الاختبارات') }}</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 text-gray-400 hover:bg-gray-50 hover:text-blue-950 px-4 py-3 rounded-xl text-xs font-bold transition-all">
                            <i class="fa-regular fa-chart-bar text-base w-5 text-center"></i>
                            <span>{{ __('التقارير') }}</span>
                        </a>
                    </nav>
        </div>

        <div class="pt-6 border-t border-gray-50 mt-8 hidden lg:block text-center">
            <span class="text-[10px] font-bold text-gray-400">{{ $provider->name }}</span>
        </div>
    </aside>

    <main class="col-span-1 lg:col-span-10 p-4 md:p-8 order-2 lg:order-2 bg-white">
                <div class="my-container space-y-8">
                    <div class="text-right">
                        <h1 class="text-3xl md:text-4xl font-black text-blue-950">{{ __('دروسي') }}</h1>
                        <p class="text-sm md:text-base font-bold text-gray-400 mt-3">
                            {{ __('المواد والكورسات التي اشتركت بها، مع حالة كل اشتراك.') }}
                        </p>
                    </div>

                    <form wire:submit="redeemCode" class="rounded-2xl border border-gray-100 bg-slate-50 p-5 flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                        <div class="flex-1 w-full text-right">
                            <label for="provider-code" class="block text-sm font-black text-blue-950 mb-2">{{ __('تفعيل كود') }}</label>
                            <input id="provider-code" type="text" wire:model="code" dir="ltr" autocomplete="off" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm" placeholder="{{ __('أدخل الكود') }}">
                            @error('code') <p class="text-rose-600 text-xs mt-2">{{ $message }}</p> @enderror
                            @if ($codeRedeemed) <p class="text-emerald-600 text-xs mt-2">{{ __('تم تفعيل الكود وإضافة المحتوى إلى دروسك.') }}</p> @endif
                        </div>
                        <button type="submit" class="rounded-xl px-6 py-3 text-white text-sm font-bold" style="background-color: {{ $themeColor }}">{{ __('تفعيل') }}</button>
                    </form>

                    @if ($subscriptions->isEmpty())
                        <div class="rounded-[2rem] border border-dashed border-gray-200 bg-slate-50 p-10 text-center">
                            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center text-2xl" style="background-color: {{ $themeColor }}14; color: {{ $themeColor }}">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <h2 class="text-xl font-black text-blue-950">{{ __('لا توجد اشتراكات بعد') }}</h2>
                            <p class="text-sm font-bold text-gray-400 mt-2">{{ __('اشترك في مادة لتظهر هنا وتبدأ متابعة دروسك.') }}</p>
                            <a href="/{{ \App\Support\WebsiteUrl::locale() }}/subjects" class="inline-flex mt-5 text-white text-sm font-bold py-3 px-8 rounded-xl transition-all" style="background-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $themeColor }}'">
                                {{ __('استكشف المواد') }}
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-stretch">
                            @foreach ($subscriptions as $subscription)
                                @php
                                    $course = $subscription->course;
                                    $accountSubject = $course?->accountSubject;
                                    $gradeSubject = $accountSubject?->gradeSubject;
                                    $subject = $gradeSubject?->subject;
                                    $track = $gradeSubject?->track;
                                    $teacher = $course?->academyTeacher;
                                    $isStandaloneTeacher = $course?->provider?->type === ProviderType::StandaloneTeacher;
                                    $subjectName = \App\Support\WebsiteTranslation::value($subject, 'name') ?: $course?->title ?: __('مادة');
                                    $courseTitle = \App\Support\WebsiteTranslation::value($course, 'title') ?: $course?->title ?: $subjectName;
                                    $trackName = \App\Support\WebsiteTranslation::value($track, 'name') ?: $track?->name;
                                    $teacherName = $isStandaloneTeacher
                                        ? ($course?->provider?->name ?: $course?->provider?->owner?->name ?: $provider->owner?->name ?: __('المعلم'))
                                        : ($teacher?->teacher?->owner?->name ?: __('المعلم'));
                                    $teacherImagePath = $isStandaloneTeacher ? $course?->provider?->logo : $teacher?->image;
                                    $teacherImage = filled($teacherImagePath)
                                        ? (filter_var($teacherImagePath, FILTER_VALIDATE_URL) ? $teacherImagePath : asset('storage/'.ltrim($teacherImagePath, '/')))
                                        : null;
                                    $firstLesson = $subscription->lesson ?: $course?->lessons?->first(fn ($lesson) => $lesson->isCurrentlyOpen() && $lesson->items->contains(fn ($item) => $item->isCurrentlyOpen()));
                                    $firstItem = $firstLesson?->items?->first(fn ($item) => $item->isCurrentlyOpen());
                                    $continueUrl = $firstItem
                                        ? \App\Support\WebsiteUrl::path('/lesson?item='.$firstItem->id)
                                        : \App\Support\WebsiteUrl::path('/single_teacher?subject='.$accountSubject?->id.($course?->academy_teacher_id ? '&teacher='.$course->academy_teacher_id : ''));
                                    $courseUrl = \App\Support\WebsiteUrl::path('/single_teacher?subject='.$accountSubject?->id.($course?->academy_teacher_id ? '&teacher='.$course->academy_teacher_id : ''));
                                    $status = $statusFor($subscription);
                                    $subscriptionUnit = \App\Support\WebsiteTranslation::value($subscription->purchaseUnit, 'name') ?: $subscription->purchaseUnit?->name;
                                @endphp

                                <article class="bg-white border border-gray-100 rounded-[2rem] p-5 shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md" wire:key="subscription-course-{{ $subscription->id }}">
                                    <div class="absolute top-0 inset-x-0 h-1.5" style="background-color: {{ $status['border'] }}"></div>

                                    <div class="space-y-5">
                                        <div class="flex justify-between items-center">
                                            <span class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg" style="background-color: {{ $themeColor }}14; color: {{ $themeColor }}">
                                                <i class="fa-solid {{ $subject?->icon ?: 'fa-book-open' }}"></i>
                                            </span>
                                            <div class="flex items-center gap-2">
                                                @if ($trackName)
                                                    <span class="bg-slate-50 text-slate-500 text-[10px] font-black px-3 py-1 rounded-full">{{ $trackName }}</span>
                                                @endif
                                                <span class="{{ $status['class'] }} text-xs font-black px-3 py-1 rounded-full">{{ $status['label'] }}</span>
                                            </div>
                                        </div>

                                        <div class="text-right space-y-1">
                                            <h2 class="text-xl font-black text-blue-950">{{ $subjectName }}</h2>
                                            <p class="text-xs font-bold text-gray-400">{{ $courseTitle }}</p>
                                        </div>

                                        <div class="bg-[#F8F9FD] rounded-2xl p-3 flex items-center gap-3">
                                            @if ($teacherImage)
                                                <img src="{{ $teacherImage }}" alt="{{ $teacherName }}" class="w-10 h-10 rounded-xl object-cover">
                                            @else
                                                <div role="img" aria-label="{{ $teacherName }}" class="w-10 h-10 rounded-xl bg-slate-100 text-blue-950 flex items-center justify-center text-sm font-black">
                                                    {{ mb_substr($teacherName, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="text-right">
                                                <h3 class="text-xs font-black text-blue-950">{{ $teacherName }}</h3>
                                                <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ __('المعلم') }}</p>
                                            </div>
                                        </div>

                                        <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-3 text-right space-y-2">
                                            <div class="flex items-center justify-between gap-3 text-[11px] font-black">
                                                <span class="text-gray-400">{{ __('حالة الاشتراك') }}</span>
                                                <span style="color: {{ $status['border'] }}">{{ $status['description'] }}</span>
                                            </div>
                                            @if ($subscriptionUnit)
                                                <div class="flex items-center justify-between gap-3 text-[11px] font-black">
                                                    <span class="text-gray-400">{{ __('مدة الاشتراك') }}</span>
                                                    <span class="text-blue-950">{{ $subscriptionUnit }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="border border-dashed border-gray-200 rounded-2xl p-3.5 flex justify-between items-center bg-gray-50/50">
                                            <span class="text-sm" style="color: {{ $themeColor }}"><i class="fa-regular fa-circle-play"></i></span>
                                            <div class="text-right flex-1 pr-3">
                                                <p class="text-[10px] font-bold text-gray-400">{{ __('أول درس متاح') }}</p>
                                                <h4 class="text-xs font-black text-blue-950 mt-0.5">{{ \App\Support\WebsiteTranslation::value($firstLesson, 'title') ?: __('لم تتم إضافة دروس بعد') }}</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-5 grid grid-cols-1 gap-2">
                                        @if ($subscription->is_active)
                                            <a href="{{ $continueUrl }}" class="w-full text-white font-black text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-2" style="background-color: {{ $themeColor }}" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $themeColor }}'">
                                                <span>{{ __('استكمال التعلم') }}</span>
                                                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                            </a>
                                        @else
                                            <a href="/{{ \App\Support\WebsiteUrl::locale() }}/checkout?course={{ $course?->id }}" class="w-full text-white font-black text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-2" style="background-color: {{ $status['border'] }}">
                                                <span>{{ __('تجديد الاشتراك') }}</span>
                                                <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                            </a>
                                        @endif

                                        <a href="{{ $courseUrl }}" class="w-full bg-slate-50 hover:bg-slate-100 text-blue-950 font-black text-xs py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                                            <span>{{ __('عرض المادة') }}</span>
                                            <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
    </main>
</div>
