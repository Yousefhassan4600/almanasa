@php
    $themeColor = $provider->websitePrimaryColor();
    $secondaryThemeColor = $provider->websiteSecondaryColor();
    $itemsCount = $items->count();
    $subtotal = (float) ($cart?->subtotal ?? 0);
    $total = (float) ($cart?->total ?? 0);
    $money = fn (float|int|string|null $amount): string => number_format((float) $amount, 0).' ج.م';
    $unitLabel = function ($unit): string {
        if (! $unit) {
            return '';
        }

        return $unit->getTranslation('name', 'ar', false)
            ?: $unit->getTranslation('name', 'en', false)
            ?: $unit->type?->value
            ?: '';
    };
    $teacherName = function ($course): string {
        if ($course?->academyTeacher?->teacher?->owner) {
            return $course->academyTeacher->teacher->owner->name;
        }

        return $course?->provider?->owner?->name ?: 'المعلم';
    };
@endphp

<div class="bg-white" dir="rtl">
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <nav class="flex items-center gap-2 text-xs font-bold text-gray-400 mb-6">
            <a href="/" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الرئيسية</a>
            <span>/</span>
            <span class="text-gray-600">السلة</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-gray-400"></i>
                        مدة الاشتراك
                    </h3>

                    @if ($purchaseUnits->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach ($purchaseUnits as $purchaseUnit)
                                @php $isSelected = (int) $selectedPurchaseUnitId === (int) $purchaseUnit->id; @endphp
                                <button
                                    type="button"
                                    wire:click="selectPurchaseUnit({{ $purchaseUnit->id }})"
                                    class="text-xs font-bold py-3.5 rounded-xl transition-all"
                                    style="{{ $isSelected ? 'background-color: '.$themeColor.'; color: white;' : 'background-color: #F3F4F9; color: #4b5563;' }}"
                                >
                                    {{ $unitLabel($purchaseUnit) }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 font-semibold">لا توجد مدد اشتراك مفعلة حالياً.</p>
                    @endif
                </div>

                <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm space-y-6">
                    <div class="flex justify-between items-center gap-3">
                        <h2 class="text-xl font-black" style="color: {{ $themeColor }}">الباقة المخصصة</h2>
                        <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg">
                            ({{ $itemsCount }} {{ $itemsCount === 1 ? 'مادة مختارة' : 'مواد مختارة' }})
                        </span>
                    </div>

                    @if ($items->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($items as $item)
                                @php
                                    $subject = $item->course?->accountSubject?->gradeSubject?->subject;
                                    $subjectName = $subject
                                        ? ($subject->getTranslation('name', 'ar', false) ?: $subject->name)
                                        : null;
                                @endphp

                                <div wire:key="cart-item-{{ $item->id }}" class="border border-gray-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all relative overflow-hidden">
                                    <div class="absolute right-0 top-0 bottom-0 w-1" style="background-color: {{ $themeColor }}"></div>

                                    <div class="flex items-center gap-4 text-right">
                                        <div class="w-14 h-14 rounded-xl border border-gray-100 shadow-sm flex items-center justify-center text-white font-black shrink-0" style="background-color: {{ $themeColor }}">
                                            <i class="fa-solid fa-book-open"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-blue-950">
                                                {{ $item->title }}
                                            </h4>
                                            <p class="text-[11px] text-gray-400 font-semibold mt-0.5">
                                                {{ $teacherName($item->course) }}
                                            </p>
                                            @if ($subjectName)
                                                <p class="text-[11px] text-gray-400 font-semibold mt-0.5">
                                                    {{ $subjectName }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <span class="text-sm font-bold text-gray-700 whitespace-nowrap">
                                            {{ $money($item->unit_price) }}
                                        </span>
                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $item->id }})"
                                            class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center gap-1 transition-colors"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-white text-gray-300 flex items-center justify-center mx-auto mb-4 text-2xl">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <h3 class="text-lg font-black text-blue-950">السلة فارغة</h3>
                            <p class="text-sm text-gray-400 font-semibold mt-2">اختر مادة أو كورس لإضافته إلى السلة.</p>
                            <a href="/subjects" class="inline-flex mt-5 text-white text-sm font-bold py-3 px-8 rounded-xl transition-all" style="background-color: {{ $themeColor }}">
                                تصفح المواد
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 shadow-sm space-y-6">
                    <h2 class="text-lg font-black text-blue-950 text-right">ملخص الطلب</h2>

                    <div class="space-y-3 text-xs font-bold text-gray-400">
                        <div class="flex justify-between">
                            <span>المجموع الفرعي ({{ $itemsCount }} {{ $itemsCount === 1 ? 'مادة' : 'مواد' }})</span>
                            <span>{{ $money($subtotal) }}</span>
                        </div>

                        <hr class="border-gray-100 my-2">

                        <div class="flex justify-between items-baseline pt-1">
                            <span>الإجمالي</span>
                            <span class="font-black text-2xl" style="color: {{ $themeColor }}">{{ $money($total) }}</span>
                        </div>
                    </div>

                    <a
                        href="/checkout"
                        class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 {{ $items->isEmpty() ? 'pointer-events-none opacity-50' : '' }}"
                        style="background-color: {{ $themeColor }}; box-shadow: 0 10px 20px {{ $themeColor }}33"
                        onmouseover="this.style.backgroundColor='{{ $secondaryThemeColor }}'"
                        onmouseout="this.style.backgroundColor='{{ $themeColor }}'"
                    >
                        إتمام عملية الدفع
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>

                    <div class="flex items-center justify-center gap-4 text-[10px] font-bold text-gray-400 pt-2 border-t border-gray-50">
                        <span class="flex items-center gap-1"><i class="fa-solid fa-rotate-left text-gray-300"></i> استرداد سهل</span>
                        <span class="text-gray-200">|</span>
                        <span class="flex items-center gap-1 text-emerald-600"><i class="fa-solid fa-shield-halved"></i> دفع آمن 100%</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center" style="color: {{ $themeColor }}">
                            <i class="fa-regular fa-comments"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-blue-950">هل تحتاج لمساعدة؟</h3>
                            <p class="text-[11px] text-gray-400 font-semibold">فريقنا متاح لمساعدتك في أي وقت</p>
                        </div>
                    </div>
                    <a href="#" class="text-xs font-bold px-4 py-2 rounded-xl border" style="color: {{ $themeColor }}; border-color: {{ $themeColor }}33">
                        تحدث معنا
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
