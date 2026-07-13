<form id="profile-completion-form" method="POST" action="/register" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">الاسم الأول</label>
            <input name="first_name" value="{{ old('first_name') }}" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('first_name') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">اسم العائلة</label>
            <input name="last_name" value="{{ old('last_name') }}" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('last_name') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">تاريخ الميلاد</label>
            <input name="date_of_birth" value="{{ old('date_of_birth') }}" type="date" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
            @error('date_of_birth') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">النوع</label>
            <select name="gender" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">اختر النوع</option>
                @foreach ($genders as $value => $label)
                    <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('gender') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">البلد</label>
            <select name="country_id" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">اختر البلد</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" @selected((int) old('country_id') === $country->id)>{{ $country->name }}</option>
                @endforeach
            </select>
            @error('country_id') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">المدينة</label>
            <select name="city_id" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">اختر المدينة</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((int) old('city_id') === $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
            @error('city_id') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">المرحلة الدراسية</label>
            <select name="education_stage_id" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">اختر المرحلة</option>
                @foreach ($educationStages as $stage)
                    <option value="{{ $stage->id }}" @selected((int) old('education_stage_id') === $stage->id)>{{ $stage->name }}</option>
                @endforeach
            </select>
            @error('education_stage_id') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5 text-right">
            <label class="text-xs font-bold text-gray-400 mr-1">السنة الدراسية</label>
            <select name="grade_id" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none appearance-none cursor-pointer">
                <option value="">اختر السنة الدراسية</option>
                @foreach ($grades as $grade)
                    <option value="{{ $grade->id }}" @selected((int) old('grade_id') === $grade->id)>{{ $grade->name }}</option>
                @endforeach
            </select>
            @error('grade_id') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-1.5 text-right">
        <label class="text-xs font-bold text-gray-400 mr-1">اسم المدرسة</label>
        <input name="school_name" value="{{ old('school_name') }}" type="text" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black px-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none">
        @error('school_name') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5 text-right">
        <label class="text-xs font-bold text-gray-400 mr-1">البريد الإلكتروني</label>
        <div class="relative">
            <input name="email" value="{{ old('email') }}" type="email" placeholder="example@mail.com" class="w-full bg-[#F3F4F9] text-gray-700 text-xs font-black pl-11 pr-4 py-4 rounded-2xl border-2 border-transparent focus:outline-none text-left" dir="ltr">
            <div class="absolute inset-y-0 left-4 flex items-center text-gray-400 pointer-events-none"><i class="fa-regular fa-envelope"></i></div>
        </div>
        @error('email') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5 text-right">
        <span class="text-xs font-bold text-gray-400 mr-1">صورة شخصية</span>
        <label for="avatar-upload" class="w-full bg-[#F3F4F9] hover:bg-gray-100 rounded-2xl border-2 border-dashed border-gray-200 p-4 flex items-center justify-between cursor-pointer transition-colors">
            <span class="text-gray-400 text-xs font-bold"><i class="fa-regular fa-image text-sm"></i></span>
            <span id="file-status-text" class="text-gray-400 text-xs font-black">رفع صورة</span>
            <input id="avatar-upload" name="avatar" type="file" accept="image/*" onchange="handleFileChange(this)" class="hidden">
        </label>
        <div id="avatar-preview" class="hidden mt-3 rounded-2xl border border-gray-100 bg-white p-3">
            <img id="avatar-preview-image" src="" alt="Avatar preview" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-gray-50">
        </div>
        @error('avatar') <p class="text-[11px] font-bold text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="pt-3">
        <button type="submit" class="w-full text-white font-black text-sm py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2" style="background-color: {{ $themeColor }}">
            <span>إكمال التسجيل</span>
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </button>
    </div>
</form>
