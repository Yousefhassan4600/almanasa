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
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\Subject;
use App\Models\Track;

class ProvidersAndAccountsSeeder extends BaseSeeder
{
    public function run(): void
    {
        $egypt = Country::query()->where('code', 'EG')->firstOrFail();
        $cairo = City::query()
            ->where('country_id', $egypt->id)
            ->where('name->en', 'Cairo')
            ->firstOrFail();

        $secondaryOneMath = $this->gradeSubject('Secondary', 10, 'Mathematics', 'scientific_math');
        $secondaryOnePhysics = $this->gradeSubject('Secondary', 10, 'Physics', 'scientific');

        $saasOwner = $this->user('01000000000', 'Almanasa', 'Owner');
        $academyOwner = $this->user('01000000001', 'Academy', 'Owner');
        $secondAcademyOwner = $this->user('01000000006', 'Science', 'Owner');
        $academyTeacherUser = $this->user('01000000002', 'Ahmed', 'Teacher');
        $standaloneTeacherUser = $this->user('01000000003', 'Mona', 'Teacher');
        $studentUser = $this->user('01000000004', 'Omar', 'Student');
        $parentUser = $this->user('01000000005', 'Sara', 'Parent');

        $saasAccount = $this->account(
            type: AccountType::SaasOwner,
            owner: $saasOwner,
        );

        foreach (['support_manager', 'tenant_reviewer', 'finance_admin'] as $roleName) {
            $this->role($saasAccount, $roleName, $saasAccount);
        }

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

        $academyAccount = $this->account(
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

        foreach (['academy_admin', 'content_manager', 'payment_reviewer'] as $roleName) {
            $this->role($academyAccount, $roleName, $academyAccount);
        }

        $academyMathCoverage = $this->accountSubject($academyProvider->id, $secondaryOneMath->id);
        $academyPhysicsCoverage = $this->accountSubject($academyProvider->id, $secondaryOnePhysics->id);

        $secondAcademyProvider = $this->provider(
            type: ProviderType::Academy,
            owner: $secondAcademyOwner,
            slug: 'science-gate-academy',
            name: 'Science Gate Academy',
            country: $egypt,
            city: $cairo,
            subdomain: 'science-gate',
        );

        $this->providerSubscription($secondAcademyProvider, $academyMonthlyOption);

        $secondAcademyAccount = $this->account(
            type: AccountType::Academy,
            owner: $secondAcademyOwner,
            provider: $secondAcademyProvider,
        );

        $secondAcademyProvider->update([
            'primary_color' => '#16a34a',
            'secondary_color' => '#1f2937',
            'completion_watch_percentage' => 70,
            'contact_phone' => '01000002000',
            'contact_whatsapp' => '01000002000',
            'contact_email' => 'info@science-gate.test',
            'facebook_link' => 'https://facebook.com/science-gate-academy',
            'terms_conditions' => '<p>Enrollment is subject to academy approval and active subscription.</p>',
        ]);

        foreach (['academy_admin', 'content_manager'] as $roleName) {
            $this->role($secondAcademyAccount, $roleName, $secondAcademyAccount);
        }

        $secondAcademyPhysicsCoverage = $this->accountSubject($secondAcademyProvider->id, $secondaryOnePhysics->id);

        $academyTeacherAccount = $this->account(
            type: AccountType::AcademyTeacher,
            owner: $academyTeacherUser,
            provider: $academyProvider,
        );

        $academyTeacherAssignment = AcademyTeacher::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'teacher_account_id' => $academyTeacherAccount->id,
        ], [
            'is_active' => true,
        ]);

        $this->teacherGradeSubject($academyTeacherAssignment->id, $academyMathCoverage->id);
        $this->teacherGradeSubject($academyTeacherAssignment->id, $academyPhysicsCoverage->id);

        $secondAcademyTeacherAccount = $this->account(
            type: AccountType::AcademyTeacher,
            owner: $academyTeacherUser,
            provider: $secondAcademyProvider,
        );

        $secondAcademyTeacherAssignment = AcademyTeacher::query()->firstOrCreate([
            'provider_id' => $secondAcademyProvider->id,
            'teacher_account_id' => $secondAcademyTeacherAccount->id,
        ], [
            'is_active' => true,
        ]);

        $this->teacherGradeSubject($secondAcademyTeacherAssignment->id, $secondAcademyPhysicsCoverage->id);

        $standaloneTeacherProvider = $this->provider(
            type: ProviderType::StandaloneTeacher,
            owner: $standaloneTeacherUser,
            slug: 'mona-physics-platform',
            name: 'Mona Physics Platform',
            country: $egypt,
            city: $cairo,
            subdomain: 'mona-physics',
        );

        $this->providerSubscription($standaloneTeacherProvider, $teacherMonthlyOption);

        $standaloneTeacherAccount = $this->account(
            type: AccountType::StandaloneTeacher,
            owner: $standaloneTeacherUser,
            provider: $standaloneTeacherProvider,
        );

        $standaloneTeacherProvider->update([
            'primary_color' => '#2563eb',
            'secondary_color' => '#0f172a',
            'completion_watch_percentage' => 80,
            'contact_phone' => '01000003000',
            'contact_whatsapp' => '01000003000',
            'contact_email' => 'mona@mona-physics.test',
            'facebook_link' => 'https://facebook.com/mona-physics',
            'instagram_link' => 'https://instagram.com/mona-physics',
            'terms_conditions' => '<p>Course access follows the teacher subscription and attendance policies.</p>',
        ]);

        foreach (['content_assistant', 'student_support'] as $roleName) {
            $this->role($standaloneTeacherAccount, $roleName, $standaloneTeacherAccount);
        }

        $this->account(
            type: AccountType::Student,
            owner: $studentUser,
            provider: $academyProvider,
        );

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
            ->where('track_id', $track->id)
            ->where('name->en', $subjectName)
            ->firstOrFail();

        return GradeSubject::query()
            ->where('grade_id', $grade->id)
            ->where('subject_id', $subject->id)
            ->firstOrFail();
    }

    private function accountSubject(int $providerId, int $gradeSubjectId): AccountSubject
    {
        return AccountSubject::query()->firstOrCreate([
            'provider_id' => $providerId,
            'grade_subject_id' => $gradeSubjectId,
        ], [
            'is_active' => true,
        ]);
    }

    private function teacherGradeSubject(int $academyTeacherId, int $accountSubjectId): void
    {
        AcademyTeacherGradeSubject::query()->firstOrCreate([
            'academy_teacher_id' => $academyTeacherId,
            'account_subject_id' => $accountSubjectId,
        ], [
            'is_active' => true,
        ]);
    }
}
