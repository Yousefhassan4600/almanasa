@if (! $otpSent)
    <form id="phone-verification-form" method="POST" action="/login/send-otp" class="space-y-6">
        @csrf
        <div class="grid grid-cols-[105px_1fr] gap-3">
            <div class="space-y-2 text-right">
                <label class="text-xs font-black text-blue-950 mr-1">الكود</label>
                <input type="text" name="dial_country_code" value="{{ old('dial_country_code', '+20') }}" placeholder="+20"
                    class="w-full bg-[#F3F4F9] text-gray-700 text-sm font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none transition-all tracking-wider text-left"
                    dir="ltr">
                @error('dial_country_code') <p class="text-[11px] font-bold text-red-500 mr-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2 text-right">
                <label class="text-xs font-black text-blue-950 mr-1">رقم الهاتف</label>
                <div class="relative">
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="010XXXXXXXX"
                        class="w-full bg-[#F3F4F9] text-gray-700 text-sm font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none transition-all tracking-wider placeholder:tracking-normal text-left placeholder:text-right"
                        dir="ltr">
                    <div class="absolute inset-y-0 left-4 flex items-center text-gray-400 pointer-events-none">
                        <i class="fa-solid fa-phone text-sm"></i>
                    </div>
                </div>
                @error('phone') <p class="text-[11px] font-bold text-red-500 mr-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2"
                style="background-color: {{ $themeColor }}">
                <span>إرسال كود التحقق</span>
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </button>
        </div>
    </form>
@else
    <form id="otp-verification-form" method="POST" action="/otp/verify" class="space-y-6">
        @csrf
        <p class="text-[11px] font-bold text-gray-400 text-center">كود التطوير الحالي: {{ $developmentOtp }}</p>

        <div class="space-y-2">
            <label class="block text-xs font-black text-gray-400 text-center mb-4">كود التحقق</label>

            <div class="flex justify-center gap-3 md:gap-4" dir="ltr">
                <input type="text" maxlength="1" name="otp1" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" maxlength="1" name="otp2" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" maxlength="1" name="otp3" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" maxlength="1" name="otp4" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
            </div>

            @error('otp') <p class="text-[11px] font-bold text-red-500 text-center mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            @error('phone') <p class="text-[11px] font-bold text-red-500 text-center mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2"
                style="background-color: {{ $themeColor }}">
                <i class="fa-regular fa-circle-check text-xs"></i>
                <span>تأكيد الرمز</span>
            </button>
        </div>
    </form>
@endif
