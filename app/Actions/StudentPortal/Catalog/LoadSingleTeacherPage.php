<?php

namespace App\Actions\StudentPortal\Catalog;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Enums\PurchaseUnitType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Course;
use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;

class LoadSingleTeacherPage
{
    /**
     * @return array{teacher: AcademyTeacher|Account|null, accountSubject: AccountSubject|null, course: Course|null, hasCourseSubscription: bool, monthlyPrice: string|null, selectedSubjectId: int|null}
     */
    public function handle(Provider $provider, ?int $teacherId, ?int $subjectId, ?int $studentUserId, ?int $gradeId): array
    {
        $teacher = $this->teacher($provider, $teacherId);
        $accountSubject = $this->accountSubject($provider, $subjectId, $gradeId);
        $course = $teacher && $accountSubject
            ? $this->course($provider, $teacher, $accountSubject)
            : null;

        return [
            'teacher' => $teacher,
            'accountSubject' => $accountSubject,
            'course' => $course,
            'hasCourseSubscription' => $course ? $this->hasActiveCourseSubscription($course, $studentUserId) : false,
            'monthlyPrice' => $course ? $this->monthlyPrice($course) : null,
            'selectedSubjectId' => $accountSubject?->id,
        ];
    }

    private function teacher(Provider $provider, ?int $teacherId): AcademyTeacher|Account|null
    {
        if ($provider->type === ProviderType::StandaloneTeacher) {
            return Account::query()
                ->with('owner:id,first_name,last_name')
                ->whereBelongsTo($provider)
                ->where('type', AccountType::StandaloneTeacher)
                ->where('is_active', true)
                ->first();
        }

        return AcademyTeacher::query()
            ->with(['teacher:id,owner_user_id,provider_id,type,is_active', 'teacher.owner:id,first_name,last_name'])
            ->whereBelongsTo($provider)
            ->where('is_active', true)
            ->when($teacherId, fn (Builder $query): Builder => $query->whereKey($teacherId))
            ->first();
    }

    private function accountSubject(Provider $provider, ?int $subjectId, ?int $gradeId): ?AccountSubject
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
                fn (Builder $query): Builder => $query->whereHas(
                    'gradeSubject',
                    fn (Builder $query): Builder => $query->where('grade_id', $gradeId),
                ),
            )
            ->when(
                $provider->type === ProviderType::StandaloneTeacher,
                fn (Builder $query): Builder => $query->whereHas(
                    'courses',
                    fn (Builder $query): Builder => $query
                        ->whereBelongsTo($provider)
                        ->whereNull('academy_teacher_id'),
                ),
            );

        $selected = filled($subjectId)
            ? (clone $query)->whereKey($subjectId)->first()
            : null;

        return $selected ?: $query->first();
    }

    private function course(Provider $provider, AcademyTeacher|Account $teacher, AccountSubject $accountSubject): ?Course
    {
        return Course::query()
            ->with([
                'academyTeacher.teacher.owner:id,first_name,last_name',
                'provider.owner:id,first_name,last_name',
                'accountSubject.gradeSubject.grade.educationStage',
                'accountSubject.gradeSubject.track',
                'accountSubject.gradeSubject.subject',
                'lessons' => fn ($query) => $query
                    ->with([
                        'coursePeriod:id,type,name,sort_order',
                        'items' => fn ($query) => $query
                            ->with('exam:id,course_id,title')
                            ->oldest('sort_order')
                            ->oldest('id'),
                    ])
                    ->where('is_active', true)
                    ->oldest('sort_order')
                    ->oldest('id'),
                'outcomes' => fn ($query) => $query->oldest('sort_order')->oldest('id'),
                'prices.purchaseUnit',
            ])
            ->whereBelongsTo($provider)
            ->whereBelongsTo($accountSubject)
            ->when(
                $provider->type === ProviderType::StandaloneTeacher,
                fn (Builder $query): Builder => $query->whereNull('academy_teacher_id'),
                fn (Builder $query): Builder => $query->whereBelongsTo($teacher, 'academyTeacher'),
            )
            ->first();
    }

    private function monthlyPrice(Course $course): ?string
    {
        $monthlyPrices = $course->prices
            ->filter(fn ($price) => $price->purchaseUnit?->type === PurchaseUnitType::Month)
            ->map(fn ($price) => $price->offer_price ?? $price->price)
            ->filter();

        $price = $monthlyPrices->isNotEmpty()
            ? $monthlyPrices->min()
            : $course->prices->map(fn ($price) => $price->offer_price ?? $price->price)->filter()->min();

        return $price ? number_format((float) $price) : null;
    }

    private function hasActiveCourseSubscription(Course $course, ?int $studentUserId): bool
    {
        if (! $studentUserId) {
            return false;
        }

        return Subscription::query()
            ->activeForStudentCourse($studentUserId, $course)
            ->exists();
    }
}
