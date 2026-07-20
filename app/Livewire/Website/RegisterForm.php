<?php

namespace App\Livewire\Website;

use App\Enums\Gender;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\Provider;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegisterForm extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $providerId;

    public string $firstName = '';

    public ?string $lastName = null;

    public ?string $email = null;

    public ?string $dateOfBirth = null;

    public ?string $gender = null;

    public ?int $countryId = null;

    public ?int $cityId = null;

    public ?int $educationStageId = null;

    public ?int $gradeId = null;

    public string $schoolName = '';

    public $avatar = null;

    public function mount(int $providerId)
    {
        $this->providerId = $providerId;

        if (! Auth::check()) {
            return $this->redirect('/login', navigate: false);
        }

        if (Auth::user()?->studentProfile()->exists()) {
            return $this->redirect('/', navigate: false);
        }
    }

    public function save(): mixed
    {
        $data = $this->validate();
        $user = Auth::user();

        abort_unless($user, 403);

        DB::transaction(function () use ($data, $user): void {
            $avatarPath = $this->avatar
                ? $this->avatar->store('students/avatars', 'public')
                : null;

            $user->forceFill([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'date_of_birth' => $data['dateOfBirth'],
            ])->save();

            StudentProfile::query()->create([
                'user_id' => $user->id,
                'email' => $data['email'],
                'avatar' => $avatarPath,
                'gender' => $data['gender'],
                'country_id' => $data['countryId'],
                'city_id' => $data['cityId'],
                'education_stage_id' => $data['educationStageId'],
                'grade_id' => $data['gradeId'],
                'school_name' => $data['schoolName'],
            ]);
        });

        return $this->redirect('/', navigate: false);
    }

    public function render(): mixed
    {
        return view('livewire.website.register-form', [
            'provider' => Provider::query()->findOrFail($this->providerId),
            'themeColor' => $this->themeColor(),
            'countries' => Country::query()->orderBy('name')->get(),
            'cities' => City::query()
                ->when($this->countryId, fn ($query) => $query->where('country_id', $this->countryId))
                ->orderBy('name')
                ->get(),
            'educationStages' => EducationStage::query()->orderBy('sort_order')->get(),
            'grades' => Grade::query()
                ->when($this->educationStageId, fn ($query) => $query->where('education_stage_id', $this->educationStageId))
                ->orderBy('sort_order')
                ->get(),
            'genders' => Gender::options(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique(StudentProfile::class, 'email')],
            'dateOfBirth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(array_keys(Gender::options()))],
            'countryId' => ['required', Rule::exists(Country::class, 'id')],
            'cityId' => [
                'required',
                Rule::exists(City::class, 'id')->where(fn ($query) => $query->where('country_id', $this->countryId)),
            ],
            'educationStageId' => ['required', Rule::exists(EducationStage::class, 'id')],
            'gradeId' => [
                'required',
                Rule::exists(Grade::class, 'id')->where(fn ($query) => $query->where('education_stage_id', $this->educationStageId)),
            ],
            'schoolName' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function themeColor(): string
    {
        return Provider::query()->findOrFail($this->providerId)->websitePrimaryColor();
    }
}
