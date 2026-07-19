<div>
@if (! $otpSent)
    <form id="phone-verification-form" wire:submit="sendOtp" class="space-y-6">
        <div class="grid grid-cols-[105px_1fr] gap-3">
            <div class="space-y-2 text-right">
                <label class="text-xs font-black text-blue-950 mr-1">الكود</label>
                <input type="text" wire:model="dialCountryCode" placeholder="+20"
                    class="w-full bg-[#F3F4F9] text-gray-700 text-sm font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none transition-all tracking-wider text-left"
                    dir="ltr">
                @error('dialCountryCode') <p class="text-[11px] font-bold text-red-500 mr-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2 text-right">
                <label class="text-xs font-black text-blue-950 mr-1">رقم الهاتف</label>
                <div class="relative">
                    <input type="tel" wire:model="phone" placeholder="010XXXXXXXX"
                        class="w-full bg-[#F3F4F9] text-gray-700 text-sm font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none transition-all tracking-wider placeholder:tracking-normal text-right placeholder:text-right"
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
                <span wire:loading.remove wire:target="sendOtp">إرسال كود التحقق</span>
                <span wire:loading wire:target="sendOtp">جاري الإرسال...</span>
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </button>
        </div>
    </form>
@else
    <form
        id="otp-verification-form"
        wire:submit="verify"
        class="space-y-6"
        x-data="{
            digits: ['', '', '', ''],
            submitted: false,
            handleInput(index, event) {
                const value = event.target.value.replace(/\D/g, '');

                if (value.length > 1) {
                    this.fillFrom(index, value);

                    return;
                }

                this.digits[index] = value.slice(-1);
                event.target.value = this.digits[index];

                if (this.digits[index] && index < 3) {
                    this.$refs[`otp${index + 1}`].focus();
                }

                this.submitIfComplete();
            },
            handleBackspace(index, event) {
                if (event.key !== 'Backspace' || this.digits[index] || index === 0) {
                    return;
                }

                this.$refs[`otp${index - 1}`].focus();
            },
            handlePaste(event) {
                const value = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4);

                if (! value) {
                    return;
                }

                event.preventDefault();
                this.fillFrom(0, value);
            },
            fillFrom(start, value) {
                value.slice(0, 4 - start).split('').forEach((digit, offset) => {
                    this.digits[start + offset] = digit;
                });

                this.$nextTick(() => {
                    const next = this.digits.findIndex((digit) => digit === '');
                    this.$refs[`otp${next === -1 ? 3 : next}`].focus();
                    this.submitIfComplete();
                });
            },
            submitIfComplete() {
                const otp = this.digits.join('');

                if (otp.length !== 4 || this.submitted) {
                    return;
                }

                this.submitted = true;
                $wire.otp = otp;
                $wire.verify();
            },
        }"
        x-on:submit="$wire.otp = digits.join('')"
    >
        <p class="text-[11px] font-bold text-gray-400 text-center">كود التطوير الحالي: {{ $developmentOtp }}</p>

        <div class="space-y-2">
            <label class="block text-xs font-black text-gray-400 text-center mb-4">كود التحقق</label>

            <div class="flex justify-center gap-3 md:gap-4" dir="ltr">
                <input type="text" inputmode="numeric" maxlength="1" x-ref="otp0" x-model="digits[0]" x-on:input="handleInput(0, $event)" x-on:keydown="handleBackspace(0, $event)" x-on:paste="handlePaste($event)" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" inputmode="numeric" maxlength="1" x-ref="otp1" x-model="digits[1]" x-on:input="handleInput(1, $event)" x-on:keydown="handleBackspace(1, $event)" x-on:paste="handlePaste($event)" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" inputmode="numeric" maxlength="1" x-ref="otp2" x-model="digits[2]" x-on:input="handleInput(2, $event)" x-on:keydown="handleBackspace(2, $event)" x-on:paste="handlePaste($event)" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
                <input type="text" inputmode="numeric" maxlength="1" x-ref="otp3" x-model="digits[3]" x-on:input="handleInput(3, $event)" x-on:keydown="handleBackspace(3, $event)" x-on:paste="handlePaste($event)" class="otp-field w-14 h-16 md:w-16 md:h-20 bg-white border-2 border-gray-200 rounded-2xl text-center text-xl font-black text-blue-950 focus:outline-none transition-all shadow-sm">
            </div>

            @error('otp') <p class="text-[11px] font-bold text-red-500 text-center mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            @error('phone') <p class="text-[11px] font-bold text-red-500 text-center mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2"
                style="background-color: {{ $themeColor }}">
                <i class="fa-regular fa-circle-check text-xs"></i>
                <span wire:loading.remove wire:target="verify">تأكيد الرمز</span>
                <span wire:loading wire:target="verify">جاري التحقق...</span>
            </button>
        </div>

        <button type="button" wire:click="resetChallenge" class="w-full text-center text-xs font-bold text-gray-400 hover:text-gray-600">
            تغيير رقم الهاتف
        </button>
    </form>
@endif
</div>
