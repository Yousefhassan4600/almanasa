<?php

namespace Database\Seeders;

use App\Enums\AcademicYearStatus;
use App\Enums\AccessType;
use App\Enums\AssessmentAttemptStatus;
use App\Enums\AssessmentType;
use App\Enums\CartStatus;
use App\Enums\CourseStatus;
use App\Enums\Currency;
use App\Enums\DurationType;
use App\Enums\EnrollmentStatus;
use App\Enums\LessonContentType;
use App\Enums\MembershipStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PlanType;
use App\Enums\ProgressStatus;
use App\Enums\PublishingStatus;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Enums\SubscriptionStatus;
use App\Enums\TenantRole;
use App\Enums\TenantStatus;
use App\Enums\TenantType;
use App\Enums\VideoProcessingStatus;
use App\Enums\VideoProvider;
use App\Enums\VideoVisibility;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\AttemptAnswer;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\EducationStage;
use App\Models\EducationTrack;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Resource;
use App\Models\StudentAcademicProfile;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\TeacherGradeSubjectAssignment;
use App\Models\Tenant;
use App\Models\TenantGradeSubject;
use App\Models\TenantUser;
use App\Models\Term;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = $this->seedUsers();
        $catalog = $this->seedEducationCatalog();

        $academy = $this->seedAcademyTenant($users, $catalog);
        $standalone = $this->seedStandaloneTeacherTenant($users, $catalog);

        $this->seedStudentProfiles($users, $catalog, $academy, $standalone);
        $this->seedCommerceAndProgress($users, $academy, 'academy');
        $this->seedCommerceAndProgress($users, $standalone, 'standalone');
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        return [
            'superAdmin' => $this->user('Super Admin', 'super.admin@almanasa.test'),
            'academyOwner' => $this->user('Nile Academy Owner', 'owner@nile-academy.test'),
            'academyAdmin' => $this->user('Nile Academy Admin', 'admin@nile-academy.test'),
            'mathTeacher' => $this->user('Ahmed Hassan', 'ahmed.math@nile-academy.test'),
            'physicsTeacher' => $this->user('Mona Saleh', 'mona.physics@nile-academy.test'),
            'standaloneTeacher' => $this->user('Youssef Samir', 'youssef.teacher@almanasa.test'),
            'studentA' => $this->user('Omar Ali', 'omar.student@almanasa.test'),
            'studentB' => $this->user('Laila Mohamed', 'laila.student@almanasa.test'),
            'support' => $this->user('Support Agent', 'support@almanasa.test'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seedEducationCatalog(): array
    {
        $primary = $this->stage('Primary', 'primary', 1);
        $preparatory = $this->stage('Preparatory', 'preparatory', 2);
        $secondary = $this->stage('Secondary', 'secondary', 3);

        $scientific = $this->track('Scientific', 'scientific', 1);
        $literary = $this->track('Literary', 'literary', 2);

        $grades = [
            'primary4' => $this->grade($primary, 'Primary 4', 'primary-4', 4),
            'preparatory1' => $this->grade($preparatory, 'Preparatory 1', 'preparatory-1', 7),
            'secondary1' => $this->grade($secondary, 'Secondary 1', 'secondary-1', 10),
            'secondary2' => $this->grade($secondary, 'Secondary 2', 'secondary-2', 11),
            'secondary3' => $this->grade($secondary, 'Secondary 3', 'secondary-3', 12),
        ];

        $subjects = [
            'arabic' => $this->subject('Arabic', 'arabic', 'Arabic language curriculum.'),
            'english' => $this->subject('English', 'english', 'English language curriculum.'),
            'math' => $this->subject('Mathematics', 'mathematics', 'Mathematics concepts and problem solving.'),
            'physics' => $this->subject('Physics', 'physics', 'Physics concepts, laws, and applications.'),
            'chemistry' => $this->subject('Chemistry', 'chemistry', 'Chemistry curriculum and experiments.'),
            'history' => $this->subject('History', 'history', 'History curriculum and source analysis.'),
        ];

        $gradeSubjects = [
            'primary4Arabic' => $this->gradeSubject($grades['primary4'], $subjects['arabic']),
            'secondary1Math' => $this->gradeSubject($grades['secondary1'], $subjects['math']),
            'secondary1Physics' => $this->gradeSubject($grades['secondary1'], $subjects['physics']),
            'secondary2MathScientific' => $this->gradeSubject($grades['secondary2'], $subjects['math'], $scientific),
            'secondary2PhysicsScientific' => $this->gradeSubject($grades['secondary2'], $subjects['physics'], $scientific),
            'secondary2HistoryLiterary' => $this->gradeSubject($grades['secondary2'], $subjects['history'], $literary),
            'secondary3MathScientific' => $this->gradeSubject($grades['secondary3'], $subjects['math'], $scientific),
            'secondary3ChemistryScientific' => $this->gradeSubject($grades['secondary3'], $subjects['chemistry'], $scientific),
        ];

        return compact('grades', 'subjects', 'gradeSubjects', 'scientific', 'literary');
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>
     */
    private function seedAcademyTenant(array $users, array $catalog): array
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'nile-academy'],
            [
                'owner_user_id' => $users['academyOwner']->id,
                'name' => 'Nile Academy',
                'type' => TenantType::Academy->value,
                'status' => TenantStatus::Active->value,
                'domain' => 'nile-academy.localhost',
                'settings' => [
                    'brand_color' => '#f59e0b',
                    'allow_course_reviews' => true,
                    'manual_payment_review_required' => true,
                ],
            ],
        );

        $memberships = [
            'owner' => $this->membership($tenant, $users['academyOwner'], TenantRole::Owner),
            'admin' => $this->membership($tenant, $users['academyAdmin'], TenantRole::Admin),
            'mathTeacher' => $this->membership($tenant, $users['mathTeacher'], TenantRole::Teacher),
            'physicsTeacher' => $this->membership($tenant, $users['physicsTeacher'], TenantRole::Teacher),
            'studentA' => $this->membership($tenant, $users['studentA'], TenantRole::Student),
            'studentB' => $this->membership($tenant, $users['studentB'], TenantRole::Student),
            'support' => $this->membership($tenant, $users['support'], TenantRole::Support),
        ];

        $offerings = [
            'secondary1Math' => $this->offering($tenant, $catalog['gradeSubjects']['secondary1Math']),
            'secondary1Physics' => $this->offering($tenant, $catalog['gradeSubjects']['secondary1Physics']),
            'secondary2MathScientific' => $this->offering($tenant, $catalog['gradeSubjects']['secondary2MathScientific']),
            'secondary2PhysicsScientific' => $this->offering($tenant, $catalog['gradeSubjects']['secondary2PhysicsScientific']),
        ];

        $assignments = [
            'mathSecondary1' => $this->assignment($tenant, $memberships['mathTeacher'], $offerings['secondary1Math']),
            'mathSecondary2' => $this->assignment($tenant, $memberships['mathTeacher'], $offerings['secondary2MathScientific']),
            'physicsSecondary1' => $this->assignment($tenant, $memberships['physicsTeacher'], $offerings['secondary1Physics']),
            'physicsSecondary2' => $this->assignment($tenant, $memberships['physicsTeacher'], $offerings['secondary2PhysicsScientific']),
        ];

        $academicYear = $this->academicYear($tenant, '2026/2027', true);
        $terms = [
            'first' => $this->term($academicYear, 'First Term', 1, '2026-09-15', '2027-01-15'),
            'second' => $this->term($academicYear, 'Second Term', 2, '2027-02-01', '2027-06-01'),
        ];

        $mathCourse = $this->course(
            tenant: $tenant,
            assignment: $assignments['mathSecondary1'],
            academicYear: $academicYear,
            term: $terms['first'],
            title: 'Secondary 1 Mathematics - First Term',
            slug: 'secondary-1-mathematics-first-term',
            price: 1200,
            featured: true,
        );

        $physicsCourse = $this->course(
            tenant: $tenant,
            assignment: $assignments['physicsSecondary1'],
            academicYear: $academicYear,
            term: $terms['first'],
            title: 'Secondary 1 Physics Foundations',
            slug: 'secondary-1-physics-foundations',
            price: 950,
        );

        $this->seedCourseContent($tenant, $mathCourse, $users['mathTeacher'], 'Algebra Unit', 'Linear Equations');
        $this->seedCourseContent($tenant, $physicsCourse, $users['physicsTeacher'], 'Motion Unit', 'Speed and Velocity');

        return compact('tenant', 'memberships', 'offerings', 'assignments', 'academicYear', 'terms') + [
            'courses' => [
                'math' => $mathCourse,
                'physics' => $physicsCourse,
            ],
        ];
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>
     */
    private function seedStandaloneTeacherTenant(array $users, array $catalog): array
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'youssef-math-platform'],
            [
                'owner_user_id' => $users['standaloneTeacher']->id,
                'name' => 'Youssef Math Platform',
                'type' => TenantType::StandaloneTeacher->value,
                'status' => TenantStatus::Active->value,
                'domain' => 'youssef-math.localhost',
                'settings' => [
                    'brand_color' => '#2563eb',
                    'standalone_teacher_owner_auto_assigned' => true,
                    'manual_payment_review_required' => true,
                ],
            ],
        );

        $memberships = [
            'ownerTeacher' => $this->membership($tenant, $users['standaloneTeacher'], TenantRole::Owner),
            'teacher' => $this->membership($tenant, $users['standaloneTeacher'], TenantRole::Teacher),
            'studentA' => $this->membership($tenant, $users['studentA'], TenantRole::Student),
            'studentB' => $this->membership($tenant, $users['studentB'], TenantRole::Student),
        ];

        $offerings = [
            'secondary2MathScientific' => $this->offering($tenant, $catalog['gradeSubjects']['secondary2MathScientific']),
            'secondary3MathScientific' => $this->offering($tenant, $catalog['gradeSubjects']['secondary3MathScientific']),
        ];

        $assignments = [
            'mathSecondary2' => $this->assignment($tenant, $memberships['teacher'], $offerings['secondary2MathScientific']),
            'mathSecondary3' => $this->assignment($tenant, $memberships['teacher'], $offerings['secondary3MathScientific']),
        ];

        $academicYear = $this->academicYear($tenant, '2026/2027', true);
        $terms = [
            'first' => $this->term($academicYear, 'First Term', 1, '2026-09-15', '2027-01-15'),
            'revision' => $this->term($academicYear, 'Final Revision', 3, '2027-05-01', '2027-06-15'),
        ];

        $mainCourse = $this->course(
            tenant: $tenant,
            assignment: $assignments['mathSecondary2'],
            academicYear: $academicYear,
            term: $terms['first'],
            title: 'Secondary 2 Scientific Mathematics',
            slug: 'secondary-2-scientific-mathematics',
            price: 800,
            featured: true,
        );

        $revisionCourse = $this->course(
            tenant: $tenant,
            assignment: $assignments['mathSecondary3'],
            academicYear: $academicYear,
            term: $terms['revision'],
            title: 'Secondary 3 Mathematics Final Revision',
            slug: 'secondary-3-mathematics-final-revision',
            price: 600,
        );

        $this->seedCourseContent($tenant, $mainCourse, $users['standaloneTeacher'], 'Functions Unit', 'Domain and Range');
        $this->seedCourseContent($tenant, $revisionCourse, $users['standaloneTeacher'], 'Revision Unit', 'Exam Strategy');

        return compact('tenant', 'memberships', 'offerings', 'assignments', 'academicYear', 'terms') + [
            'courses' => [
                'main' => $mainCourse,
                'revision' => $revisionCourse,
            ],
        ];
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, mixed>  $catalog
     * @param  array<string, mixed>  $academy
     * @param  array<string, mixed>  $standalone
     */
    private function seedStudentProfiles(array $users, array $catalog, array $academy, array $standalone): void
    {
        $this->studentProfile($academy['tenant'], $users['studentA'], $academy['academicYear'], $catalog['grades']['secondary1'], 'Future Language School');
        $this->studentProfile($academy['tenant'], $users['studentB'], $academy['academicYear'], $catalog['grades']['secondary1'], 'Nile International School');
        $this->studentProfile($standalone['tenant'], $users['studentA'], $standalone['academicYear'], $catalog['grades']['secondary2'], 'Future Language School');
        $this->studentProfile($standalone['tenant'], $users['studentB'], $standalone['academicYear'], $catalog['grades']['secondary3'], 'Nile International School');
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, mixed>  $tenantData
     */
    private function seedCommerceAndProgress(array $users, array $tenantData, string $prefix): void
    {
        /** @var Tenant $tenant */
        $tenant = $tenantData['tenant'];
        /** @var Course $primaryCourse */
        $primaryCourse = array_values($tenantData['courses'])[0];
        /** @var Course $secondaryCourse */
        $secondaryCourse = array_values($tenantData['courses'])[1];

        $coursePlan = $this->plan($tenant, "{$tenant->name} Single Course Plan", "{$prefix}-single-course-plan", PlanType::Course, 450);
        $packagePlan = $this->plan($tenant, "{$tenant->name} Term Package", "{$prefix}-term-package", PlanType::CustomizedPackage, 1600);

        $this->planItem($coursePlan, $primaryCourse);
        $this->planItem($packagePlan, $primaryCourse);
        $this->planItem($packagePlan, $secondaryCourse);

        $order = Order::updateOrCreate(
            ['order_number' => strtoupper($prefix).'-ORDER-0001'],
            [
                'tenant_id' => $tenant->id,
                'student_id' => $users['studentA']->id,
                'subtotal' => $primaryCourse->price,
                'discount' => 0,
                'tax' => 0,
                'total' => $primaryCourse->price,
                'currency' => Currency::Egp->value,
                'status' => OrderStatus::Paid->value,
                'payment_status' => PaymentStatus::Paid->value,
                'billing_name' => $users['studentA']->name,
                'billing_email' => $users['studentA']->email,
            ],
        );

        $orderItem = OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'item_type' => Course::class,
                'item_id' => $primaryCourse->id,
            ],
            [
                'title' => $primaryCourse->title,
                'unit_price' => $primaryCourse->price,
                'quantity' => 1,
                'total' => $primaryCourse->price,
                'metadata' => [
                    'course_slug' => $primaryCourse->slug,
                    'tenant_slug' => $tenant->slug,
                ],
            ],
        );

        $payment = Payment::updateOrCreate(
            [
                'order_id' => $order->id,
                'transaction_reference' => strtoupper($prefix).'-PAY-0001',
            ],
            [
                'tenant_id' => $tenant->id,
                'student_id' => $users['studentA']->id,
                'method' => PaymentMethod::ManualTransfer->value,
                'amount' => $order->total,
                'currency' => Currency::Egp->value,
                'status' => PaymentStatus::Paid->value,
                'provider_reference' => strtoupper($prefix).'-BANK-REF',
                'paid_at' => now()->subDays(3),
                'metadata' => [
                    'review_channel' => 'tenant_admin_dashboard',
                ],
            ],
        );

        PaymentProof::updateOrCreate(
            ['payment_id' => $payment->id],
            [
                'sender_phone' => '+201000000001',
                'transfer_reference' => strtoupper($prefix).'-BANK-REF',
                'receipt_path' => "demo/payment-proofs/{$prefix}-receipt.jpg",
                'status' => PaymentStatus::Approved->value,
                'reviewed_by' => $tenantData['memberships']['admin']->user_id ?? $tenantData['memberships']['ownerTeacher']->user_id,
                'reviewed_at' => now()->subDays(2),
                'rejection_reason' => null,
            ],
        );

        $subscription = Subscription::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'student_id' => $users['studentA']->id,
                'plan_id' => $coursePlan->id,
            ],
            [
                'order_id' => $order->id,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addMonths(4),
                'status' => SubscriptionStatus::Active->value,
                'auto_renew' => false,
                'cancelled_at' => null,
            ],
        );

        $enrollment = Enrollment::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'student_id' => $users['studentA']->id,
                'course_id' => $primaryCourse->id,
            ],
            [
                'subscription_id' => $subscription->id,
                'order_item_id' => $orderItem->id,
                'starts_at' => now()->subDays(2),
                'expires_at' => now()->addMonths(4),
                'status' => EnrollmentStatus::Active->value,
                'access_type' => AccessType::Paid->value,
                'granted_by' => null,
            ],
        );

        $cart = Cart::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'student_id' => $users['studentB']->id,
                'status' => CartStatus::Active->value,
            ],
        );

        CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'item_type' => Plan::class,
                'item_id' => $packagePlan->id,
            ],
            [
                'unit_price' => $packagePlan->price,
                'quantity' => 1,
            ],
        );

        $lesson = $primaryCourse->lessons()->orderBy('sort_order')->first();
        $videoContent = $lesson?->contents()->where('type', LessonContentType::RecordedVideo->value)->first();
        $video = $videoContent?->videoProgress()->first()?->video ?? Video::query()->where('lesson_content_id', $videoContent?->id)->first();

        if ($lesson) {
            LessonProgress::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'student_id' => $users['studentA']->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'status' => ProgressStatus::InProgress->value,
                    'started_at' => now()->subDay(),
                    'completed_at' => null,
                    'last_activity_at' => now()->subHours(4),
                ],
            );
        }

        if ($videoContent && $video) {
            VideoProgress::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'student_id' => $users['studentA']->id,
                    'lesson_content_id' => $videoContent->id,
                ],
                [
                    'video_id' => $video->id,
                    'watched_seconds' => 930,
                    'last_position_seconds' => 1020,
                    'watch_percentage' => 65,
                    'started_at' => now()->subDay(),
                    'completed_at' => null,
                    'last_watched_at' => now()->subHours(4),
                    'status' => ProgressStatus::InProgress->value,
                ],
            );
        }

        $this->seedAssessmentAttempt($tenant, $primaryCourse, $users['studentA'], $enrollment);
    }

    private function seedCourseContent(Tenant $tenant, Course $course, User $teacher, string $sectionTitle, string $lessonTitle): void
    {
        $section = CourseSection::firstOrCreate(
            [
                'course_id' => $course->id,
                'title' => $sectionTitle,
            ],
            [
                'description' => "Core concepts for {$sectionTitle}.",
                'sort_order' => 1,
                'is_published' => true,
            ],
        );

        $lesson = Lesson::updateOrCreate(
            [
                'course_id' => $course->id,
                'slug' => str($lessonTitle)->slug()->toString(),
            ],
            [
                'course_section_id' => $section->id,
                'title' => $lessonTitle,
                'description' => "Recorded explanation and practice for {$lessonTitle}.",
                'sort_order' => 1,
                'is_free' => false,
                'is_preview' => true,
                'is_published' => true,
                'available_at' => now()->subWeek(),
                'estimated_duration' => 45,
                'completion_percentage_required' => 70,
            ],
        );

        $videoContent = LessonContent::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'type' => LessonContentType::RecordedVideo->value,
                'title' => "{$lessonTitle} Recorded Explanation",
            ],
            [
                'sort_order' => 1,
                'is_required' => true,
                'is_preview' => true,
                'available_at' => now()->subWeek(),
            ],
        );

        $video = Video::updateOrCreate(
            ['lesson_content_id' => $videoContent->id],
            [
                'tenant_id' => $tenant->id,
                'uploaded_by' => $teacher->id,
                'provider' => VideoProvider::BunnyStream->value,
                'provider_video_id' => 'demo-'.$lesson->id,
                'video_url' => 'https://video.example.test/demo/'.$lesson->slug,
                'duration_seconds' => 1800,
                'thumbnail' => "demo/thumbnails/{$lesson->slug}.jpg",
                'processing_status' => VideoProcessingStatus::Ready->value,
                'visibility' => VideoVisibility::Private->value,
            ],
        );

        $videoContent->update([
            'contentable_type' => Video::class,
            'contentable_id' => $video->id,
        ]);

        $pdfContent = LessonContent::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'type' => LessonContentType::Pdf->value,
                'title' => "{$lessonTitle} PDF Summary",
            ],
            [
                'sort_order' => 2,
                'is_required' => true,
                'is_preview' => false,
                'available_at' => now()->subWeek(),
            ],
        );

        $resource = Resource::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'resourceable_type' => LessonContent::class,
                'resourceable_id' => $pdfContent->id,
                'title' => "{$lessonTitle} Worksheet",
            ],
            [
                'uploaded_by' => $teacher->id,
                'file_path' => "demo/resources/{$lesson->slug}-worksheet.pdf",
                'disk' => 'private',
                'mime_type' => 'application/pdf',
                'file_size' => 512000,
                'is_downloadable' => true,
                'external_url' => null,
            ],
        );

        $pdfContent->update([
            'contentable_type' => Resource::class,
            'contentable_id' => $resource->id,
        ]);

        $this->seedAssessment($tenant, $course, $lesson, $teacher);
    }

    private function seedAssessment(Tenant $tenant, Course $course, Lesson $lesson, User $teacher): Assessment
    {
        $assessment = Assessment::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'title' => "{$lesson->title} Quick Quiz",
            ],
            [
                'type' => AssessmentType::Quiz->value,
                'description' => 'Short automatically graded quiz for the recorded lesson.',
                'instructions' => 'Answer all questions after watching the recorded lesson.',
                'duration_minutes' => 20,
                'total_score' => 10,
                'passing_score' => 6,
                'max_attempts' => 2,
                'starts_at' => now()->subWeek(),
                'ends_at' => now()->addMonths(3),
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'show_result_immediately' => true,
                'show_correct_answers' => false,
                'show_explanations' => true,
                'allow_retry' => true,
                'is_published' => true,
            ],
        );

        $question = Question::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'text' => "What is the key idea in {$lesson->title}?",
            ],
            [
                'created_by' => $teacher->id,
                'type' => QuestionType::SingleChoice->value,
                'image' => null,
                'explanation_text' => 'Review the first recorded example and identify the main rule.',
                'explanation_image' => null,
                'difficulty' => QuestionDifficulty::Easy->value,
                'default_score' => 10,
                'topic' => $lesson->title,
                'status' => PublishingStatus::Published->value,
            ],
        );

        $correctOption = QuestionOption::updateOrCreate(
            [
                'question_id' => $question->id,
                'text' => 'Apply the rule step by step and verify the final result.',
            ],
            [
                'image' => null,
                'is_correct' => true,
                'sort_order' => 1,
            ],
        );

        QuestionOption::updateOrCreate(
            [
                'question_id' => $question->id,
                'text' => 'Skip the rule and guess from the answer choices.',
            ],
            [
                'image' => null,
                'is_correct' => false,
                'sort_order' => 2,
            ],
        );

        AssessmentQuestion::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
            ],
            [
                'score' => 10,
                'sort_order' => 1,
            ],
        );

        return $assessment;
    }

    private function seedAssessmentAttempt(Tenant $tenant, Course $course, User $student, Enrollment $enrollment): void
    {
        $assessment = $course->assessments()->first();

        if (! $assessment) {
            return;
        }

        $question = $assessment->assessmentQuestions()->with('question.options')->first()?->question;
        $selectedOption = $question?->options()->where('is_correct', true)->first();

        $attempt = AssessmentAttempt::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'attempt_number' => 1,
            ],
            [
                'tenant_id' => $tenant->id,
                'started_at' => now()->subHours(3),
                'submitted_at' => now()->subHours(2),
                'expires_at' => now()->subHours(2)->addMinutes($assessment->duration_minutes ?? 20),
                'time_spent_seconds' => 900,
                'score' => 10,
                'percentage' => 100,
                'is_passed' => true,
                'status' => AssessmentAttemptStatus::Graded->value,
            ],
        );

        if ($question && $selectedOption) {
            AttemptAnswer::updateOrCreate(
                [
                    'assessment_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer' => [
                        'selected_option_ids' => [$selectedOption->id],
                        'enrollment_id' => $enrollment->id,
                    ],
                    'is_correct' => true,
                    'score' => 10,
                    'feedback' => 'Correct. Good understanding of the recorded lesson.',
                    'graded_at' => now()->subHours(2),
                    'graded_by' => null,
                ],
            );
        }
    }

    private function user(string $name, string $email): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
    }

    private function stage(string $name, string $slug, int $sortOrder): EducationStage
    {
        return EducationStage::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ],
        );
    }

    private function track(string $name, string $slug, int $sortOrder): EducationTrack
    {
        return EducationTrack::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ],
        );
    }

    private function grade(EducationStage $stage, string $name, string $slug, int $sortOrder): Grade
    {
        return Grade::updateOrCreate(
            ['slug' => $slug],
            [
                'education_stage_id' => $stage->id,
                'name' => $name,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ],
        );
    }

    private function subject(string $name, string $slug, string $description): Subject
    {
        return Subject::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => $description,
                'icon' => null,
                'image' => null,
                'is_active' => true,
            ],
        );
    }

    private function gradeSubject(Grade $grade, Subject $subject, ?EducationTrack $track = null): GradeSubject
    {
        return GradeSubject::firstOrCreate(
            [
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
                'education_track_id' => $track?->id,
            ],
            [
                'is_active' => true,
            ],
        );
    }

    private function membership(Tenant $tenant, User $user, TenantRole $role): TenantUser
    {
        return TenantUser::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'role' => $role->value,
            ],
            [
                'status' => MembershipStatus::Active->value,
                'joined_at' => now()->subMonth(),
            ],
        );
    }

    private function offering(Tenant $tenant, GradeSubject $gradeSubject): TenantGradeSubject
    {
        return TenantGradeSubject::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'grade_subject_id' => $gradeSubject->id,
            ],
            [
                'is_active' => true,
            ],
        );
    }

    private function assignment(Tenant $tenant, TenantUser $teacher, TenantGradeSubject $offering): TeacherGradeSubjectAssignment
    {
        return TeacherGradeSubjectAssignment::updateOrCreate(
            [
                'tenant_user_id' => $teacher->id,
                'tenant_grade_subject_id' => $offering->id,
            ],
            [
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ],
        );
    }

    private function academicYear(Tenant $tenant, string $name, bool $current): AcademicYear
    {
        return AcademicYear::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => $name,
            ],
            [
                'starts_at' => '2026-09-15',
                'ends_at' => '2027-06-30',
                'is_current' => $current,
                'status' => AcademicYearStatus::Active->value,
            ],
        );
    }

    private function term(AcademicYear $academicYear, string $name, int $sortOrder, string $startsAt, string $endsAt): Term
    {
        return Term::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'name' => $name,
            ],
            [
                'sort_order' => $sortOrder,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'is_active' => true,
            ],
        );
    }

    private function course(
        Tenant $tenant,
        TeacherGradeSubjectAssignment $assignment,
        AcademicYear $academicYear,
        ?Term $term,
        string $title,
        string $slug,
        float $price,
        bool $featured = false,
    ): Course {
        return Course::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'slug' => $slug,
            ],
            [
                'teacher_grade_subject_assignment_id' => $assignment->id,
                'academic_year_id' => $academicYear->id,
                'term_id' => $term?->id,
                'title' => $title,
                'description' => "Recorded course for {$title}.",
                'learning_outcomes' => [
                    'Understand the main concepts.',
                    'Solve guided exercises.',
                    'Prepare for quizzes and exams.',
                ],
                'thumbnail' => "demo/course-thumbnails/{$slug}.jpg",
                'intro_video' => "https://video.example.test/intro/{$slug}",
                'price' => $price,
                'currency' => Currency::Egp->value,
                'status' => CourseStatus::Published->value,
                'is_featured' => $featured,
                'is_free' => false,
                'published_at' => now()->subWeek(),
                'available_from' => now()->subWeek(),
                'available_until' => now()->addMonths(6),
            ],
        );
    }

    private function studentProfile(Tenant $tenant, User $student, AcademicYear $academicYear, Grade $grade, string $schoolName): StudentAcademicProfile
    {
        return StudentAcademicProfile::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'grade_id' => $grade->id,
                'school_name' => $schoolName,
            ],
        );
    }

    private function plan(Tenant $tenant, string $name, string $slug, PlanType $type, float $price): Plan
    {
        return Plan::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => $name,
            ],
            [
                'type' => $type->value,
                'description' => "Demo {$type->label()} for {$tenant->name}. Reference: {$slug}.",
                'price' => $price,
                'currency' => Currency::Egp->value,
                'duration_type' => DurationType::Months->value,
                'duration_value' => 4,
                'is_active' => true,
            ],
        );
    }

    private function planItem(Plan $plan, Course $course): PlanItem
    {
        return PlanItem::updateOrCreate(
            [
                'plan_id' => $plan->id,
                'item_type' => Course::class,
                'item_id' => $course->id,
            ],
        );
    }
}
