<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\AccountSubject;
use App\Models\City;
use App\Models\Country;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Collection;

class ProvidersAndAccountsSeeder extends BaseSeeder
{
    public function run(): void
    {
        $egypt = Country::query()->where('code', 'EG')->firstOrFail();
        $cairo = City::query()
            ->where('country_id', $egypt->id)
            ->where('name->en', 'Cairo')
            ->firstOrFail();

        $secondaryTwoPhysics = $this->gradeSubject('Secondary Stage', 11, 'Physics', 'secondary_old_scientific');

        $saasOwner = $this->user('01000000000', 'Almanasa', 'Owner');
        $academyOwner = $this->user('01000000001', 'Academy', 'Owner');
        $academyTeacherUser = $this->user('01000000002', 'Ahmed', 'Teacher');
        $standaloneTeacherUser = $this->user('01203726375', 'محمد', 'خالد');
        $scienceTeacherUser = $this->user('01000000008', 'Youssef', 'Teacher');
        $studentUser = $this->user('01000000004', 'Omar', 'Student');
        $parentUser = $this->user('01000000005', 'Sara', 'Parent');

        $this->account(
            type: AccountType::SaasOwner,
            owner: $saasOwner,
        );

        $academyMonthlyOption = $this->academyPlanOption();
        $teacherMonthlyOption = $this->teacherPlanOption();

        $academyProvider = $this->provider(
            type: ProviderType::Academy,
            owner: $academyOwner,
            slug: 'future-stars-academy',
            name: 'Future Stars Academy',
            country: $egypt,
            city: $cairo,
            subdomain: 'future-stars',
        );

        $this->providerSubscription($academyProvider, $academyMonthlyOption);

        $this->account(
            type: AccountType::Academy,
            owner: $academyOwner,
            provider: $academyProvider,
        );

        $academyProvider->update([
            'primary_color' => '#f59e0b',
            'secondary_color' => '#111827',
            'completion_watch_percentage' => 75,
            'contact_phone' => '01000001000',
            'contact_whatsapp' => '01000001000',
            'contact_email' => 'info@future-stars.test',
            'facebook_link' => 'https://facebook.com/future-stars-academy',
            'instagram_link' => 'https://instagram.com/future-stars-academy',
            'terms_conditions' => '<p>Students must follow academy learning and payment policies.</p>',
        ]);

        $academyCoverages = $this->syncProviderCoverage(
            providerId: $academyProvider->id,
            gradeSubjects: GradeSubject::query()->with(['grade', 'track', 'subject'])->get(),
        );

        $academyTeachers = $this->academyTeachers($academyProvider->id, [
            $academyTeacherUser,
            $scienceTeacherUser,
        ]);

        $this->assignTeachersToCoverages($academyTeachers, $academyCoverages);

        $standaloneTeacherProvider = $this->provider(
            type: ProviderType::StandaloneTeacher,
            owner: $standaloneTeacherUser,
            slug: 'falta-platform',
            name: 'Mr Falta',
            country: $egypt,
            city: $cairo,
            subdomain: 'falta',
        );

        $this->providerSubscription($standaloneTeacherProvider, $teacherMonthlyOption);

        $this->account(
            type: AccountType::StandaloneTeacher,
            owner: $standaloneTeacherUser,
            provider: $standaloneTeacherProvider,
        );

        $standaloneTeacherProvider->update([
            'primary_color' => '#2563eb',
            'secondary_color' => '#0f172a',
            'completion_watch_percentage' => 70,
            'bio' => [
                'en' => 'With over 15 years of experience teaching mathematics, Mr. Ahmed has successfully transformed the subject from a source of anxiety for students into an enjoyable and accessible one. His teaching method emphasizes a deep understanding of the rules rather than rote memorization, focusing on problem-solving skills and logical reasoning.',
                'ar' => 'بخبرة تزيد عن 15 عاماً في تدريس مناهج الرياضيات، نجح مستر أحمد في تحويل المادة من "عقدة" لدى الطلاب إلى مادة ممتعة وسهلة. يعتمد في أسلوبه على الفهم العميق للقوانين بدلاً من الحفظ، مع التركيز على مهارات حل المشكلات والتفكير المنطقي.',
            ],
        ]);

        $this->syncProviderCoverage(
            providerId: $standaloneTeacherProvider->id,
            gradeSubjects: $this->standaloneTeacherGradeSubjects(),
        );

        $this->removeAcademyTeacherAssignmentsForUser($standaloneTeacherUser);

        $this->account(
            type: AccountType::Student,
            owner: $studentUser,
            provider: $academyProvider,
        );

        StudentProfile::query()->updateOrCreate([
            'user_id' => $studentUser->id,
        ], [
            'email' => 'omar.student@example.test',
            'gender' => 'male',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'education_stage_id' => $secondaryTwoPhysics->grade->education_stage_id,
            'grade_id' => $secondaryTwoPhysics->grade_id,
            'school_name' => 'Future Stars Secondary School',
        ]);

        $this->account(
            type: AccountType::Parent,
            owner: $parentUser,
            provider: $academyProvider,
        );
    }

