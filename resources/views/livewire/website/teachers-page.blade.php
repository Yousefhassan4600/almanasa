@php
    $subject = $accountSubject?->gradeSubject?->subject;
    $grade = $accountSubject?->gradeSubject?->grade;
    $stage = $grade?->educationStage;
    $subjectName = $subject ? ($subject->getTranslation('name', 'ar', false) ?: $subject->name) : 'المادة';
    $subjectDescription = $subject ? ($subject->getTranslation('description', 'ar', false) ?: $subject->description) : null;
    $trackName = $subject?->track ? ($subject->track->getTranslation('name', 'ar', false) ?: $subject->track->name) : null;
@endphp

<div>
    <section
        class="bg-gradient-to-br from-purple-50/60 to-indigo-50/40 py-12 md:py-20 overflow-hidden"
        dir="rtl"
    >
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-right order-2 lg:order-1">
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2.5">
                        @if ($stage)
                            <span
                                class="bg-[#5D3FD3] text-white text-xs font-bold px-4 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm shadow-[#5D3FD3]/10"
                            >
                                <i class="fa-solid fa-graduation-cap text-[10px]"></i>
                                {{ $stage->name }}
                            </span>
                        @endif

                        @if ($trackName)
                            <span
                                class="bg-[#5D3FD3]/10 text-[#5D3FD3] text-xs font-bold px-4 py-1.5 rounded-full flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-book-open text-[10px]"></i>
                                {{ $trackName }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-blue-950 tracking-tight">
                            {{ $subjectName }}
                        </h1>
                        {{-- @if ($grade)
                            <p class="text-sm sm:text-base font-bold text-[#5D3FD3] opacity-90">
                                المعلمون المتاحون في {{ $grade->name }}
                            </p>
                        @endif --}}
                    </div>

                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        {{ $subjectDescription ?: 'اختر المعلم المناسب لهذه المادة من المعلمين المتاحين داخل الأكاديمية.' }}
                    </p>
                </div>

                <div class="lg:col-span-5 flex justify-center order-1 lg:order-2">
                    <div class="w-full max-w-[320px] sm:max-w-[400px] lg:max-w-full aspect-square relative flex items-center justify-center">
                        <img
                            src="/academy/assets/images/book.png"
                            alt="{{ $subjectName }}"
                            class="w-full h-auto object-contain drop-shadow-xl transform hover:scale-102 transition-transform duration-500"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-blue-950 text-center mb-12">
                اختر المعلم المناسب لك
            </h2>

            @if ($teachers->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($teachers as $teacher)
                        @php
                            $teacherCourses = $coursesByTeacher->get($teacher->teacher_account_id, collect());
                            $monthlyPrice = $teacherCourses->pluck('monthly_price')->filter()->min();
                            $weeklyLectures = $teacherCourses->pluck('weekly_lectures_count')->filter()->max();
                            $teacherName = $teacher->teacher?->owner?->name ?: 'معلم';
                            $teacherImage = $teacher->image
                                ? asset('storage/'.$teacher->image)
                                : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200';
                        @endphp

                        <div
                            wire:key="teacher-card-{{ $teacher->id }}"
                            class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between relative group"
                        >
                            <div class="flex flex-col items-center text-center mt-2">
                                <div class="w-24 h-24 rounded-full p-1 border border-gray-100 mb-4 overflow-hidden bg-slate-50">
                                    <img
                                        src="{{ $teacherImage }}"
                                        alt="{{ $teacherName }}"
                                        class="w-full h-full object-cover rounded-full"
                                    />
                                </div>
                                <h3 class="font-extrabold text-blue-950 text-base mb-1">{{ $teacherName }}</h3>
                                <p class="text-xs text-gray-400 mb-2">
                                    خبرة {{ $teacher->experience_years }} سنوات
                                </p>
                                {{-- <div class="flex items-center gap-1 text-xs text-amber-500 font-bold mb-4">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    <span>4.8</span>
                                </div> --}}
                            </div>

                            <div class="grid grid-cols-2 gap-2 border-t border-b border-gray-50 py-4 mb-5 text-center">
                                <div class="border-l border-gray-100">
                                    <span class="block text-sm font-bold text-[#5D3FD3]">
                                        {{ $monthlyPrice ? number_format((float) $monthlyPrice).' EGP' : '—' }}
                                    </span>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">سعر الاشتراك الشهري</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-blue-950">{{ $weeklyLectures ?: '—' }}</span>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">محاضرة أسبوعياً</span>
                                </div>
                            </div>

                            <a
                                href="/single_teacher?teacher={{ $teacher->id }}&subject={{ $accountSubject?->id }}"
                                class="w-full border border-purple-200 text-[#5D3FD3] hover:bg-purple-50 font-bold text-xs py-3 rounded-xl transition-colors bg-transparent text-center"
                            >
                                عرض التفاصيل
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                    <p class="text-sm font-bold text-blue-950">
                        لا يوجد معلمون متاحون لهذه المادة حالياً.
                    </p>
                    <a href="/subjects" class="inline-flex mt-4 text-[#5D3FD3] text-sm font-bold">
                        العودة لاختيار مادة أخرى
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
