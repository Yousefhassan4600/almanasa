<?php

namespace Database\Seeders;

use App\Enums\AccountMemberRole;
use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\ContentStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\LessonItemType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\Account;
use App\Models\AccountMembership;
use App\Models\AccountSetting;
use App\Models\AccountSubject;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\CourseUnit;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageCourse;
use App\Models\Payment;
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
            'currency_code' => 'EGP',
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

        $math = Subject::query()
            ->where('name->en', 'Mathematics')
            ->firstOrCreate([], [
                'name' => $this->translation('Mathematics', 'الرياضيات'),
                'description' => $this->translation(
                    'Core secondary mathematics subject.',
                    'مادة الرياضيات الأساسية للمرحلة الثانوية.'
                ),
            ]);

        $physics = Subject::query()
            ->where('name->en', 'Physics')
            ->firstOrCreate([], [
                'name' => $this->translation('Physics', 'الفيزياء'),
                'description' => $this->translation(
                    'Secondary physics subject.',
                    'مادة الفيزياء للمرحلة الثانوية.'
                ),
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

        $secondaryOneMath = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $math->id,
            'track_id' => $scientificTrack->id,
        ]);

        $secondaryOnePhysics = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $physics->id,
            'track_id' => $literaryTrack->id,
        ]);

        $secondaryTwoMath = GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryTwo->id,
            'subject_id' => $math->id,
            'track_id' => $literaryTrack->id,
        ]);

        $saasOwner = $this->user('01000000000', 'Almanasa', 'Owner', 'admin@almanasa.test');
        $academyOwner = $this->user('01000000001', 'Academy', 'Owner', 'academy.owner@almanasa.test');
        $secondAcademyOwner = $this->user('01000000006', 'Science', 'Owner', 'science.owner@almanasa.test');
        $academyTeacherUser = $this->user('01000000002', 'Ahmed', 'Teacher', 'ahmed.teacher@almanasa.test');
        $standaloneTeacherUser = $this->user('01000000003', 'Mona', 'Teacher', 'mona.teacher@almanasa.test');
        $studentUser = $this->user('01000000004', 'Omar', 'Student', 'omar.student@almanasa.test');
        $parentUser = $this->user('01000000005', 'Sara', 'Parent', 'sara.parent@almanasa.test');

        $saasAccount = $this->account(
            type: AccountType::SaasOwner,
            owner: $saasOwner,
            slug: 'almanasa-saas',
            name: 'Almanasa SaaS',
            phone: '01000000000',
            email: 'admin@almanasa.test',
            country: $egypt,
            city: $cairo,
        );

        foreach (['support_manager', 'tenant_reviewer', 'finance_admin'] as $roleName) {
            $this->role($saasAccount, $roleName, $saasOwner);
        }

        $this->membership($saasAccount, $saasOwner, AccountMemberRole::Owner, $saasOwner);

        $academyAccount = $this->account(
            type: AccountType::Academy,
            owner: $academyOwner,
            slug: 'future-stars-academy',
            name: 'Future Stars Academy',
            phone: '01000001000',
            email: 'academy@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $saasAccount,
            subdomain: 'future-stars',
        );

        AccountSetting::query()->firstOrCreate([
            'account_id' => $academyAccount->id,
        ], [
            'primary_color' => '#f59e0b',
            'secondary_color' => '#111827',
            'completion_watch_percentage' => 75,
        ]);

        $academyAdminRole = $this->role($academyAccount, 'academy_admin', $academyOwner);
        $this->role($academyAccount, 'content_manager', $academyOwner);
        $this->role($academyAccount, 'payment_reviewer', $academyOwner);
        $this->membership($academyAccount, $academyOwner, AccountMemberRole::Owner, $saasOwner);
        $this->membership($academyAccount, $saasOwner, AccountMemberRole::Admin, $academyOwner, $academyAdminRole);

        $academyMathCoverage = AccountSubject::query()->firstOrCreate([
            'account_id' => $academyAccount->id,
            'grade_subject_id' => $secondaryOneMath->id,
        ], [
            'is_active' => true,
        ]);

        $academyPhysicsCoverage = AccountSubject::query()->firstOrCreate([
            'account_id' => $academyAccount->id,
            'grade_subject_id' => $secondaryOnePhysics->id,
        ], [
            'is_active' => true,
        ]);

        $secondAcademyAccount = $this->account(
            type: AccountType::Academy,
            owner: $secondAcademyOwner,
            slug: 'science-gate-academy',
            name: 'Science Gate Academy',
            phone: '01000006000',
            email: 'science.academy@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $saasAccount,
            subdomain: 'science-gate',
        );

        AccountSetting::query()->firstOrCreate([
            'account_id' => $secondAcademyAccount->id,
        ], [
            'primary_color' => '#16a34a',
            'secondary_color' => '#1f2937',
            'completion_watch_percentage' => 70,
        ]);

        $secondAcademyAdminRole = $this->role($secondAcademyAccount, 'academy_admin', $secondAcademyOwner);
        $this->role($secondAcademyAccount, 'content_manager', $secondAcademyOwner);
        $this->membership($secondAcademyAccount, $secondAcademyOwner, AccountMemberRole::Owner, $saasOwner);
        $this->membership($secondAcademyAccount, $saasOwner, AccountMemberRole::Admin, $secondAcademyOwner, $secondAcademyAdminRole);

        $secondAcademyPhysicsCoverage = AccountSubject::query()->firstOrCreate([
            'account_id' => $secondAcademyAccount->id,
            'grade_subject_id' => $secondaryOnePhysics->id,
        ], [
            'is_active' => true,
        ]);

        $academyTeacherAccount = $this->account(
            type: AccountType::AcademyTeacher,
            owner: $academyTeacherUser,
            slug: 'ahmed-math-teacher',
            name: 'Ahmed Mathematics Teacher',
            phone: '01000002000',
            email: 'ahmed.teacher@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $saasAccount,
        );

        $assistantRole = $this->role($academyTeacherAccount, 'teaching_assistant', $academyTeacherUser);
        $this->membership($academyTeacherAccount, $academyTeacherUser, AccountMemberRole::Teacher, $academyOwner);
        $this->membership($academyTeacherAccount, $academyOwner, AccountMemberRole::Staff, $academyTeacherUser, $assistantRole);

        $academyTeacherAssignment = AcademyTeacher::query()->firstOrCreate([
            'academy_account_id' => $academyAccount->id,
            'teacher_account_id' => $academyTeacherAccount->id,
        ], [
            'status' => AccountStatus::Active,
            'joined_at' => now(),
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

        $secondAcademyTeacherAssignment = AcademyTeacher::query()->firstOrCreate([
            'academy_account_id' => $secondAcademyAccount->id,
            'teacher_account_id' => $academyTeacherAccount->id,
        ], [
            'status' => AccountStatus::Active,
            'joined_at' => now(),
        ]);

        AcademyTeacherGradeSubject::query()->firstOrCreate([
            'academy_teacher_id' => $secondAcademyTeacherAssignment->id,
            'account_subject_id' => $secondAcademyPhysicsCoverage->id,
        ], [
            'is_active' => true,
        ]);

        $standaloneTeacherAccount = $this->account(
            type: AccountType::StandaloneTeacher,
            owner: $standaloneTeacherUser,
            slug: 'mona-physics-platform',
            name: 'Mona Physics Platform',
            phone: '01000003000',
            email: 'mona.teacher@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $saasAccount,
            subdomain: 'mona-physics',
        );

        AccountSetting::query()->firstOrCreate([
            'account_id' => $standaloneTeacherAccount->id,
        ], [
            'primary_color' => '#2563eb',
            'secondary_color' => '#0f172a',
            'completion_watch_percentage' => 80,
        ]);

        $this->role($standaloneTeacherAccount, 'content_assistant', $standaloneTeacherUser);
        $this->role($standaloneTeacherAccount, 'student_support', $standaloneTeacherUser);
        $this->membership($standaloneTeacherAccount, $standaloneTeacherUser, AccountMemberRole::Owner, $saasOwner);

        $studentAccount = $this->account(
            type: AccountType::Student,
            owner: $studentUser,
            slug: 'omar-student-account',
            name: 'Omar Student Account',
            phone: '01000000004',
            email: 'omar.student@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $academyAccount,
        );

        $this->membership($studentAccount, $studentUser, AccountMemberRole::Student, $academyOwner);

        $parentAccount = $this->account(
            type: AccountType::Parent,
            owner: $parentUser,
            slug: 'sara-parent-account',
            name: 'Sara Parent Account',
            phone: '01000000005',
            email: 'sara.parent@almanasa.test',
            country: $egypt,
            city: $cairo,
            parent: $studentAccount,
        );

        $this->membership($parentAccount, $parentUser, AccountMemberRole::Parent, $studentUser);

        $academyCourse = Course::query()->firstOrCreate([
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
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
            'account_id' => $academyAccount->id,
            'student_user_id' => $studentUser->id,
            'method' => PaymentMethod::Instapay,
            'status' => PaymentStatus::Paid,
            'amount' => 500,
            'paid_at' => now(),
            'reviewed_by_user_id' => $academyOwner->id,
            'reviewed_at' => now(),
        ]);
    }

    private function user(string $phone, string $firstName, string $lastName, string $email): User
    {
        return User::query()->firstOrCreate([
            'phone' => $phone,
        ], [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'dial_country_code' => '+20',
            'password' => bcrypt('123456'),
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }

    private function account(
        AccountType $type,
        User $owner,
        string $slug,
        string $name,
        string $phone,
        string $email,
        Country $country,
        City $city,
        ?Account $parent = null,
        ?string $subdomain = null,
    ): Account {
        return Account::query()->firstOrCreate([
            'slug' => $slug,
        ], [
            'type' => $type,
            'owner_user_id' => $owner->id,
            'parent_account_id' => $parent?->id,
            'name' => $name,
            'subdomain' => $subdomain,
            'phone' => $phone,
            'email' => $email,
            'country_id' => $country->id,
            'city_id' => $city->id,
            'status' => AccountStatus::Active,
            'approved_at' => now(),
        ]);
    }

    private function role(
        Account $account,
        string $name,
        User $creator,
        bool $isAssignable = true,
    ): Role {
        return Role::query()->firstOrCreate([
            'account_id' => $account->id,
            'name' => $name,
        ], [
            'guard_name' => 'web',
            'created_by_user_id' => $creator->id,
            'is_assignable' => $isAssignable,
        ]);
    }

    private function membership(
        Account $account,
        User $user,
        AccountMemberRole $predefinedRole,
        User $creator,
        ?Role $customRole = null,
    ): AccountMembership {
        return AccountMembership::query()->firstOrCreate([
            'account_id' => $account->id,
            'user_id' => $user->id,
        ], [
            'predefined_role' => $predefinedRole,
            'role_id' => $customRole?->id,
            'created_by_user_id' => $creator->id,
            'status' => 'active',
            'joined_at' => now(),
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
