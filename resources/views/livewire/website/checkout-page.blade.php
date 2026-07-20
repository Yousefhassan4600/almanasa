@php
    $themeColor = $provider->websitePrimaryColor();
    $secondaryThemeColor = $provider->websiteSecondaryColor();
    $subtotal = (float) ($cart?->subtotal ?? 0);
    $total = (float) ($cart?->total ?? 0);
    $money = fn (float|int|string|null $amount): string => number_format((float) $amount, 2).' ج.م';
    $methodName = function ($providerPaymentMethod): string {
        $paymentMethod = $providerPaymentMethod?->paymentMethod;

        if (! $paymentMethod) {
            return 'وسيلة دفع';
        }

        return $paymentMethod->getTranslation('name', 'ar', false)
            ?: $paymentMethod->getTranslation('name', 'en', false)
            ?: $paymentMethod->slug
            ?: 'وسيلة دفع';
    };
    $methodIcon = function ($providerPaymentMethod): string {
        $slug = $providerPaymentMethod?->paymentMethod?->slug;

        return match ($slug) {
            'bank-transfer' => 'fa-building-columns',
            'insta-pay' => 'fa-building-columns',
            'vodafone-cash', 'orange-cash', 'e&-cash' => 'fa-mobile-screen-button',
            'code' => 'fa-ellipsis',
            default => 'fa-credit-card',
        };
    };
    $paymentIdentifier = $selectedPaymentMethod?->paymentMethod?->is_bank || $selectedPaymentMethod?->paymentMethod?->is_code
        ? $selectedPaymentMethod?->account_number
        : $selectedPaymentMethod?->phone_number;
    $paymentHolder = $selectedPaymentMethod?->paymentMethod?->is_bank || $selectedPaymentMethod?->paymentMethod?->is_code
        ? $selectedPaymentMethod?->account_holder
        : $selectedPaymentMethod?->phone_holder;
@endphp

