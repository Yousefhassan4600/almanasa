<?php

namespace App\Livewire\Website;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Enums\PurchaseUnitType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Course;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class SingleTeacherPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'teacher')]
    public ?int $teacherId = null;

    #[Url(as: 'subject')]
    public ?int $subjectId = null;

    public function mount(): void
    {
        $this->teacherId ??= request()->integer('teacher') ?: null;
        $this->subjectId ??= request()->integer('subject') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $teacher = $this->teacher($provider);
        $accountSubject = $this->accountSubject($provider);
        $course = $teacher && $accountSubject
            ? $this->course($provider, $teacher, $accountSubject)
            : null;

        return view('livewire.website.single-teacher-page', [
            'provider' => $provider,
            'teacher' => $teacher,
            'accountSubject' => $accountSubject,
            'course' => $course,
            'monthlyPrice' => $course ? $this->monthlyPrice($course) : null,
        ]);
    }

    private function teacher(Provider $provider): AcademyTeacher|Account|null
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
            ->when($this->teacherId, fn (Builder $query): Builder => $query->whereKey($this->teacherId))
            ->first();
    }

    private function accountSubject(Provider $provider): ?AccountSubject
    {
        $gradeId = Auth::user()?->studentProfile()->value('grade_id');

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

        $selected = filled($this->subjectId)
            ? (clone $query)->whereKey($this->subjectId)->first()
            : null;

        $selected ??= $query->first();
        $this->subjectId = $selected?->id;

        return $selected;
    }

    private function course(Provider $provider, AcademyTeacher|Account $teacher, AccountSubject $accountSubject): ?Course
    {
        return Course::query()
            ->with([
                'academyTeacher.teacher.owner:id,first_name,last_name',
                'provider.owner:id,first_name,last_name',
                'accountSubject.gradeSubject.grade.educationStage',
                'accountSubject.gradeSubject.subject.track',
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
}
