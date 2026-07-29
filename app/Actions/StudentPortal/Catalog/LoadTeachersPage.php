<?php

namespace App\Actions\StudentPortal\Catalog;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Course;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class LoadTeachersPage
{
    /**
     * @return array{accountSubject: AccountSubject|null, teachers: Collection<int, AcademyTeacher|Account>, coursesByTeacher: SupportCollection<int, Collection<int, Course>>, selectedSubjectId: int|null, isStandaloneTeacher: bool}
     */
    public function handle(Provider $provider, ?int $gradeId, ?int $subjectId): array
    {
        $accountSubject = $this->selectedAccountSubject($provider, $gradeId, $subjectId);
        $teachers = $accountSubject ? $this->teachers($provider, $accountSubject) : new Collection;

        return [
            'accountSubject' => $accountSubject,
            'teachers' => $teachers,
            'coursesByTeacher' => $accountSubject ? $this->coursesByTeacher($provider, $accountSubject, $teachers) : collect(),
            'selectedSubjectId' => $accountSubject?->id,
            'isStandaloneTeacher' => $provider->type === ProviderType::StandaloneTeacher,
        ];
    }

    private function selectedAccountSubject(Provider $provider, ?int $gradeId, ?int $subjectId): ?AccountSubject
    {
        $query = AccountSubject::query()
            ->with([
                'gradeSubject:id,grade_id,track_id,subject_id',
                'gradeSubject.grade:id,education_stage_id,name',
                'gradeSubject.grade.educationStage:id,name',
                'gradeSubject.track:id,name',
                'gradeSubject.subject:id,name,description,icon',
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

        $selected = filled($subjectId)
            ? (clone $query)->whereKey($subjectId)->first()
            : null;

        return $selected ?: $query->first();
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