    private function academyPlanOption(): ProviderPlanOption
    {
        $academyPlan = ProviderPlan::query()->firstOrCreate([
            'sort_order' => 1,
        ], [
            'name' => $this->translation('Academy Growth', 'نمو الأكاديمية'),
            'description' => $this->translation(
                'Core plan for academies with multiple teachers and students.',
                'باقة أساسية للأكاديميات التي تضم عدة معلمين وطلاب.'
            ),
            'max_students' => 1000,
            'max_courses' => 250,
            'max_teachers' => 50,
            'features' => '<ul><li>Academy subdomain</li><li>Teacher accounts</li><li>Payments</li></ul>',
            'is_active' => true,
        ]);

        return $this->providerPlanOption($academyPlan, billingPeriodDays: 30, price: 1500);
    }

    private function teacherPlanOption(): ProviderPlanOption
    {
        $teacherPlan = ProviderPlan::query()->firstOrCreate([
            'sort_order' => 2,
        ], [
            'name' => $this->translation('Teacher Starter', 'بداية المعلم'),
            'description' => $this->translation(
                'Starter plan for standalone teachers.',
                'باقة بداية للمعلمين المستقلين.'
            ),
            'max_students' => 300,
            'max_courses' => 50,
            'max_teachers' => 1,
            'features' => '<ul><li>Teacher subdomain</li><li>Payments</li></ul>',
            'is_active' => true,
        ]);

        return $this->providerPlanOption($teacherPlan, billingPeriodDays: 30, price: 500);
    }

    private function gradeSubject(string $stageName, int $gradeSortOrder, string $subjectName, string $trackCode): GradeSubject
    {
        $grade = Grade::query()
            ->whereHas('educationStage', fn ($query) => $query->where('name->en', $stageName))
            ->where('sort_order', $gradeSortOrder)
            ->firstOrFail();

        $track = Track::query()->where('code', $trackCode)->firstOrFail();

        $subject = Subject::query()
            ->where('name', $this->subjectName($subjectName))
            ->firstOrFail();

        return GradeSubject::query()
            ->where('grade_id', $grade->id)
            ->where('track_id', $track->id)
            ->where('subject_id', $subject->id)
            ->firstOrFail();
    }

    private function subjectName(string $englishName): string
    {
        return match ($englishName) {
            'Arabic Language' => 'عربي',
            'Arabic' => 'عربي',
            'Mathematics' => 'رياضيات',
            'Math' => 'Math',
            'English Language' => 'English (O.L)',
            'English (O.L)' => 'English (O.L)',
            'English (A.L)' => 'English (A.L)',
            'Social Studies' => 'دراسات',
            'Science' => 'علوم',
            'French Language' => 'French',
            'French' => 'French',
            'Italian Language' => 'Italian',
            'Italian' => 'Italian',
            'German' => 'German',
            'History' => 'تاريخ',
            'Geography' => 'جغرافيا',
            'Chemistry' => 'كيمياء',
            'Physics' => 'فيزياء',
            'Biology' => 'أحياء',
            'Philosophy and Logic' => 'فلسفة ومنطق',
            'Psychology and Sociology' => 'علم نفس',
            'Pure Mathematics' => 'رياضة بحتة',
            'Pure Math' => 'Pure Math',
            'Applied Mathematics' => 'رياضة تطبيقية',
            'Applied Math' => 'Applied Math',
            'Statistics' => 'إحصاء',
            'Accounting' => 'محاسبة',
            'Business Management' => 'إدارة أعمال',
            'Economy' => 'اقتصاد',
            default => $englishName,
        };
    }