<div class="bg-white" dir="rtl">
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-8 font-sans">
        <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-8 font-medium">
            <a href="/" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">الرئيسية</a>
            <span>/</span>
            <a href="/cart" class="transition-colors" style="--hover-color: {{ $themeColor }}" onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')" onmouseout="this.style.color=''">السلة</a>
            <span>/</span>
            <span class="text-gray-600 font-bold">الدفع</span>
        </nav>

        @if ($submittedOrderNumber)
            <div class="max-w-2xl mx-auto bg-white border border-emerald-100 rounded-[2.5rem] p-8 md:p-12 shadow-sm text-center">
                <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-5 text-3xl">
                    <i class="fa-solid fa-check"></i>
                </div>
                <p class="text-xs font-black text-emerald-600 bg-emerald-50 rounded-full px-4 py-2 inline-flex mb-4">
                    {{ $submittedOrderNumber }}
                </p>
                <h1 class="text-2xl md:text-3xl font-black text-blue-950">تم إرسال الطلب</h1>
                <p class="text-sm font-bold text-gray-400 mt-3 leading-7">
                    طلبك الآن في انتظار موافقة الإدارة. سنقوم بتفعيل الاشتراك بعد مراجعة بيانات الدفع.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                    <a href="/my_lessons" class="text-white font-black text-sm py-3.5 px-8 rounded-2xl transition-all" style="background-color: {{ $themeColor }}">
                        دروسي
                    </a>
                    <a href="/" class="font-black text-sm py-3.5 px-8 rounded-2xl border transition-all" style="color: {{ $themeColor }}; border-color: {{ $themeColor }}55">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        @else
        <form wire:submit="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8 bg-white border border-gray-100 rounded-[2.5rem] p-6 md:p-10 shadow-sm space-y-8">
                <h1 class="text-2xl md:text-3xl font-black" style="color: {{ $themeColor }}">إتمام عملية الدفع</h1>

                @error('checkout')
                    <div class="rounded-2xl bg-red-50 border border-red-100 p-4 text-red-600 text-sm font-bold">
                        {{ $message }}
                    </div>
                @enderror

                <div class="space-y-4">
                    <h3 class="text-sm font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-regular fa-credit-card text-gray-400"></i>
                        اختر وسيلة الدفع
                    </h3>

                    @if ($paymentMethods->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($paymentMethods as $providerPaymentMethod)
                                @php $isSelected = (int) $selectedPaymentMethod?->id === (int) $providerPaymentMethod->id; @endphp
                                <button
                                    type="button"
                                    wire:click="selectPaymentMethod({{ $providerPaymentMethod->id }})"
                                    class="p-4 border border-transparent rounded-xl flex flex-col items-center justify-center gap-2 cursor-pointer transition-all text-xs font-bold"
                                    style="{{ $isSelected ? 'background-color: '.$themeColor.'; color: white; box-shadow: 0 12px 22px '.$themeColor.'33;' : 'background-color: #F3F4F9; color: #4b5563;' }}"
                                >
                                    <i class="fa-solid {{ $methodIcon($providerPaymentMethod) }} text-lg"></i>
                                    <span>{{ $methodName($providerPaymentMethod) }}</span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl bg-amber-50 border border-amber-100 p-5 text-amber-700 text-sm font-bold">
                            لا توجد وسائل دفع مفعلة لهذا المزود حالياً.
                        </div>
                    @endif
                </div>

                @if ($selectedPaymentMethod)
                    <div class="space-y-4 pt-2">
                        <h3 class="text-sm font-black text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-money-bill-transfer text-gray-400"></i>
                            بيانات التحويل
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5 text-right">
                                <label class="text-xs font-bold text-gray-400 block">
                                    {{ $selectedPaymentMethod->paymentMethod?->is_code ? 'الكود' : ($selectedPaymentMethod->paymentMethod?->is_bank ? 'رقم الحساب' : 'رقم الموبايل') }}
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        readonly
                                        value="{{ $paymentIdentifier ?: 'غير محدد' }}"
                                        class="w-full bg-[#F3F4F9] text-gray-800 text-sm font-black px-4 py-4 rounded-xl text-left tracking-wider"
                                        dir="ltr"
                                    >
                                    @if ($paymentIdentifier)
                                        <button
                                            type="button"
                                            onclick="navigator.clipboard.writeText(@js($paymentIdentifier))"
                                            class="absolute inset-y-0 right-0 px-4 text-gray-400 transition-colors"
                                            style="--hover-color: {{ $themeColor }}"
                                            onmouseover="this.style.color=this.style.getPropertyValue('--hover-color')"
                                            onmouseout="this.style.color=''"
                                            title="نسخ"
                                        >
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-1.5 text-right">
                                <label class="text-xs font-bold text-gray-400 block">اسم المستلم</label>
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $paymentHolder ?: $provider->name }}"
                                    class="w-full bg-[#F3F4F9] text-gray-800 text-sm font-black px-4 py-4 rounded-xl"
                                >
                            </div>
                        </div>

                        @if ($selectedPaymentMethod->paymentMethod?->require_proof)
                            <div class="space-y-1.5 text-right">
                                <label class="text-xs font-bold text-gray-400 block">صورة التحويل</label>
                                <label class="bg-[#F3F4F9] rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-dashed border-gray-300 transition-all cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-gray-400">
                                            <i class="fa-regular fa-image text-lg"></i>
                                        </span>
                                        <div>
                                            <span class="text-xs font-black text-gray-600 block">رفع صورة التحويل</span>
                                            <span class="text-[11px] font-bold text-gray-400 block mt-1">PNG / JPG حتى 2MB</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-white rounded-xl px-4 py-2" style="background-color: {{ $themeColor }}">
                                        اختر صورة
                                    </span>
                                    <input type="file" wire:model="transferImage" accept="image/*" class="hidden">
                                </label>

                                <div wire:loading wire:target="transferImage" class="text-xs font-bold text-gray-400 mt-2">
                                    جاري رفع الصورة...
                                </div>

                                @if ($transferImage)
                                    <div class="bg-[#F8F9FD] border border-gray-200 rounded-xl p-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img src="{{ $transferImage->temporaryUrl() }}" alt="صورة التحويل" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                                            <div class="text-right min-w-0">
                                                <p class="text-xs font-bold text-blue-950 truncate max-w-[220px]">{{ $transferImage->getClientOriginalName() }}</p>
                                                <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-1 mt-0.5">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    الصورة جاهزة للإرسال
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('transferImage', null)" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-lg transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                @endif

                                @error('transferImage')
                                    <p class="text-xs font-bold text-red-500 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="space-y-1.5 text-right">
                            <label class="text-xs font-bold text-gray-400 block">رقم العملية / ملاحظة الدفع</label>
                            <input
                                type="text"
                                wire:model="transactionReference"
                                placeholder="اختياري"
                                class="w-full bg-[#F3F4F9] text-gray-800 text-sm font-bold px-4 py-4 rounded-xl border border-transparent focus:outline-none transition-all"
                            >
                            @error('transactionReference')
                                <p class="text-xs font-bold text-red-500 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <button
                    type="submit"
                    class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 {{ $paymentMethods->isEmpty() || $items->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                    style="background-color: {{ $themeColor }}; box-shadow: 0 10px 20px {{ $themeColor }}33"
                    onmouseover="this.style.backgroundColor='{{ $paymentMethods->isEmpty() || $items->isEmpty() ? $themeColor : $secondaryThemeColor }}'"
                    onmouseout="this.style.backgroundColor='{{ $themeColor }}'"
                    @disabled($paymentMethods->isEmpty() || $items->isEmpty())
                >
                    <span wire:loading.remove wire:target="submitOrder">
                        <i class="fa-solid fa-lock text-xs"></i>
                        تأكيد عملية الدفع
                    </span>
                    <span wire:loading wire:target="submitOrder">جاري إرسال الطلب...</span>
                </button>
            </div>

            <aside class="lg:col-span-4 space-y-4">
                <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm">
                    <h2 class="text-lg font-black text-blue-950 mb-5">ملخص الطلب</h2>

                    @if ($items->isNotEmpty())
                        <div class="space-y-4 mb-6">
                            @foreach ($items as $item)
                                <div class="flex justify-between gap-3 text-sm">
                                    <div>
                                        <h3 class="font-black text-blue-950">{{ $item->title }}</h3>
                                        <p class="text-xs font-bold text-gray-400 mt-1">{{ $item->purchaseUnit?->getTranslation('name', 'ar', false) ?: $item->purchaseUnit?->type?->value }}</p>
                                    </div>
                                    <span class="font-black whitespace-nowrap" style="color: {{ $themeColor }}">{{ $money($item->unit_price) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 font-bold mb-6">السلة فارغة.</p>
                    @endif

                    <div class="bg-[#F3F4F9] rounded-2xl p-5 space-y-3 text-xs font-bold text-gray-500">
                        <div class="flex justify-between">
                            <span>سعر الاشتراك</span>
                            <span>{{ $money($subtotal) }}</span>
                        </div>
                        <div class="border-t border-white pt-3 flex justify-between items-baseline">
                            <span class="text-blue-950">الإجمالي</span>
                            <span class="text-xl font-black" style="color: {{ $themeColor }}">{{ $money($total) }}</span>
                        </div>
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
            </aside>
        </form>
        @endif
    </section>
</div>
