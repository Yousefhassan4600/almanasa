@php
    $styles = [
        'رياضيات' => ['icon' => 'fa-calculator', 'bg' => 'bg-[#F3F0FF]', 'text' => 'text-[#5D3FD3]'],
        'Mathematics' => ['icon' => 'fa-calculator', 'bg' => 'bg-[#F3F0FF]', 'text' => 'text-[#5D3FD3]'],
        'علوم' => ['icon' => 'fa-microscope', 'bg' => 'bg-[#E0F2FE]', 'text' => 'text-sky-600'],
        'Science' => ['icon' => 'fa-microscope', 'bg' => 'bg-[#E0F2FE]', 'text' => 'text-sky-600'],
        'فيزياء' => ['icon' => 'fa-atom', 'bg' => 'bg-[#DCFCE7]', 'text' => 'text-green-600'],
        'Physics' => ['icon' => 'fa-atom', 'bg' => 'bg-[#DCFCE7]', 'text' => 'text-green-600'],
        'كيمياء' => ['icon' => 'fa-flask-vial', 'bg' => 'bg-[#FCE7F3]', 'text' => 'text-pink-600'],
        'Chemistry' => ['icon' => 'fa-flask-vial', 'bg' => 'bg-[#FCE7F3]', 'text' => 'text-pink-600'],
        'إنجليزي' => ['label' => 'En', 'bg' => 'bg-[#FEF9C3]', 'text' => 'text-yellow-600'],
        'English' => ['label' => 'En', 'bg' => 'bg-[#FEF9C3]', 'text' => 'text-yellow-600'],
        'عربي' => ['icon' => 'fa-pen-nib', 'bg' => 'bg-[#FFEDD5]', 'text' => 'text-orange-600'],
        'Arabic' => ['icon' => 'fa-pen-nib', 'bg' => 'bg-[#FFEDD5]', 'text' => 'text-orange-600'],
        'أحياء' => ['icon' => 'fa-seedling', 'bg' => 'bg-[#E0F2FE]', 'text' => 'text-teal-600'],
        'Biology' => ['icon' => 'fa-seedling', 'bg' => 'bg-[#E0F2FE]', 'text' => 'text-teal-600'],
    ];

    $styleFor = function (string $name) use ($styles): array {
        foreach ($styles as $needle => $style) {
            if (str_contains($name, $needle)) {
                return $style;
            }
        }

        return ['icon' => 'fa-book-open', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
    };
@endphp

<section class="bg-white" dir="rtl">
    <div class="my-container max-w-7xl mx-auto px-4 md:px-8">
        @if ($subjects->isNotEmpty())
            <div
                class="flex overflow-x-auto no-scrollbar md:grid md:grid-cols-4 lg:grid-cols-8 gap-6 pb-4 text-center snap-x snap-mandatory"
            >
                @foreach ($subjects as $accountSubject)
                    @php
                        $subject = $accountSubject->gradeSubject?->subject;
                        $subjectName = $subject
                            ? ($subject->getTranslation('name', 'ar', false) ?: $subject->name)
                            : $accountSubject->name;
                        $style = $styleFor($subjectName);
                    @endphp

                    <a
                        href="/subjects"
                        class="group flex flex-col items-center shrink-0 w-24 sm:w-auto snap-start"
                    >
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 {{ $style['bg'] }} rounded-2xl flex items-center justify-center {{ $style['text'] }} text-2xl sm:text-3xl transition-all group-hover:scale-110"
                        >
                            @if (isset($style['label']))
                                <span class="text-xl sm:text-2xl font-bold">{{ $style['label'] }}</span>
                            @else
                                <i class="fa-solid {{ $subject?->icon ?: $style['icon'] }}"></i>
                            @endif
                        </div>
                        <span
                            class="mt-3 font-semibold text-blue-950 text-xs sm:text-base whitespace-nowrap"
                        >
                            {{ $subjectName }}
                        </span>
                    </a>
                @endforeach

                <a
                    href="/subjects"
                    class="group flex flex-col items-center shrink-0 w-24 sm:w-auto snap-start"
                >
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500 text-xl sm:text-2xl font-bold"
                    >
                        <span>•••</span>
                    </div>
                    <span
                        class="mt-3 font-semibold text-blue-950 text-xs sm:text-base whitespace-nowrap"
                    >
                        المزيد
                    </span>
                </a>
            </div>
        @else
            <div class="rounded-3xl bg-slate-50 border border-slate-100 p-6 text-center">
                <p class="text-sm font-bold text-blue-950">
                    لا توجد مواد متاحة{{ $hasGradeFilter ? ' لصفك الدراسي' : '' }} حالياً.
                </p>
            </div>
        @endif
    </div>
</section>
