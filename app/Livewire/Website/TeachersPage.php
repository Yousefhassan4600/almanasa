<?php

namespace App\Livewire\Website;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Course;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class TeachersPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'subject')]
    public ?int $subjectId = null;

    public function mount(): void
    {
        $this->subjectId ??= request()->integer('subject') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $gradeId = Auth::user()?->studentProfile()->value('grade_id');
        $accountSubject = $this->selectedAccountSubject($provider, $gradeId);
        $teachers = $accountSubject ? $this->teachers($provider, $accountSubject) : new Collection;

        return view('livewire.website.teachers-page', [
            'provider' => $provider,
            'accountSubject' => $accountSubject,
            'teachers' => $teachers,
            'coursesByTeacher' => $accountSubject ? $this->coursesByTeacher($provider, $accountSubject, $teachers) : collect(),
            'isStandaloneTeacher' => $provider->type === ProviderType::StandaloneTeacher,
        ]);
    }

    private function selectedAccountSubject(Provider $provider, ?int $gradeId): ?AccountSubject
    {
        $query = AccountSubject::query()
            ->with([
                'gradeSubject:id,grade_id,subject_id',
                'gradeSubject.grade:id,education_stage_id,name',
                'gradeSubject.grade.educationStage:id,name',
                'gradeSubject.subject:id,track_id,name,description,icon',
                'gradeSubject.subject.track:id,name',
            ])
            ->whereBelongsTo($provider)
            ->where('is_active', true)
            ->when(
                $gradeId,
                fn ($query) => $query->whereHas(
                    'gradeSubject',
                    fn ($query) => $query->where('grade_id', $gradeId),
                ),
            )
            ->whereHas('gradeSubject.subject');

        $selected = filled($this->subjectId)
            ? (clone $query)->whereKey($this->subjectId)->first()
            : null;

        $selected ??= $query->first();
        $this->subjectId = $selected?->id;

        return $selected;
    }

    /**
     * @return Collection<int, AcademyTeacher|Account>
     */
    private function teachers(Provider $provider, AccountSubject $accountSubject): Collection
    {
        if ($provider->type === ProviderType::StandaloneTeacher) {
            return Account::query()
                ->with('owner:id,first_name,last_name')
                ->whereBelongsTo($provider)
                ->where('type', AccountType::StandaloneTeacher)
                ->where('is_active', true)
                ->get();
        }

        return AcademyTeacher::query()
            ->with(['teacher:id,owner_user_id,provider_id,type,is_active', 'teacher.owner:id,first_name,last_name'])
            ->whereBelongsTo($provider)
            ->where('is_active', true)
            ->whereHas(
                'gradeSubjectAssignments',
                fn ($query) => $query
                    ->where('account_subject_id', $accountSubject->id)
                    ->where('is_active', true),
            )
            ->get();
    }

    /**
     * @param  Collection<int, AcademyTeacher|Account>  $teachers
     * @return SupportCollection<int, Collection<int, Course>>
     */
    private function coursesByTeacher(Provider $provider, AccountSubject $accountSubject, Collection $teachers): SupportCollection
    {
        $courses = Course::query()
            ->with(['prices.purchaseUnit'])
            ->whereBelongsTo($provider)
            ->where('account_subject_id', $accountSubject->id)
            ->when(
                $provider->type === ProviderType::StandaloneTeacher,
                fn ($query) => $query->whereNull('academy_teacher_id'),
                fn ($query) => $query->whereIn('academy_teacher_id', $teachers->modelKeys()),
            )
            ->get();

        if ($provider->type === ProviderType::StandaloneTeacher) {
            return $courses->groupBy(fn () => $teachers->first()?->id);
        }

        return $courses->groupBy('academy_teacher_id');
    }
}
