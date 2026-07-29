<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Auth\CompleteStudentRegistration;
use App\Actions\StudentPortal\Auth\LoadRegistrationOptions;
use App\Actions\StudentPortal\Layout\LoadProviderTheme;
use App\Enums\Gender;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
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

    private CompleteStudentRegistration $completeStudentRegistration;

    private LoadRegistrationOptions $loadRegistrationOptions;

    private LoadProviderTheme $loadProviderTheme;

    public function boot(
        CompleteStudentRegistration $completeStudentRegistration,
        LoadRegistrationOptions $loadRegistrationOptions,
        LoadProviderTheme $loadProviderTheme,
    ): void {
        $this->completeStudentRegistration = $completeStudentRegistration;
        $this->loadRegistrationOptions = $loadRegistrationOptions;
        $this->loadProviderTheme = $loadProviderTheme;
    }

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

        $this->completeStudentRegistration->handle($user, $data, $this->avatar);

        return $this->redirect('/', navigate: false);
    }

    public function render(): mixed
    {
        $theme = $this->loadProviderTheme->handle($this->providerId);

        return view('livewire.website.register-form', [
            'provider' => $theme['provider'],
            'themeColor' => $theme['themeColor'],
            ...$this->loadRegistrationOptions->handle($this->countryId, $this->educationStageId),
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
}
