<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ContentStatus;
use App\Enums\EmployeeRole;
use App\Enums\EnrollmentStatus;
use App\Enums\LessonItemType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Enums\SubscriptionStatus;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\CourseUnit;
use App\Models\EducationStage;
use App\Models\Employee;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageCourse;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\ProviderSubscription;
use App\Models\Role;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::query()->firstOrCreate([
            'code' => 'EG',
        ], [
            'name' => $this->translation('Egypt', 'مصر'),
            'phone_code' => '+20',
        ]);

        $cairo = City::query()->firstOrCreate([
            'country_id' => $egypt->id,
        ], [
            'name' => $this->translation('Cairo', 'القاهرة'),
        ]);

        $elementary = EducationStage::query()->firstOrCreate([
            'sort_order' => 1,
        ], [
            'name' => $this->translation('Elementary', 'المرحلة الابتدائية'),
        ]);

        $primary = EducationStage::query()->firstOrCreate([
            'sort_order' => 2,
        ], [
            'name' => $this->translation('Primary', 'المرحلة الإعدادية'),
        ]);

        $secondary = EducationStage::query()->firstOrCreate([
            'sort_order' => 3,
        ], [
            'name' => $this->translation('Secondary', 'المرحلة الثانوية'),
        ]);

        $elementaryOne = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 1,
        ], [
            'name' => $this->translation('One', 'الصف الأول'),
        ]);

        $elementaryTwo = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 2,
        ], [
            'name' => $this->translation('Two', 'الصف الثاني'),
        ]);

        $elementaryThree = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 3,
        ], [
            'name' => $this->translation('Three', 'الصف الثالث'),
        ]);

        $elementaryFour = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 4,
        ], [
            'name' => $this->translation('Four', 'الصف الرابع'),
        ]);

        $elementaryFive = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 5,
        ], [
            'name' => $this->translation('Five', 'الصف الخامس'),
        ]);

        $elementarySix = Grade::query()->firstOrCreate([
            'education_stage_id' => $elementary->id,
            'sort_order' => 6,
        ], [
            'name' => $this->translation('Six', 'الصف السادس'),
        ]);

        $primaryOne = Grade::query()->firstOrCreate([
            'education_stage_id' => $primary->id,
            'sort_order' => 7,
        ], [
            'name' => $this->translation('One', 'الصف الأول'),
        ]);

        $primaryTwo = Grade::query()->firstOrCreate([
            'education_stage_id' => $primary->id,
            'sort_order' => 8,
        ], [
            'name' => $this->translation('Two', 'الصف الثاني'),
        ]);

        $primaryThree = Grade::query()->firstOrCreate([
            'education_stage_id' => $primary->id,
            'sort_order' => 9,
        ], [
            'name' => $this->translation('Three', 'الصف الثالث'),
        ]);

        $secondaryOne = Grade::query()->firstOrCreate([
            'education_stage_id' => $secondary->id,
            'sort_order' => 10,
        ], [
            'name' => $this->translation('One', 'الصف الأول'),
        ]);

        $secondaryTwo = Grade::query()->firstOrCreate([
            'education_stage_id' => $secondary->id,
            'sort_order' => 11,
        ], [
            'name' => $this->translation('Two', 'الصف الثاني'),
        ]);

        $secondaryThree = Grade::query()->firstOrCreate([
            'education_stage_id' => $secondary->id,
            'sort_order' => 12,
        ], [
            'name' => $this->translation('Three', 'الصف الثالث'),
        ]);

        $generalTrack = Track::query()->firstOrCreate([
            'code' => 'general',
        ], [
            'name' => $this->translation('General', 'عام'),
            'code' => 'general',
            'sort_order' => 0,
        ]);

        $scientificTrack = Track::query()->firstOrCreate([
            'code' => 'scientific',
        ], [
            'name' => $this->translation('Scientific', 'علمي'),
            'code' => 'scientific',
            'sort_order' => 1,
        ]);

        $literaryTrack = Track::query()->firstOrCreate([
            'code' => 'literary',
        ], [
            'name' => $this->translation('Literary', 'أدبي'),
            'code' => 'literary',
            'sort_order' => 2,
        ]);

        $scientificMathTrack = Track::query()->firstOrCreate([
            'code' => 'scientific_math',
        ], [
            'name' => $this->translation('Scientific (Mathematics)', 'علمي رياضة'),
            'code' => 'scientific_math',
            'sort_order' => 3,
        ]);

        $scientificSciencesTrack = Track::query()->firstOrCreate([
            'code' => 'scientific_sciences',
        ], [
            'name' => $this->translation('Scientific (Sciences)', 'علمي علوم'),
            'code' => 'scientific_sciences',
            'sort_order' => 4,
        ]);

        $math = Subject::query()
            ->where('track_id', $scientificMathTrack->id)
            ->where('name->en', 'Mathematics')
            ->firstOrCreate([], [
                'track_id' => $scientificMathTrack->id,
                'name' => $this->translation('Mathematics', 'الرياضيات'),
                'description' => $this->translation(
                    'Core secondary mathematics subject.',
                    'مادة الرياضيات الأساسية للمرحلة الثانوية.'
                ),
            ]);

        $physics = Subject::query()
            ->where('track_id', $scientificTrack->id)
            ->where('name->en', 'Physics')
            ->firstOrCreate([], [
                'track_id' => $scientificTrack->id,
                'name' => $this->translation('Physics', 'الفيزياء'),
                'description' => $this->translation(
                    'Secondary physics subject.',
                    'مادة الفيزياء للمرحلة الثانوية.'
                ),
            ]);

        $secondaryOneMath = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $math->id,
        ]);

        $secondaryOnePhysics = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $physics->id,
        ]);

        $secondaryTwoMath = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryTwo->id,
            'subject_id' => $math->id,
        ]);

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

        $academyMonthlyOption = $this->providerPlanOption($academyPlan, billingPeriodDays: 30, price: 1500);

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

        $teacherMonthlyOption = $this->providerPlanOption($teacherPlan, billingPeriodDays: 30, price: 500);

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
        ]);

        $this->role($academyAccount, 'academy_admin', $academyAccount);
        $this->role($academyAccount, 'content_manager', $academyAccount);
        $this->role($academyAccount, 'payment_reviewer', $academyAccount);

        $academyMathCoverage = AccountSubject::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'grade_subject_id' => $secondaryOneMath->id,
        ], [
            'is_active' => true,
        ]);

        $academyPhysicsCoverage = AccountSubject::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'grade_subject_id' => $secondaryOnePhysics->id,
        ], [
            'is_active' => true,
        ]);

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
        ]);

        $this->role($secondAcademyAccount, 'academy_admin', $secondAcademyAccount);
        $this->role($secondAcademyAccount, 'content_manager', $secondAcademyAccount);

        $secondAcademyPhysicsCoverage = AccountSubject::query()->firstOrCreate([
            'provider_id' => $secondAcademyProvider->id,
            'grade_subject_id' => $secondaryOnePhysics->id,
        ], [
            'is_active' => true,
        ]);

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

        AcademyTeacherGradeSubject::query()->firstOrCreate([
            'academy_teacher_id' => $academyTeacherAssignment->id,
            'account_subject_id' => $academyMathCoverage->id,
        ], [
            'is_active' => true,
        ]);

        AcademyTeacherGradeSubject::query()->firstOrCreate([
            'academy_teacher_id' => $academyTeacherAssignment->id,
            'account_subject_id' => $academyPhysicsCoverage->id,
        ], [
            'is_active' => true,
        ]);

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

        AcademyTeacherGradeSubject::query()->firstOrCreate([
            'academy_teacher_id' => $secondAcademyTeacherAssignment->id,
            'account_subject_id' => $secondAcademyPhysicsCoverage->id,
        ], [
            'is_active' => true,
        ]);

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
        ]);

        $this->role($standaloneTeacherAccount, 'content_assistant', $standaloneTeacherAccount);
        $this->role($standaloneTeacherAccount, 'student_support', $standaloneTeacherAccount);

        $studentAccount = $this->account(
            type: AccountType::Student,
            owner: $studentUser,
            provider: $academyProvider,
        );

        $parentAccount = $this->account(
            type: AccountType::Parent,
            owner: $parentUser,
            provider: $academyProvider,
        );

        $academyCourse = Course::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'slug' => 'secondary-1-mathematics-first-term',
        ], [
            'account_subject_id' => $academyMathCoverage->id,
            'teacher_account_id' => $academyTeacherAccount->id,
            'title' => $this->translation(
                'Secondary 1 Mathematics - First Term',
                'رياضيات الصف الأول الثانوي - الترم الأول'
            ),
            'description' => $this->translation(
                'Recorded mathematics course for Secondary 1 students.',
                'كورس رياضيات مسجل لطلاب الصف الأول الثانوي.'
            ),
            'term' => 'First Term',
            'price' => 500,
            'monthly_price' => 180,
            'weekly_lectures_count' => 2,
            'status' => ContentStatus::Published,
            'is_featured' => true,
            'published_at' => now(),
        ]);

        CourseOutcome::query()->firstOrCreate([
            'course_id' => $academyCourse->id,
        ], [
            'title' => $this->translation(
                'Solve first-term algebra and geometry problems confidently.',
                'حل مسائل الجبر والهندسة للترم الأول بثقة.'
            ),
        ]);

        $unit = CourseUnit::query()->firstOrCreate([
            'course_id' => $academyCourse->id,
            'title' => 'Algebra Foundations',
        ], [
            'description' => $this->translation(
                'Numbers, expressions, and equations.',
                'الأعداد والتعبيرات والمعادلات.'
            ),
            'term' => 'First Term',
            'sort_order' => 1,
            'status' => ContentStatus::Published,
        ]);

        $lesson = Lesson::query()->firstOrCreate([
            'course_id' => $academyCourse->id,
            'course_unit_id' => $unit->id,
            'sort_order' => 1,
        ], [
            'title' => $this->translation('Linear Equations', 'المعادلات الخطية'),
            'description' => $this->translation(
                'Recorded explanation and practice for linear equations.',
                'شرح مسجل وتدريبات على المعادلات الخطية.'
            ),
            'duration_seconds' => 1800,
            'is_free' => true,
            'status' => ContentStatus::Published,
            'published_at' => now(),
        ]);

        LessonItem::query()->firstOrCreate([
            'lesson_id' => $lesson->id,
            'type' => LessonItemType::Video,
        ], [
            'title' => $this->translation(
                'Linear Equations Recorded Lesson',
                'درس مسجل عن المعادلات الخطية'
            ),
            'video_url' => 'https://videos.example.test/linear-equations',
            'duration_seconds' => 1800,
            'sort_order' => 1,
            'is_required' => true,
        ]);

        LessonItem::query()->firstOrCreate([
            'lesson_id' => $lesson->id,
            'type' => LessonItemType::Pdf,
        ], [
            'title' => $this->translation(
                'Linear Equations Summary PDF',
                'ملخص المعادلات الخطية PDF'
            ),
            'file_url' => 'resources/linear-equations-summary.pdf',
            'sort_order' => 2,
            'is_required' => false,
        ]);

        $package = Package::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'duration_days' => 120,
        ], [
            'name' => $this->translation(
                'Secondary 1 Mathematics Term Package',
                'باقة رياضيات الصف الأول الثانوي للترم'
            ),
            'description' => $this->translation(
                'Access to the full first-term mathematics course.',
                'وصول كامل إلى كورس الرياضيات للترم الأول.'
            ),
            'price' => 500,
            'is_featured' => true,
            'status' => ContentStatus::Published,
        ]);

        PackageCourse::query()->firstOrCreate([
            'package_id' => $package->id,
            'course_id' => $academyCourse->id,
        ]);

        $subscription = Subscription::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addDays(120),
            'auto_renew' => false,
        ]);

        StudentEnrollment::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'course_id' => $academyCourse->id,
        ], [
            'package_id' => $package->id,
            'subscription_id' => $subscription->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now(),
            'expires_at' => now()->addDays(120),
        ]);

        $cart = Cart::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'status' => 'converted',
        ], [
            'subtotal' => 500,
            'tax' => 0,
            'discount' => 0,
            'total' => 500,
        ]);

        CartItem::query()->firstOrCreate([
            'cart_id' => $cart->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'quantity' => 1,
            'unit_price' => 500,
            'total' => 500,
        ]);

        $order = Order::query()->firstOrCreate([
            'order_number' => 'ORD-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'cart_id' => $cart->id,
            'subtotal' => 500,
            'tax' => 0,
            'discount' => 0,
            'total' => 500,
            'status' => PaymentStatus::Paid,
        ]);

        OrderItem::query()->firstOrCreate([
            'order_id' => $order->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'subscription_id' => $subscription->id,
            'title' => $package->name,
            'unit_price' => 500,
            'quantity' => 1,
            'total' => 500,
        ]);

        Payment::query()->firstOrCreate([
            'order_id' => $order->id,
            'transaction_reference' => 'PAY-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'method' => PaymentMethod::Instapay,
            'status' => PaymentStatus::Paid,
            'amount' => 500,
            'paid_at' => now(),
            'reviewed_by_user_id' => $academyOwner->id,
            'reviewed_at' => now(),
        ]);
    }

    private function user(string $phone, string $firstName, string $lastName): User
    {
        return User::query()->firstOrCreate([
            'phone' => $phone,
        ], [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dial_country_code' => '+20',
            'password' => bcrypt('123456'),
            'verified_at' => now(),
            'is_active' => true,
        ]);
    }

    private function provider(
        ProviderType $type,
        User $owner,
        string $slug,
        string $name,
        Country $country,
        City $city,
        ?string $subdomain = null,
    ): Provider {
        return Provider::query()->firstOrCreate([
            'slug' => $slug,
        ], [
            'type' => $type,
            'owner_user_id' => $owner->id,
            'name' => $name,
            'subdomain' => $subdomain,
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);
    }

    private function providerPlanOption(
        ProviderPlan $plan,
        int $billingPeriodDays,
        int $price,
        int $sortOrder = 0,
    ): ProviderPlanOption {
        return ProviderPlanOption::query()->firstOrCreate([
            'provider_plan_id' => $plan->id,
            'billing_period_days' => $billingPeriodDays,
        ], [
            'price' => $price,
            'sort_order' => $sortOrder,
        ]);
    }

    private function providerSubscription(Provider $provider, ProviderPlanOption $option): ProviderSubscription
    {
        return ProviderSubscription::query()->firstOrCreate([
            'provider_id' => $provider->id,
            'provider_plan_option_id' => $option->id,
        ], [
            'status' => ProviderSubscriptionStatus::Active,
            'amount' => $option->price,
            'starts_at' => now(),
            'ends_at' => now()->addDays($option->billing_period_days),
        ]);
    }

    private function account(
        AccountType $type,
        User $owner,
        ?Provider $provider = null,
    ): Account {
        return Account::query()->firstOrCreate([
            'provider_id' => $provider?->id,
            'type' => $type,
            'owner_user_id' => $owner->id,
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function role(
        Account $account,
        string $name,
        Account $creator,
        bool $isAssignable = true,
    ): Role {
        return Role::query()->firstOrCreate([
            'provider_id' => $account->provider_id,
            'name' => $name,
        ], [
            'guard_name' => 'web',
            'created_by_account_id' => $creator->id,
            'is_assignable' => $isAssignable,
        ]);
    }

    private function employee(
        Account $account,
        User $user,
        EmployeeRole $predefinedRole,
        User $creator,
        ?Role $customRole = null,
    ): Employee {
        return Employee::query()->firstOrCreate([
            'account_id' => $account->id,
            'user_id' => $user->id,
        ], [
            'predefined_role' => $customRole ? null : $predefinedRole,
            'role_id' => $customRole?->id,
            'created_by_user_id' => $creator->id,
            'is_active' => true,
        ]);
    }

    private function translation(string $en, string $ar): array
    {
        return [
            'en' => $en,
            'ar' => $ar,
        ];
    }
}
