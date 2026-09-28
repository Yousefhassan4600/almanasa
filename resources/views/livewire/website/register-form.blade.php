<form id="profile-completion-form" wire:submit="save" class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('الاسم الأول') }}</label>
            <input wire:model="firstName" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('firstName') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('اسم العائلة') }}</label>
            <input wire:model="lastName" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('lastName') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('تاريخ الميلاد') }}</label>
            <input wire:model="dateOfBirth" type="date" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('dateOfBirth') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('النوع') }}</label>
            <select wire:model="gender" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">{{ __('اختر النوع') }}</option>
                @foreach ($genders as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('gender') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('البلد') }}</label>
            <select wire:model.live="countryId" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">{{ __('اختر البلد') }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ \App\Support\WebsiteTranslation::value($country, 'name') }}</option>
                @endforeach
            </select>
            @error('countryId') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('المدينة') }}</label>
            <select wire:model="cityId" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">{{ __('اختر المدينة') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ \App\Support\WebsiteTranslation::value($city, 'name') }}</option>
                @endforeach
            </select>
            @error('cityId') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('المرحلة الدراسية') }}</label>
            <select wire:model.live="educationStageId" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">{{ __('اختر المرحلة') }}</option>
                @foreach ($educationStages as $stage)
                    <option value="{{ $stage->id }}">{{ \App\Support\WebsiteTranslation::value($stage, 'name') }}</option>
                @endforeach
            </select>
            @error('educationStageId') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">{{ __('السنة الدراسية') }}</label>
            <select wire:model="gradeId" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">{{ __('اختر السنة الدراسية') }}</option>
                @foreach ($grades as $grade)
                    <option value="{{ $grade->id }}">{{ \App\Support\WebsiteTranslation::value($grade, 'name') }}</option>
                @endforeach
            </select>
            @error('gradeId') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-1.5 text-right">
        <label class="text-xs font-bold text-gray-400 mr-1">{{ __('اسم المدرسة') }}</label>
        <input wire:model="schoolName" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
        @error('schoolName') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5 text-right">
        <label class="text-xs font-bold text-gray-400 mr-1">{{ __('البريد الإلكتروني') }}</label>
        <div class="relative">
            <input wire:model="email" type="email" placeholder="example@mail.com" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black pl-11 pr-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none text-left" dir="ltr">
            <div class="absolute inset-y-0 left-4 flex items-center text-gray-400 pointer-events-none"><i class="fa-regular fa-envelope"></i></div>
        </div>
        @error('email') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5 text-right">
        <span class="text-xs font-bold text-gray-400 mr-1">{{ __('صورة شخصية') }}</span>
        <label for="avatar-upload" class="w-full bg-[#F3F4F9] hover:bg-gray-100 rounded-2xl border-2 border-dashed border-gray-200 p-4 flex items-center justify-between cursor-pointer transition-colors">
            <span class="text-gray-400 text-xs font-bold"><i class="fa-regular fa-image text-sm"></i></span>
            <span id="file-status-text" class="text-gray-400 text-xs font-black" wire:loading.remove wire:target="avatar">{{ __('رفع صورة') }}</span>
            <span class="text-gray-400 text-xs font-black" wire:loading wire:target="avatar">{{ __('جاري الرفع...') }}</span>
            <input id="avatar-upload" wire:model="avatar" type="file" accept="image/*" class="hidden">
        </label>
        @if ($avatar)
            <div id="avatar-preview" class="mt-3 rounded-2xl border border-gray-100 bg-white p-3">
                <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar preview" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-gray-50">
            </div>
        @endif
        @error('avatar') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="pt-3">
        <button type="submit" class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2" style="background-color: {{ $themeColor }}">
            <span wire:loading.remove wire:target="save">{{ __('إكمال التسجيل') }}</span>
            <span wire:loading wire:target="save">{{ __('جاري الحفظ...') }}</span>
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </button>
    </div>
</form>