    private function accountSubject(int $providerId, int $gradeSubjectId): AccountSubject
    {
        /** @var AccountSubject $accountSubject */
        $accountSubject = AccountSubject::query()->withTrashed()->firstOrNew([
            'provider_id' => $providerId,
            'grade_subject_id' => $gradeSubjectId,
        ]);

        $accountSubject->fill([
            'is_active' => true,
        ]);

        $accountSubject->restore();
        $accountSubject->save();

        return $accountSubject;
    }

    private function teacherGradeSubject(int $academyTeacherId, int $accountSubjectId): void
    {
        /** @var AcademyTeacherGradeSubject $assignment */
        $assignment = AcademyTeacherGradeSubject::query()->withTrashed()->firstOrNew([
            'academy_teacher_id' => $academyTeacherId,
            'account_subject_id' => $accountSubjectId,
        ]);

        $assignment->fill([
            'is_active' => true,
        ]);

        $assignment->restore();
        $assignment->save();
    }

    /**
     * @param  Collection<int, GradeSubject>  $gradeSubjects
     * @return Collection<int, AccountSubject>
     */
    private function syncProviderCoverage(int $providerId, Collection $gradeSubjects): Collection
    {
        $gradeSubjectIds = $gradeSubjects
            ->pluck('id')
            ->map(fn (int|string $id): int => (int) $id)
            ->values();

        $coverages = $gradeSubjectIds
            ->map(fn (int $gradeSubjectId): AccountSubject => $this->accountSubject($providerId, $gradeSubjectId));

        AccountSubject::query()
            ->where('provider_id', $providerId)
            ->whereNotIn('grade_subject_id', $gradeSubjectIds)
            ->get()
            ->each(function (AccountSubject $accountSubject): void {
                $accountSubject->teacherAssignments()->delete();
                $accountSubject->courses()->delete();
                $accountSubject->delete();
            });

        return $coverages;
    }

    /**
     * @return Collection<int, GradeSubject>
     */
    private function standaloneTeacherGradeSubjects(): Collection
    {
        return collect([
            $this->gradeSubject('Elementary Stage', 4, 'Math', 'general'),
            $this->gradeSubject('Elementary Stage', 5, 'Math', 'general'),
            $this->gradeSubject('Elementary Stage', 6, 'Math', 'general'),
            $this->gradeSubject('Preparatory Stage', 7, 'Math', 'general'),
            $this->gradeSubject('Preparatory Stage', 8, 'Math', 'general'),
            $this->gradeSubject('Preparatory Stage', 9, 'Math', 'general'),
            $this->gradeSubject('Secondary Stage', 10, 'Math', 'general'),
            $this->gradeSubject('Secondary Stage', 11, 'Math', 'secondary_new_medicine_life_sciences'),
            $this->gradeSubject('Secondary Stage', 12, 'Math', 'secondary_old_scientific_math'),
            $this->gradeSubject('Secondary Stage', 12, 'Math', 'secondary_new_business'),
            $this->gradeSubject('Secondary Stage', 12, 'Math', 'secondary_new_engineering_computer_science'),
        ]);
    }

    /**
     * @param  array<int, User>  $users
     * @return Collection<int, AcademyTeacher>
     */
    private function academyTeachers(int $providerId, array $users): Collection
    {
        return collect($users)
            ->map(function ($user) use ($providerId): AcademyTeacher {
                $account = $this->account(
                    type: AccountType::AcademyTeacher,
                    owner: $user,
                    provider: Provider::query()->findOrFail($providerId),
                );

                return AcademyTeacher::query()->updateOrCreate([
                    'provider_id' => $providerId,
                    'teacher_account_id' => $account->id,
                ], [
                    'is_active' => true,
                ]);
            });
    }

    private function removeAcademyTeacherAssignmentsForUser(User $user): void
    {
        AcademyTeacher::query()
            ->whereHas('teacher', fn ($query) => $query->where('owner_user_id', $user->id))
            ->get()
            ->each(function (AcademyTeacher $academyTeacher): void {
                $academyTeacher->accountSubjects()->detach();
                $academyTeacher->delete();
            });
    }

    /**
     * @param  Collection<int, AcademyTeacher>  $academyTeachers
     * @param  Collection<int, AccountSubject>  $coverages
     */
    private function assignTeachersToCoverages(Collection $academyTeachers, Collection $coverages): void
    {
        $academyTeachers->each(function (AcademyTeacher $academyTeacher) use ($coverages): void {
            $coverages->each(fn (AccountSubject $coverage) => $this->teacherGradeSubject($academyTeacher->id, $coverage->id));
        });
    }
}
