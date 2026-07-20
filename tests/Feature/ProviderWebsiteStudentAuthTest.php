<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\CoursePeriodType;
use App\Enums\LessonTypeEnum;
use App\Enums\PaymentMethodSlugs;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Enums\PurchaseType;
use App\Enums\PurchaseUnitType;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Livewire\Website\AssessmentPage;
use App\Livewire\Website\AttemptResultPage;
use App\Livewire\Website\AuthControls;
use App\Livewire\Website\CartPage;
use App\Livewire\Website\CheckoutPage;
use App\Livewire\Website\HomeCta;
use App\Livewire\Website\HomeSubjects;
use App\Livewire\Website\LessonPage;
use App\Livewire\Website\LoginForm;
use App\Livewire\Website\MyLessonsPage;
use App\Livewire\Website\RegisterForm;
use App\Livewire\Website\SingleTeacherPage;
use App\Livewire\Website\SubjectsPage;
use App\Livewire\Website\TeachersPage;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Assignment;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\CoursePeriod;
use App\Models\CoursePrice;
use App\Models\EducationStage;
use App\Models\Exam;
use App\Models\ExamModel;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\ProviderPaymentMethod;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\ProviderSubscription;
use App\Models\PurchaseUnit;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentAttempt;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProviderWebsiteStudentAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSession([]);
    }

    public function test_login_page_uses_public_template_with_livewire_form(): void
    {
        $provider = $this->provider();

        $response = $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/login');

        $response
            ->assertOk()
            ->assertSeeLivewire(LoginForm::class)
            ->assertSeeLivewire(AuthControls::class)
            ->assertSee('phone-verification-form', false)
            ->assertSee('wire:submit', false)
            ->assertDontSee('action="/login/send-otp"', false)
            ->assertDontSee('http://127.0.0.1:8000/livewire', false)
            ->assertSee('href="/login"', false)
            ->assertSee('href="/my_lessons"', false)
            ->assertSee('دروسي', false)
            ->assertDontSee('id="dropdownNvbarButton"', false)
            ->assertDontSee('href="/teachers"', false)
            ->assertDontSee('href="login.html"', false)
            ->assertDontSee('href="subjects.html"', false);
    }

    public function test_legacy_page_urls_redirect_permanently_to_canonical_urls(): void
    {
        $provider = $this->provider();
        $providerUrl = 'http://'.$provider->subdomain.'.'.config('almanasa.root_domain');

        foreach ([
            '/index.html' => '/',
            '/login.html' => '/login',
            '/subjects.html' => '/subjects',
            '/home_work_done.html' => '/home_work_done',
        ] as $legacyUrl => $canonicalUrl) {
            $this->get($providerUrl.$legacyUrl)
                ->assertRedirect($canonicalUrl)
                ->assertMovedPermanently();
        }
    }

    public function test_protected_auth_pages_redirect_guests_to_canonical_login_url(): void
    {
        $provider = $this->provider();
        $providerUrl = 'http://'.$provider->subdomain.'.'.config('almanasa.root_domain');

        $this->get($providerUrl.'/otp')->assertRedirect('/login');
        $this->get($providerUrl.'/register')->assertRedirect('/login');
        $this->get($providerUrl.'/my_lessons')->assertRedirect('/login');
        $this->get($providerUrl.'/cart')->assertRedirect('/login');
        $this->get($providerUrl.'/checkout')->assertRedirect('/login');
    }

    public function test_provider_branding_banner_and_footer_data_render_on_website(): void
    {
        $provider = $this->provider();
        $provider->update([
            'name' => 'Future Stars Academy',
            'logo' => 'providers/logos/future-stars.png',
            'bio' => ['en' => 'Provider English bio', 'ar' => 'نبذة الأكاديمية من قاعدة البيانات'],
            'facebook_link' => 'https://facebook.example/future-stars',
            'instagram_link' => 'https://instagram.example/future-stars',
        ]);
        Banner::query()->create([
            'provider_id' => $provider->id,
            'title' => ['en' => 'Banner English title', 'ar' => 'عنوان البانر من قاعدة البيانات'],
            'subtitle' => ['en' => 'Banner English subtitle', 'ar' => 'وصف البانر من قاعدة البيانات'],
            'cover' => 'banners/home-hero.png',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('Future Stars Academy', false)
            ->assertSee('storage/providers/logos/future-stars.png', false)
            ->assertSee('عنوان البانر من قاعدة البيانات', false)
            ->assertSee('وصف البانر من قاعدة البيانات', false)
            ->assertSee('storage/banners/home-hero.png', false)
            ->assertSee('نبذة الأكاديمية من قاعدة البيانات', false)
            ->assertSee('https://facebook.example/future-stars', false)
            ->assertSee('href="/my_lessons"', false);
    }

    public function test_new_phone_creates_user_and_provider_student_account_after_valid_otp(): void
    {
        $provider = $this->provider();

        Livewire::test(LoginForm::class, ['providerId' => $provider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01012345678')
            ->call('sendOtp')
            ->set('otp', '1234')
            ->call('verify')
            ->assertRedirect('/register');

        $user = User::query()->where('phone', '01012345678')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->verified_at);
        $this->assertDatabaseHas(Account::class, [
            'provider_id' => $provider->id,
            'owner_user_id' => $user->id,
            'type' => AccountType::Student->value,
            'is_active' => true,
        ]);
        $this->assertSame($provider->id, session('current_provider_id'));
    }

    public function test_otp_and_register_pages_render_livewire_components(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $this->withSession([
            LoginForm::challengeKeyFor($provider->id) => [
                'provider_id' => $provider->id,
                'dial_country_code' => '+20',
                'phone' => '01012345678',
                'code_hash' => bcrypt('1234'),
                'expires_at' => now()->addMinutes(5)->timestamp,
            ],
        ])->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/otp')
            ->assertOk()
            ->assertSeeLivewire(LoginForm::class)
            ->assertSee('otp-verification-form', false)
            ->assertSee('wire:submit', false)
            ->assertSee('submitIfComplete', false)
            ->assertDontSee('action="/otp/verify"', false);

        $this->actingAs($user)->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/register')
            ->assertOk()
            ->assertSeeLivewire(RegisterForm::class)
            ->assertSeeLivewire(AuthControls::class)
            ->assertSee('profile-completion-form', false)
            ->assertSee('wire:submit', false)
            ->assertDontSee('action="/register"', false);
    }

    public function test_removed_plain_html_auth_post_routes_do_not_handle_auth(): void
    {
        $provider = $this->provider();
        $providerUrl = 'http://'.$provider->subdomain.'.'.config('almanasa.root_domain');

        $this->post($providerUrl.'/login/send-otp')->assertStatus(405);
        $this->post($providerUrl.'/otp/verify')->assertStatus(405);
        $this->post($providerUrl.'/register')->assertStatus(405);

        $this->assertGuest();
        $this->assertDatabaseMissing(Account::class, [
            'provider_id' => $provider->id,
            'type' => AccountType::Student->value,
        ]);
    }

    public function test_complete_profile_page_only_shows_logout_until_profile_is_completed(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/register')
            ->assertOk()
            ->assertSee('تسجيل الخروج', false)
            ->assertSeeLivewire(AuthControls::class)
            ->assertSee('wire:click="logout"', false)
            ->assertDontSee('action="/logout"', false)
            ->assertDontSee('href="/profile"', false)
            ->assertDontSee('href="/cart"', false)
            ->assertSee('id="openSidebarBtn"', false)
            ->assertSee('id="mobileSidebar"', false);
    }

    public function test_incomplete_profile_student_sees_home_as_guest(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('تسجيل الدخول', false)
            ->assertDontSee('تسجيل الخروج', false)
            ->assertDontSee('href="/profile"', false)
            ->assertDontSee('href="/cart"', false);
    }

    public function test_completed_profile_student_sees_profile_and_cart_icons(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);
        $this->studentProfile($user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('تسجيل الخروج', false)
            ->assertSee('href="/profile"', false)
            ->assertSee('href="/cart"', false)
            ->assertDontSee('>الملف الشخصي<', false);
    }

    public function test_header_cart_icon_displays_current_cart_items_count(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'General', 'ar' => 'عام'], 'code' => 'general']);
        $subject = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Arabic', 'ar' => 'اللغة العربية']]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);
        $purchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Month->value,
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $courses = collect([
            Course::query()->create([
                'provider_id' => $provider->id,
                'account_subject_id' => $accountSubject->id,
                'title' => ['en' => 'First Course', 'ar' => 'الكورس الأول'],
            ]),
            Course::query()->create([
                'provider_id' => $provider->id,
                'account_subject_id' => $accountSubject->id,
                'title' => ['en' => 'Second Course', 'ar' => 'الكورس الثاني'],
            ]),
        ]);
        $cart = Cart::query()->create([
            'student_user_id' => $user->id,
            'provider_id' => $provider->id,
            'purchase_type' => PurchaseType::SingleCourse->value,
        ]);

        $courses->each(function (Course $course) use ($cart, $purchaseUnit): void {
            $coursePrice = CoursePrice::query()->create([
                'course_id' => $course->id,
                'purchase_unit_id' => $purchaseUnit->id,
                'price' => 100,
            ]);

            CartItem::query()->create([
                'cart_id' => $cart->id,
                'course_id' => $course->id,
                'course_price_id' => $coursePrice->id,
                'purchase_unit_id' => $purchaseUnit->id,
                'purchase_type' => PurchaseType::SingleCourse->value,
                'title' => $course->title,
                'unit_price' => 100,
                'total' => 100,
            ]);
        });

        $component = Livewire::actingAs($user)
            ->test(AuthControls::class, ['providerId' => $provider->id])
            ->assertSee('aria-label="السلة (2)"', false)
            ->assertSee('data-testid="cart-items-count"', false)
            ->assertSee('2', false);

        $cart->items()->firstOrFail()->delete();

        $component
            ->dispatch('cart-updated')
            ->assertSee('aria-label="السلة (1)"', false)
            ->assertSee('1', false);
    }

    public function test_home_hero_actions_follow_auth_state(): void
    {
        $provider = $this->provider();

        $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('href="/login"', false)
            ->assertSee('ابدأ رحلتك الآن', false)
            ->assertSee('استكشف المواد', false);

        $user = User::factory()->create();
        $this->studentAccount($provider, $user);
        $this->studentProfile($user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('href="/subjects"', false)
            ->assertSee('استكشف المواد', false)
            ->assertDontSee('ابدأ رحلتك الآن', false);
    }

    public function test_standalone_teacher_home_explore_button_links_to_single_teacher(): void
    {
        $provider = $this->provider('mona-physics', ProviderType::StandaloneTeacher);
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);
        $this->studentProfile($user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSee('href="/single_teacher"', false)
            ->assertSee('استكشف المواد', false);
    }

    public function test_home_subjects_are_filtered_by_student_grade_and_provider(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $studentGrade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $otherGrade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 2', 'sort_order' => 2]);
        $this->studentProfile($user, $studentGrade);

        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $math = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات']]);
        $physics = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Physics', 'ar' => 'الفيزياء']]);
        $chemistry = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Chemistry', 'ar' => 'الكيمياء']]);

        foreach ([$math, $physics] as $subject) {
            AccountSubject::query()->create([
                'provider_id' => $provider->id,
                'grade_subject_id' => GradeSubject::query()->create([
                    'grade_id' => $studentGrade->id,
                    'subject_id' => $subject->id,
                ])->id,
                'is_active' => true,
            ]);
        }

        AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $otherGrade->id,
                'subject_id' => $chemistry->id,
            ])->id,
            'is_active' => true,
        ]);

        $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertDontSee('href="/teachers?subject=', false)
            ->assertDontSee('الرياضيات', false)
            ->assertDontSee('الفيزياء', false)
            ->assertDontSee('المزيد', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSeeLivewire(HomeSubjects::class)
            ->assertSee('href="/teachers?subject=', false)
            ->assertSee('الرياضيات', false)
            ->assertSee('الفيزياء', false)
            ->assertDontSee('fa-flask-vial', false);
    }

    public function test_subjects_page_searches_subjects_in_student_grade(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $studentGrade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $otherGrade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 2', 'sort_order' => 2]);
        $this->studentProfile($user, $studentGrade);

        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $math = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
            'description' => ['en' => 'Math description', 'ar' => 'شرح الرياضيات'],
        ]);
        $physics = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
            'description' => ['en' => 'Physics description', 'ar' => 'شرح الفيزياء'],
        ]);
        $chemistry = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Chemistry', 'ar' => 'الكيمياء'],
            'description' => ['en' => 'Chemistry description', 'ar' => 'شرح الكيمياء'],
        ]);

        foreach ([$math, $physics] as $subject) {
            AccountSubject::query()->create([
                'provider_id' => $provider->id,
                'grade_subject_id' => GradeSubject::query()->create([
                    'grade_id' => $studentGrade->id,
                    'subject_id' => $subject->id,
                ])->id,
                'is_active' => true,
            ]);
        }

        AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $otherGrade->id,
                'subject_id' => $chemistry->id,
            ])->id,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/subjects')
            ->assertOk()
            ->assertSeeLivewire(SubjectsPage::class)
            ->assertSee('wire:model.live.debounce.300ms="search"', false)
            ->assertSee('href="/teachers?subject=', false)
            ->assertSee('Grade 1', false)
            ->assertSee('الرياضيات', false)
            ->assertSee('الفيزياء', false)
            ->assertDontSee('الكيمياء', false)
            ->assertDontSee('فلترة', false);

        Livewire::actingAs($user)
            ->test(SubjectsPage::class, ['providerId' => $provider->id])
            ->assertSee('الرياضيات', false)
            ->assertSee('الفيزياء', false)
            ->set('search', 'رياض')
            ->assertSee('الرياضيات', false)
            ->assertDontSee('الفيزياء', false)
            ->assertDontSee('الكيمياء', false);
    }

    public function test_teachers_page_shows_teachers_for_selected_subject(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $studentGrade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $studentGrade);

        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $math = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
            'description' => ['en' => 'Math description', 'ar' => 'شرح الرياضيات'],
        ]);
        $physics = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
            'description' => ['en' => 'Physics description', 'ar' => 'شرح الفيزياء'],
        ]);

        $mathAccountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $studentGrade->id,
                'subject_id' => $math->id,
            ])->id,
            'is_active' => true,
        ]);
        $physicsAccountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $studentGrade->id,
                'subject_id' => $physics->id,
            ])->id,
            'is_active' => true,
        ]);

        $mathTeacherUser = User::factory()->create(['first_name' => 'Math', 'last_name' => 'Teacher']);
        $physicsTeacherUser = User::factory()->create(['first_name' => 'Physics', 'last_name' => 'Teacher']);
        $mathTeacherAccount = $this->teacherAccount($provider, $mathTeacherUser);
        $physicsTeacherAccount = $this->teacherAccount($provider, $physicsTeacherUser);
        $mathTeacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $mathTeacherAccount->id,
            'experience_years' => 7,
            'is_active' => true,
        ]);
        $physicsTeacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $physicsTeacherAccount->id,
            'experience_years' => 5,
            'is_active' => true,
        ]);

        AcademyTeacherGradeSubject::query()->create([
            'academy_teacher_id' => $mathTeacher->id,
            'account_subject_id' => $mathAccountSubject->id,
            'is_active' => true,
        ]);
        AcademyTeacherGradeSubject::query()->create([
            'academy_teacher_id' => $physicsTeacher->id,
            'account_subject_id' => $physicsAccountSubject->id,
            'is_active' => true,
        ]);

        $monthPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Month->value,
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $mathCourse = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $mathAccountSubject->id,
            'academy_teacher_id' => $mathTeacher->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
            'weekly_lectures_count' => 2,
        ]);

        CoursePrice::query()->create([
            'course_id' => $mathCourse->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'price' => 200,
            'offer_price' => 180,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/teachers?subject='.$mathAccountSubject->id)
            ->assertOk()
            ->assertSeeLivewire(TeachersPage::class)
            ->assertSee('الرياضيات', false)
            ->assertSee('Math Teacher', false)
            ->assertSee('180 EGP', false)
            ->assertDontSee('Physics Teacher', false)
            ->assertDontSee('الفيزياء', false);
    }

    public function test_single_teacher_page_uses_course_lessons_and_hides_final_reviews_tab(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
            'description' => ['en' => 'Math description', 'ar' => 'شرح الرياضيات'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Teacher']);
        $teacherAccount = $this->teacherAccount($provider, $teacherUser);
        $teacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'experience_years' => 7,
            'is_active' => true,
        ]);
        AcademyTeacherGradeSubject::query()->create([
            'academy_teacher_id' => $teacher->id,
            'account_subject_id' => $accountSubject->id,
            'is_active' => true,
        ]);

        $monthPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Month->value,
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $termOnePeriod = CoursePeriod::query()->create([
            'type' => CoursePeriodType::Term1->value,
            'name' => ['en' => 'Term 1', 'ar' => 'الترم الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
            'description' => ['en' => 'Course description', 'ar' => 'وصف الكورس'],
            'weekly_lectures_count' => 2,
            'num_of_lessons' => 10,
            'num_of_hours' => 12,
        ]);
        CoursePrice::query()->create([
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'price' => 200,
            'offer_price' => 180,
        ]);
        CourseOutcome::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Understand equations', 'ar' => 'فهم المعادلات'],
            'sort_order' => 1,
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $termOnePeriod->id,
            'title' => ['en' => 'Real Numbers', 'ar' => 'الأعداد الحقيقية'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Introduction', 'ar' => 'مقدمة في الأعداد الحقيقية'],
            'video_url' => 'https://videos.example.test/introduction',
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => true,
        ]);
        $inactiveItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Inactive Video', 'ar' => 'فيديو غير مفعل'],
            'video_url' => 'https://videos.example.test/inactive',
            'sort_order' => 2,
            'is_active' => false,
            'is_free' => true,
        ]);
        $futureActiveLessonItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Future Active Lesson Video', 'ar' => 'فيديو مستقبلي داخل حصة مفتوحة'],
            'video_url' => 'https://videos.example.test/future-active-lesson',
            'starts_at' => now()->addDay(),
            'sort_order' => 3,
            'is_active' => true,
            'is_free' => true,
        ]);
        $futureLesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $termOnePeriod->id,
            'title' => ['en' => 'Future Lesson', 'ar' => 'حصة مستقبلية'],
            'starts_at' => now()->addDay(),
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $futureItem = LessonItem::query()->create([
            'lesson_id' => $futureLesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Future Video', 'ar' => 'فيديو مستقبلي'],
            'video_url' => 'https://videos.example.test/future',
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => true,
        ]);
        $expiredLesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $termOnePeriod->id,
            'title' => ['en' => 'Expired Lesson', 'ar' => 'حصة منتهية'],
            'ends_at' => now()->subMinute(),
            'sort_order' => 3,
            'is_active' => true,
        ]);
        $expiredItem = LessonItem::query()->create([
            'lesson_id' => $expiredLesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Expired Video', 'ar' => 'فيديو منتهي'],
            'video_url' => 'https://videos.example.test/expired',
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => true,
        ]);
        $expiredExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Expired Exam', 'ar' => 'اختبار منتهي'],
            'duration_minutes' => 15,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        $expiredExamItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'exam_id' => $expiredExam->id,
            'type' => LessonTypeEnum::Exams->value,
            'title' => ['en' => 'Expired Exam Item', 'ar' => 'اختبار منتهي'],
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'sort_order' => 4,
            'is_active' => true,
            'is_free' => true,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/single_teacher?teacher='.$teacher->id.'&subject='.$accountSubject->id)
            ->assertOk()
            ->assertSeeLivewire(SingleTeacherPage::class)
            ->assertSee('كورس الرياضيات', false)
            ->assertSee('الأعداد الحقيقية', false)
            ->assertSee('مقدمة في الأعداد الحقيقية', false)
            ->assertSee('180', false)
            ->assertSee('فيديو غير مفعل', false)
            ->assertSee('غير مفعل حالياً', false)
            ->assertSee('فيديو مستقبلي داخل حصة مفتوحة', false)
            ->assertSee('حصة مستقبلية', false)
            ->assertSee('فيديو مستقبلي', false)
            ->assertSee('حصة منتهية', false)
            ->assertSee('فيديو منتهي', false)
            ->assertSee('اختبار منتهي', false)
            ->assertSee('انتهى في', false)
            ->assertSee('غير متاح الآن', false)
            ->assertDontSee('href="/lesson?item='.$inactiveItem->id.'"', false)
            ->assertDontSee('href="/lesson?item='.$futureActiveLessonItem->id.'"', false)
            ->assertDontSee('href="/lesson?item='.$futureItem->id.'"', false)
            ->assertDontSee('href="/lesson?item='.$expiredItem->id.'"', false)
            ->assertDontSee('href="/lesson?item='.$expiredExamItem->id.'"', false)
            ->assertDontSee('المراجعات النهائية', false);
    }

    public function test_active_course_subscription_unlocks_paid_lesson_items(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'General', 'ar' => 'عام'], 'code' => 'general']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Arabic', 'ar' => 'اللغة العربية'],
            'is_active' => true,
        ]);
        $gradeSubject = GradeSubject::query()->create([
            'grade_id' => $grade->id,
            'subject_id' => $subject->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => $gradeSubject->id,
            'is_active' => true,
        ]);
        $teacherUser = User::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Teacher']);
        $teacherAccount = $this->teacherAccount($provider, $teacherUser);
        $teacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Arabic Course', 'ar' => 'كورس اللغة العربية'],
        ]);
        $purchaseUnit = PurchaseUnit::query()->create([
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'type' => PurchaseUnitType::Month,
            'duration_days' => 30,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $period = CoursePeriod::query()->create([
            'type' => CoursePeriodType::Term1->value,
            'name' => ['en' => 'Term 1', 'ar' => 'الترم الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $period->id,
            'title' => ['en' => 'Grammar', 'ar' => 'النحو'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $paidItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Paid Explanation', 'ar' => 'شرح مدفوع'],
            'video_url' => 'https://videos.example.test/paid-arabic',
            'duration_minutes' => 20,
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => false,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/single_teacher?teacher='.$teacher->id.'&subject='.$accountSubject->id)
            ->assertOk()
            ->assertSee('شرح مدفوع', false)
            ->assertDontSee('href="/lesson?item='.$paidItem->id.'"', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$paidItem->id)
            ->assertOk()
            ->assertSee('هذا العنصر متاح للمشتركين في الكورس فقط.', false)
            ->assertDontSee('https://videos.example.test/paid-arabic', false);

        Subscription::query()->create([
            'student_user_id' => $user->id,
            'provider_id' => $provider->id,
            'course_id' => $course->id,
            'purchase_unit_id' => $purchaseUnit->id,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/single_teacher?teacher='.$teacher->id.'&subject='.$accountSubject->id)
            ->assertOk()
            ->assertSee('href="/lesson?item='.$paidItem->id.'"', false)
            ->assertSee('مشترك', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$paidItem->id)
            ->assertOk()
            ->assertSee('https://videos.example.test/paid-arabic', false)
            ->assertDontSee('هذا العنصر متاح للمشتركين في الكورس فقط.', false);
    }

    public function test_my_lessons_displays_student_subscribed_courses_with_subscription_status(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Student']);
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'General', 'ar' => 'عام'], 'code' => 'general']);
        $arabic = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Arabic', 'ar' => 'اللغة العربية']]);
        $physics = Subject::query()->create(['track_id' => $track->id, 'name' => ['en' => 'Physics', 'ar' => 'الفيزياء']]);
        $monthPurchaseUnit = PurchaseUnit::query()->create([
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'type' => PurchaseUnitType::Month,
            'period_days' => 30,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $teacherUser = User::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Teacher']);
        $teacherAccount = $this->teacherAccount($provider, $teacherUser);
        $teacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'is_active' => true,
        ]);

        $activeAccountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $arabic->id,
            ])->id,
            'is_active' => true,
        ]);
        $expiredAccountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $physics->id,
            ])->id,
            'is_active' => true,
        ]);
        $activeCourse = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $activeAccountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Arabic Course', 'ar' => 'كورس اللغة العربية'],
        ]);
        $expiredCourse = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $expiredAccountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Physics Course', 'ar' => 'كورس الفيزياء'],
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $activeCourse->id,
            'title' => ['en' => 'First Lesson', 'ar' => 'الدرس الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $lessonItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Intro', 'ar' => 'مقدمة'],
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => false,
        ]);

        Subscription::query()->create([
            'student_user_id' => $user->id,
            'provider_id' => $provider->id,
            'course_id' => $activeCourse->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);
        Subscription::query()->create([
            'student_user_id' => $user->id,
            'provider_id' => $provider->id,
            'course_id' => $expiredCourse->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/my_lessons')
            ->assertOk()
            ->assertSeeLivewire(MyLessonsPage::class)
            ->assertSee('اللغة العربية', false)
            ->assertSee('الفيزياء', false)
            ->assertSee('نشط', false)
            ->assertSee('غير نشط', false)
            ->assertSee('href="/lesson?item='.$lessonItem->id.'"', false)
            ->assertSee('href="/checkout?course='.$expiredCourse->id.'"', false);
    }

    public function test_cart_adds_course_and_uses_purchase_unit_prices_without_offer_price(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
        ]);
        $monthPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Month->value,
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $termPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Term->value,
            'name' => ['en' => 'Term', 'ar' => 'ترم'],
            'sort_order' => 2,
            'is_active' => true,
        ]);
        CoursePrice::query()->create([
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'price' => 200,
            'offer_price' => 150,
        ]);
        CoursePrice::query()->create([
            'course_id' => $course->id,
            'purchase_unit_id' => $termPurchaseUnit->id,
            'price' => 600,
            'offer_price' => 450,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/cart?course='.$course->id)
            ->assertOk()
            ->assertSeeLivewire(CartPage::class)
            ->assertSee('كورس الرياضيات', false)
            ->assertSee('شهر', false)
            ->assertSee('ترم', false)
            ->assertSee('200 ج.م', false)
            ->assertDontSee('150 ج.م', false);

        $cart = Cart::query()
            ->whereBelongsTo($provider)
            ->whereBelongsTo($user, 'student')
            ->firstOrFail();

        $this->assertDatabaseHas(CartItem::class, [
            'cart_id' => $cart->id,
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'unit_price' => 200,
            'total' => 200,
        ]);

        Livewire::actingAs($user)
            ->test(CartPage::class, ['providerId' => $provider->id])
            ->call('selectPurchaseUnit', $termPurchaseUnit->id)
            ->assertSee('600 ج.م', false)
            ->assertDontSee('450 ج.م', false);

        $this->assertDatabaseHas(CartItem::class, [
            'cart_id' => $cart->id,
            'course_id' => $course->id,
            'purchase_unit_id' => $termPurchaseUnit->id,
            'unit_price' => 600,
            'total' => 600,
        ]);

        $cartItem = CartItem::query()
            ->whereBelongsTo($cart)
            ->whereBelongsTo($course)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(CartPage::class, ['providerId' => $provider->id])
            ->call('removeItem', $cartItem->id)
            ->assertDispatched('cart-updated');
    }

    public function test_checkout_uses_provider_payment_methods_without_billing_or_duration_sections(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
        ]);
        $monthPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Month->value,
            'name' => ['en' => 'Month', 'ar' => 'شهر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $termPurchaseUnit = PurchaseUnit::query()->create([
            'type' => PurchaseUnitType::Term->value,
            'name' => ['en' => 'Term', 'ar' => 'ترم'],
            'sort_order' => 2,
            'is_active' => true,
        ]);
        CoursePrice::query()->create([
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'price' => 200,
            'offer_price' => 150,
        ]);
        CoursePrice::query()->create([
            'course_id' => $course->id,
            'purchase_unit_id' => $termPurchaseUnit->id,
            'price' => 600,
            'offer_price' => 450,
        ]);
        $instaPay = PaymentMethod::query()->create([
            'slug' => PaymentMethodSlugs::InstaPay->value,
            'name' => ['en' => 'InstaPay', 'ar' => 'إنستا باي'],
            'sort_order' => 1,
            'is_active' => true,
            'require_proof' => true,
        ]);
        $code = PaymentMethod::query()->create([
            'slug' => PaymentMethodSlugs::Code->value,
            'name' => ['en' => 'Code', 'ar' => 'كود'],
            'sort_order' => 2,
            'is_active' => true,
            'is_code' => true,
        ]);
        $inactiveMethod = PaymentMethod::query()->create([
            'slug' => PaymentMethodSlugs::Bank->value,
            'name' => ['en' => 'Bank Transfer', 'ar' => 'تحويل بنكي'],
            'sort_order' => 3,
            'is_active' => false,
        ]);
        $providerPaymentMethod = ProviderPaymentMethod::query()->create([
            'provider_id' => $provider->id,
            'payment_method_id' => $instaPay->id,
            'phone_number' => '01000000001',
            'phone_holder' => 'Future Stars',
        ]);
        ProviderPaymentMethod::query()->create([
            'provider_id' => $provider->id,
            'payment_method_id' => $code->id,
            'account_number' => 'CODE-123',
            'account_holder' => 'Future Stars',
        ]);
        ProviderPaymentMethod::query()->create([
            'provider_id' => $provider->id,
            'payment_method_id' => $inactiveMethod->id,
            'account_number' => 'BANK-123',
            'account_holder' => 'Future Stars',
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/checkout?course='.$course->id)
            ->assertOk()
            ->assertSeeLivewire(CheckoutPage::class)
            ->assertSee('نوع الاشتراك', false)
            ->assertSee('شهر', false)
            ->assertSee('ترم', false)
            ->assertSee('اختر وسيلة الدفع', false)
            ->assertSee('إنستا باي', false)
            ->assertSee('كود', false)
            ->assertSee('01000000001', false)
            ->assertSee('كورس الرياضيات', false)
            ->assertSee('200.00 ج.م', false)
            ->assertDontSee('معلومات الفوترة', false)
            ->assertDontSee('مدة الاشتراك', false)
            ->assertDontSee('تحويل بنكي', false)
            ->assertDontSee('150.00 ج.م', false);

        Storage::fake('public');

        Livewire::actingAs($user)
            ->test(CheckoutPage::class, ['providerId' => $provider->id])
            ->call('selectPurchaseUnit', $termPurchaseUnit->id)
            ->assertSee('600.00 ج.م', false)
            ->assertDontSee('450.00 ج.م', false)
            ->set('selectedProviderPaymentMethodId', $providerPaymentMethod->id)
            ->set('transferImage', UploadedFile::fake()->image('receipt.jpg'))
            ->set('transactionReference', 'TX-123')
            ->call('submitOrder')
            ->assertHasNoErrors()
            ->assertSee('تم إرسال الطلب', false)
            ->assertSee('في انتظار موافقة الإدارة', false);

        $order = Order::query()
            ->whereBelongsTo($provider)
            ->whereBelongsTo($user, 'student')
            ->firstOrFail();

        $this->assertDatabaseHas(Payment::class, [
            'order_id' => $order->id,
            'provider_payment_method_id' => $providerPaymentMethod->id,
            'transaction_reference' => 'TX-123',
            'is_paid' => false,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'course_id' => $course->id,
            'purchase_unit_id' => $termPurchaseUnit->id,
            'unit_price' => 600,
            'total' => 600,
        ]);

        $this->assertDatabaseHas('order_status_types', [
            'slug' => 'pending',
        ]);

        $payment = Payment::query()->whereBelongsTo($order)->firstOrFail();
        $this->assertNotNull($payment->transfer_image);
        Storage::disk('public')->assertExists($payment->transfer_image);
    }

    public function test_lesson_page_uses_selected_lesson_item_content(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Teacher']);
        $teacherAccount = $this->teacherAccount($provider, $teacherUser);
        $teacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'experience_years' => 7,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
        ]);
        $period = CoursePeriod::query()->create([
            'type' => CoursePeriodType::Term1->value,
            'name' => ['en' => 'Term 1', 'ar' => 'الترم الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $period->id,
            'title' => ['en' => 'Algebra Basics', 'ar' => 'أساسيات الجبر'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $firstItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'First Explanation', 'ar' => 'الشرح الأول من قاعدة البيانات'],
            'video_url' => 'https://videos.example.test/database-video',
            'duration_minutes' => 30,
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => true,
        ]);
        LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Second Explanation', 'ar' => 'الشرح الثاني من قاعدة البيانات'],
            'video_url' => 'https://videos.example.test/database-video-two',
            'duration_minutes' => 25,
            'sort_order' => 2,
            'is_active' => true,
            'is_free' => false,
        ]);
        $assignment = Assignment::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Algebra Homework', 'ar' => 'واجب الجبر'],
            'duration_minutes' => 30,
            'num_of_attempts' => 2,
            'question_ids' => [],
        ]);
        $assignmentItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'assignment_id' => $assignment->id,
            'type' => LessonTypeEnum::Assignments->value,
            'title' => ['en' => 'Homework Item', 'ar' => 'عنصر واجب الجبر'],
            'duration_minutes' => 30,
            'sort_order' => 3,
            'is_active' => true,
            'is_free' => true,
        ]);
        $inactiveItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Inactive Lesson Item', 'ar' => 'عنصر غير مفعل'],
            'video_url' => 'https://videos.example.test/inactive-video',
            'duration_minutes' => 15,
            'sort_order' => 4,
            'is_active' => false,
            'is_free' => true,
        ]);
        $futureActiveLessonItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Future Lesson Item', 'ar' => 'عنصر مستقبلي داخل الدرس'],
            'video_url' => 'https://videos.example.test/future-item-video',
            'duration_minutes' => 20,
            'starts_at' => now()->addDay(),
            'sort_order' => 5,
            'is_active' => true,
            'is_free' => true,
        ]);
        $futureLesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $period->id,
            'title' => ['en' => 'Future Algebra', 'ar' => 'جبر مستقبلي'],
            'starts_at' => now()->addDay(),
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $futureItem = LessonItem::query()->create([
            'lesson_id' => $futureLesson->id,
            'type' => LessonTypeEnum::Video->value,
            'title' => ['en' => 'Unavailable Video', 'ar' => 'فيديو غير متاح الآن'],
            'video_url' => 'https://videos.example.test/unavailable',
            'duration_minutes' => 20,
            'sort_order' => 1,
            'is_active' => true,
            'is_free' => true,
        ]);
        $expiredExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Expired Exam', 'ar' => 'اختبار منتهي'],
            'duration_minutes' => 20,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        $expiredExamItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'exam_id' => $expiredExam->id,
            'type' => LessonTypeEnum::Exams->value,
            'title' => ['en' => 'Expired Exam Item', 'ar' => 'عنصر اختبار منتهي'],
            'duration_minutes' => 20,
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'sort_order' => 6,
            'is_active' => true,
            'is_free' => true,
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$firstItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('أساسيات الجبر', false)
            ->assertSee('الشرح الأول من قاعدة البيانات', false)
            ->assertSee('الشرح الثاني من قاعدة البيانات', false)
            ->assertSee('https://videos.example.test/database-video', false)
            ->assertDontSee('الشرح الأول: المفاهيم الأساسية', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$assignmentItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('عنصر واجب الجبر', false)
            ->assertSee('عدد المحاولات: 0 / 2', false)
            ->assertSee('متبقي 2', false)
            ->assertSee('href="/home_work?assignment='.$assignment->id.'&item='.$assignmentItem->id.'"', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$inactiveItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('عنصر غير مفعل', false)
            ->assertSee('غير مفعل حالياً', false)
            ->assertSee('العنصر ظاهر في قائمة الدروس', false)
            ->assertDontSee('https://videos.example.test/inactive-video', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$futureActiveLessonItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('عنصر مستقبلي داخل الدرس', false)
            ->assertSee('يفتح في', false)
            ->assertSee('العنصر ظاهر في قائمة الدروس', false)
            ->assertDontSee('https://videos.example.test/future-item-video', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$futureItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('فيديو غير متاح الآن', false)
            ->assertSee('هذا الدرس سيفتح في', false)
            ->assertSee('العنصر ظاهر في قائمة الدروس', false)
            ->assertDontSee('https://videos.example.test/unavailable', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/lesson?item='.$expiredExamItem->id)
            ->assertOk()
            ->assertSeeLivewire(LessonPage::class)
            ->assertSee('اختبار منتهي', false)
            ->assertSee('انتهى في', false)
            ->assertDontSee('href="/quiz?exam='.$expiredExam->id.'"', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$expiredExam->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('الاختبار مغلق حالياً', false)
            ->assertDontSee('إنهاء الاختبار', false);
    }

    public function test_student_can_submit_assignment_and_exam_attempts_from_website(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($user, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);
        $teacherUser = User::factory()->create(['first_name' => 'Mona', 'last_name' => 'Teacher']);
        $teacherAccount = $this->teacherAccount($provider, $teacherUser);
        $teacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $teacher->id,
            'title' => ['en' => 'Physics Course', 'ar' => 'كورس الفيزياء'],
        ]);
        $period = CoursePeriod::query()->create([
            'type' => CoursePeriodType::Term1->value,
            'name' => ['en' => 'Term 1', 'ar' => 'الترم الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $period->id,
            'title' => ['en' => 'Motion', 'ar' => 'الحركة'],
            'is_active' => true,
        ]);
        $mcqQuestion = Question::query()->create([
            'lesson_id' => $lesson->id,
            'title' => 'What is velocity?',
            'type' => QuestionType::Mcq->value,
            'difficulty' => QuestionDifficulty::Easy->value,
            'sort_order' => 1,
        ]);
        $correctOption = QuestionOption::query()->create([
            'question_id' => $mcqQuestion->id,
            'title' => 'Displacement over time',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        QuestionOption::query()->create([
            'question_id' => $mcqQuestion->id,
            'title' => 'Mass over time',
            'is_correct' => false,
            'sort_order' => 2,
        ]);
        $statementQuestion = Question::query()->create([
            'lesson_id' => $lesson->id,
            'title' => 'Explain acceleration.',
            'type' => QuestionType::Statement->value,
            'difficulty' => QuestionDifficulty::Medium->value,
            'sort_order' => 2,
        ]);

        $assignment = Assignment::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Motion Homework', 'ar' => 'واجب الحركة'],
            'description' => ['en' => 'Answer these questions.', 'ar' => 'أجب عن هذه الأسئلة.'],
            'duration_minutes' => 30,
            'question_ids' => [$mcqQuestion->id, $statementQuestion->id],
        ]);
        $exam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Motion Quiz', 'ar' => 'اختبار الحركة'],
            'duration_minutes' => 20,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        $examModel = ExamModel::query()->create([
            'exam_id' => $exam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $mcqQuestion->id, 'max_score' => 10],
            ],
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/home_work?assignment='.$assignment->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('What is velocity?', false)
            ->assertSee('الوقت المتبقي', false)
            ->assertSee('التقدم: 0 / 2 سؤال', false)
            ->assertSee('السؤال 1', false)
            ->assertSee('إنهاء الواجب', false);

        $startedAssignmentAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $assignment->id)
            ->firstOrFail();

        $this->assertSame('in_progress', $startedAssignmentAttempt->currentStatus?->type?->slug);
        $this->assertCount(2, $startedAssignmentAttempt->studentAnswers);
        $this->assertNull($startedAssignmentAttempt->studentAnswers->first()?->question_option_id);
        $this->assertNull($startedAssignmentAttempt->studentAnswers->first()?->answer_text);
        $this->assertNull($startedAssignmentAttempt->studentAnswers->first()?->score);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$exam->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('What is velocity?', false)
            ->assertSee('تنبيه: في حال الخروج من الصفحة قبل التسليم', false)
            ->assertSee('إنهاء الاختبار', false);

        $startedExamAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $exam->id)
            ->firstOrFail();

        $this->assertSame('in_progress', $startedExamAttempt->currentStatus?->type?->slug);
        $this->assertSame(10.0, (float) $startedExamAttempt->max_score);
        $this->assertCount(1, $startedExamAttempt->studentAnswers);
        $this->assertNull($startedExamAttempt->studentAnswers->first()?->question_option_id);
        $this->assertNull($startedExamAttempt->studentAnswers->first()?->answer_text);
        $this->assertNull($startedExamAttempt->studentAnswers->first()?->score);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'assignment'])
            ->set('assignmentId', $assignment->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
                $statementQuestion->id => 'Acceleration is velocity change over time.',
            ])
            ->call('submit');

        $assignmentAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $assignment->id)
            ->firstOrFail();

        $this->assertSame('submitted', $assignmentAttempt->currentStatus?->type?->slug);
        $this->assertCount(2, $assignmentAttempt->studentAnswers);
        $this->assertTrue((bool) $assignmentAttempt->studentAnswers->firstWhere('question_id', $mcqQuestion->id)?->is_correct);
        $this->assertNull($assignmentAttempt->studentAnswers->firstWhere('question_id', $statementQuestion->id)?->score);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/home_work_done?attempt='.$assignmentAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('في انتظار التصحيح اليدوي', false)
            ->assertSee('تم تسليم الواجب', false)
            ->assertSee('/home_work_done?attempt='.$assignmentAttempt->id.'&amp;review=1', false)
            ->assertDontSee('Acceleration is velocity change over time.', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/home_work_done?attempt='.$assignmentAttempt->id.'&review=1')
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('العودة للمادة', false)
            ->assertSee('Acceleration is velocity change over time.', false);

        $twoAttemptAssignment = Assignment::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Two Attempt Homework', 'ar' => 'واجب بمحاولتين'],
            'duration_minutes' => 30,
            'num_of_attempts' => 2,
            'question_ids' => [$mcqQuestion->id],
        ]);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'assignment'])
            ->set('assignmentId', $twoAttemptAssignment->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
            ])
            ->call('submit');

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/home_work?assignment='.$twoAttemptAssignment->id)
            ->assertOk()
            ->assertSee('تم تسليم الواجب من قبل', false)
            ->assertSee('إعادة الواجب', false)
            ->assertSee('/home_work?assignment='.$twoAttemptAssignment->id.'&amp;retry=1', false)
            ->assertDontSee('What is velocity?', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/home_work?assignment='.$twoAttemptAssignment->id.'&retry=1')
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('What is velocity?', false);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'assignment'])
            ->set('assignmentId', $twoAttemptAssignment->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
            ])
            ->call('submit');

        $secondLimitedAssignmentAttempt = StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $twoAttemptAssignment->id)
            ->where('attempt_number', 2)
            ->firstOrFail();

        $this->assertSame(2, StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $twoAttemptAssignment->id)
            ->count());

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'assignment'])
            ->set('assignmentId', $twoAttemptAssignment->id)
            ->call('submit')
            ->assertRedirect('/home_work_done?attempt='.$secondLimitedAssignmentAttempt->id);

        $this->assertSame(2, StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $twoAttemptAssignment->id)
            ->count());

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $exam->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
            ])
            ->call('submit');

        $examAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $exam->id)
            ->firstOrFail();

        $this->assertTrue($examAttempt->examModel()->is($examModel));
        $this->assertSame('graded', $examAttempt->currentStatus?->type?->slug);
        $this->assertSame(10.0, (float) $examAttempt->studentAnswers->first()?->score);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz_done?attempt='.$examAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('تم التصحيح', false)
            ->assertSee('أحسنت! لقد اجتزت الاختبار', false)
            ->assertSee('1/1', false)
            ->assertSee('مراجعة الإجابات الصحيحة', false)
            ->assertSee('/quiz_review?attempt='.$examAttempt->id, false)
            ->assertDontSee('What is velocity?', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz_review?attempt='.$examAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertDontSee('أحسنت! لقد اجتزت الاختبار', false)
            ->assertDontSee('1/1', false)
            ->assertSee('العودة للمادة', false)
            ->assertSee('/single_teacher?teacher='.$teacher->id.'&amp;subject='.$accountSubject->id, false)
            ->assertSee('What is velocity?', false)
            ->assertSee('Displacement over time', false)
            ->assertSee('10.00 / 10.00', false);

        $twoAttemptExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Two Attempt Quiz', 'ar' => 'اختبار بمحاولتين'],
            'duration_minutes' => 20,
            'num_of_attempts' => 2,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        ExamModel::query()->create([
            'exam_id' => $twoAttemptExam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $mcqQuestion->id, 'max_score' => 10],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $twoAttemptExam->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
            ])
            ->call('submit');

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$twoAttemptExam->id)
            ->assertOk()
            ->assertSee('تم تسليم الاختبار من قبل', false)
            ->assertSee('إعادة الامتحان', false)
            ->assertSee('/quiz?exam='.$twoAttemptExam->id.'&amp;retry=1', false)
            ->assertDontSee('What is velocity?', false);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$twoAttemptExam->id.'&retry=1')
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('What is velocity?', false);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $twoAttemptExam->id)
            ->set('answers', [
                $mcqQuestion->id => $correctOption->id,
            ])
            ->call('submit');

        $secondLimitedAttempt = StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $twoAttemptExam->id)
            ->where('attempt_number', 2)
            ->firstOrFail();

        $this->assertSame(2, StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $twoAttemptExam->id)
            ->count());

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $twoAttemptExam->id)
            ->call('submit')
            ->assertRedirect('/quiz_done?attempt='.$secondLimitedAttempt->id);

        $this->assertSame(2, StudentAttempt::query()
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $twoAttemptExam->id)
            ->count());

        $timeoutExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Timeout Quiz', 'ar' => 'اختبار انتهاء الوقت'],
            'duration_minutes' => 1,
            'max_degree' => 20,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        ExamModel::query()->create([
            'exam_id' => $timeoutExam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $mcqQuestion->id, 'max_score' => 10],
                ['id' => $statementQuestion->id, 'max_score' => 10],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $timeoutExam->id)
            ->call('submit', true);

        $timeoutAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $timeoutExam->id)
            ->firstOrFail();

        $this->assertSame('graded', $timeoutAttempt->currentStatus?->type?->slug);
        $this->assertCount(2, $timeoutAttempt->studentAnswers);
        $this->assertSame(0.0, (float) $timeoutAttempt->studentAnswers->sum('score'));

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz_done?attempt='.$timeoutAttempt->id)
            ->assertOk()
            ->assertSee('0/2', false)
            ->assertDontSee('0/20', false);

        $returnedExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Returned Quiz', 'ar' => 'اختبار الرجوع'],
            'duration_minutes' => 15,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);
        ExamModel::query()->create([
            'exam_id' => $returnedExam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $mcqQuestion->id, 'max_score' => 10],
            ],
        ]);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$returnedExam->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('What is velocity?', false);

        $returnedStartedAttempt = StudentAttempt::query()
            ->with(['studentAnswers', 'currentStatus.type'])
            ->where('student_user_id', $user->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $returnedExam->id)
            ->firstOrFail();

        $this->assertSame('in_progress', $returnedStartedAttempt->currentStatus?->type?->slug);
        $this->assertNull($returnedStartedAttempt->studentAnswers->first()?->question_option_id);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/quiz?exam='.$returnedExam->id)
            ->assertOk()
            ->assertSee('تم تسليم الاختبار من قبل', false);

        $returnedFinalAttempt = $returnedStartedAttempt->refresh()->load(['studentAnswers', 'currentStatus.type']);

        $this->assertSame('graded', $returnedFinalAttempt->currentStatus?->type?->slug);
        $this->assertNull($returnedFinalAttempt->studentAnswers->first()?->question_option_id);
        $this->assertSame(0.0, (float) $returnedFinalAttempt->studentAnswers->sum('score'));
    }

    public function test_standalone_teacher_website_uses_provider_teacher_without_academy_teacher(): void
    {
        $provider = $this->provider('mona-physics', ProviderType::StandaloneTeacher);
        $provider->owner()->update([
            'first_name' => 'Mona',
            'last_name' => 'Physics',
        ]);
        Account::query()->create([
            'provider_id' => $provider->id,
            'owner_user_id' => $provider->owner_user_id,
            'type' => AccountType::StandaloneTeacher,
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $studentUser = User::factory()->create();
        $this->studentAccount($provider, $studentUser);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($studentUser, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
        ]);
        $emptySubject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Empty Subject', 'ar' => 'مادة بدون كورس'],
        ]);
        AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $emptySubject->id,
            ])->id,
            'is_active' => true,
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);

        Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => null,
            'title' => ['en' => 'Physics Course', 'ar' => 'كورس الفيزياء'],
            'weekly_lectures_count' => 2,
        ]);

        $baseUrl = 'http://'.$provider->subdomain.'.'.config('almanasa.root_domain');

        $this->actingAs($studentUser)
            ->get($baseUrl.'/teachers?subject='.$accountSubject->id)
            ->assertOk()
            ->assertSeeLivewire(TeachersPage::class)
            ->assertSee('Mona Physics', false)
            ->assertSee('الفيزياء', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/single_teacher')
            ->assertOk()
            ->assertSeeLivewire(SingleTeacherPage::class)
            ->assertSee('Mona Physics', false)
            ->assertSee('كورس الفيزياء', false)
            ->assertDontSee('لا يوجد كورس منشأ', false);
    }

    public function test_standalone_teacher_assessment_pages_use_teacher_template_flow(): void
    {
        $provider = $this->provider('mona-physics', ProviderType::StandaloneTeacher);
        $provider->owner()->update([
            'first_name' => 'Mona',
            'last_name' => 'Physics',
        ]);
        Account::query()->create([
            'provider_id' => $provider->id,
            'owner_user_id' => $provider->owner_user_id,
            'type' => AccountType::StandaloneTeacher,
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $studentUser = User::factory()->create();
        $this->studentAccount($provider, $studentUser);

        $stage = EducationStage::query()->create(['name' => 'Secondary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);
        $this->studentProfile($studentUser, $grade);
        $track = Track::query()->create(['name' => ['en' => 'Scientific', 'ar' => 'علمي'], 'code' => 'scientific']);
        $subject = Subject::query()->create([
            'track_id' => $track->id,
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
        ]);
        $accountSubject = AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => GradeSubject::query()->create([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ])->id,
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => null,
            'title' => ['en' => 'Physics Course', 'ar' => 'كورس الفيزياء'],
        ]);
        $period = CoursePeriod::query()->create([
            'type' => CoursePeriodType::Term1->value,
            'name' => ['en' => 'Term 1', 'ar' => 'الترم الأول'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'course_period_id' => $period->id,
            'title' => ['en' => 'Motion', 'ar' => 'الحركة'],
            'is_active' => true,
        ]);
        $question = Question::query()->create([
            'lesson_id' => $lesson->id,
            'title' => 'What is force?',
            'type' => QuestionType::Mcq->value,
            'difficulty' => QuestionDifficulty::Easy->value,
            'sort_order' => 1,
        ]);
        $correctOption = QuestionOption::query()->create([
            'question_id' => $question->id,
            'title' => 'Mass times acceleration',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        QuestionOption::query()->create([
            'question_id' => $question->id,
            'title' => 'Distance over time',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $assignment = Assignment::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Physics Homework', 'ar' => 'واجب الفيزياء'],
            'duration_minutes' => 15,
            'num_of_attempts' => 2,
            'question_ids' => [$question->id],
        ]);
        $assignmentItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'assignment_id' => $assignment->id,
            'type' => LessonTypeEnum::Assignments->value,
            'title' => ['en' => 'Homework Item', 'ar' => 'عنصر الواجب'],
            'is_active' => true,
            'is_free' => true,
        ]);
        $expiredAssignment = Assignment::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Expired Physics Homework', 'ar' => 'واجب فيزياء منتهي'],
            'duration_minutes' => 15,
            'num_of_attempts' => 2,
            'question_ids' => [$question->id],
        ]);
        $expiredAssignmentItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'assignment_id' => $expiredAssignment->id,
            'type' => LessonTypeEnum::Assignments->value,
            'title' => ['en' => 'Expired Homework Item', 'ar' => 'عنصر واجب منتهي'],
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'is_active' => true,
            'is_free' => true,
            'sort_order' => 2,
        ]);

        $exam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Physics Quiz', 'ar' => 'اختبار الفيزياء'],
            'duration_minutes' => 10,
            'max_degree' => 10,
            'num_of_models' => 1,
            'num_of_attempts' => 2,
            'lesson_ids' => [$lesson->id],
        ]);
        ExamModel::query()->create([
            'exam_id' => $exam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $question->id, 'max_score' => 10],
            ],
        ]);
        $examItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'exam_id' => $exam->id,
            'type' => LessonTypeEnum::Exams->value,
            'title' => ['en' => 'Quiz Item', 'ar' => 'عنصر الاختبار'],
            'is_active' => true,
            'is_free' => true,
        ]);
        $expiredExam = Exam::query()->create([
            'course_id' => $course->id,
            'title' => ['en' => 'Expired Physics Quiz', 'ar' => 'اختبار فيزياء منتهي'],
            'duration_minutes' => 10,
            'max_degree' => 10,
            'num_of_models' => 1,
            'num_of_attempts' => 2,
            'lesson_ids' => [$lesson->id],
        ]);
        ExamModel::query()->create([
            'exam_id' => $expiredExam->id,
            'model_number' => 1,
            'question_ids' => [
                ['id' => $question->id, 'max_score' => 10],
            ],
        ]);
        $expiredExamItem = LessonItem::query()->create([
            'lesson_id' => $lesson->id,
            'exam_id' => $expiredExam->id,
            'type' => LessonTypeEnum::Exams->value,
            'title' => ['en' => 'Expired Quiz Item', 'ar' => 'عنصر اختبار منتهي'],
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'is_active' => true,
            'is_free' => true,
            'sort_order' => 3,
        ]);

        $baseUrl = 'http://'.$provider->subdomain.'.'.config('almanasa.root_domain');

        $this->actingAs($studentUser)
            ->get($baseUrl.'/single_teacher?subject='.$accountSubject->id)
            ->assertOk()
            ->assertSee('عنصر واجب منتهي', false)
            ->assertSee('عنصر اختبار منتهي', false)
            ->assertSee('انتهى في', false)
            ->assertDontSee('href="/lesson?item='.$expiredAssignmentItem->id.'"', false)
            ->assertDontSee('href="/lesson?item='.$expiredExamItem->id.'"', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/home_work?assignment='.$assignment->id.'&item='.$assignmentItem->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('إنهاء الواجب', false);

        Livewire::actingAs($studentUser)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'assignment'])
            ->set('assignmentId', $assignment->id)
            ->set('itemId', $assignmentItem->id)
            ->set('answers', [
                $question->id => $correctOption->id,
            ])
            ->call('submit');

        $assignmentAttempt = StudentAttempt::query()
            ->where('student_user_id', $studentUser->id)
            ->where('attemptable_type', Assignment::class)
            ->where('attemptable_id', $assignment->id)
            ->firstOrFail();

        $this->actingAs($studentUser)
            ->get($baseUrl.'/home_work?assignment='.$assignment->id.'&item='.$assignmentItem->id)
            ->assertOk()
            ->assertSee('/home_work?assignment='.$assignment->id.'&amp;item='.$assignmentItem->id.'&amp;retry=1', false)
            ->assertSee('إعادة الواجب', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/home_work_done?attempt='.$assignmentAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('href="/single_teacher?subject='.$accountSubject->id.'"', false)
            ->assertSee('/home_work_done?attempt='.$assignmentAttempt->id.'&amp;review=1', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/home_work_done?attempt='.$assignmentAttempt->id.'&review=1')
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('href="/single_teacher?subject='.$accountSubject->id.'"', false)
            ->assertSee('What is force?', false)
            ->assertDontSee('أحسنت! لقد أنهيت الواجب', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/home_work?assignment='.$expiredAssignment->id.'&item='.$expiredAssignmentItem->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('الواجب مغلق حالياً', false)
            ->assertDontSee('إنهاء الواجب', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/quiz?exam='.$exam->id.'&item='.$examItem->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('إنهاء الاختبار', false);

        Livewire::actingAs($studentUser)
            ->test(AssessmentPage::class, ['providerId' => $provider->id, 'type' => 'exam'])
            ->set('examId', $exam->id)
            ->set('itemId', $examItem->id)
            ->set('answers', [
                $question->id => $correctOption->id,
            ])
            ->call('submit');

        $examAttempt = StudentAttempt::query()
            ->where('student_user_id', $studentUser->id)
            ->where('attemptable_type', Exam::class)
            ->where('attemptable_id', $exam->id)
            ->firstOrFail();

        $this->actingAs($studentUser)
            ->get($baseUrl.'/quiz_done?attempt='.$examAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('href="/single_teacher?subject='.$accountSubject->id.'"', false)
            ->assertSee('/quiz_review?attempt='.$examAttempt->id, false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/quiz_review?attempt='.$examAttempt->id)
            ->assertOk()
            ->assertSeeLivewire(AttemptResultPage::class)
            ->assertSee('#FEB008', false)
            ->assertSee('href="/single_teacher?subject='.$accountSubject->id.'"', false)
            ->assertSee('What is force?', false)
            ->assertDontSee('أحسنت! لقد اجتزت الاختبار', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/quiz?exam='.$expiredExam->id.'&item='.$expiredExamItem->id)
            ->assertOk()
            ->assertSeeLivewire(AssessmentPage::class)
            ->assertSee('الاختبار مغلق حالياً', false)
            ->assertDontSee('إنهاء الاختبار', false);

        $this->actingAs($studentUser)
            ->get($baseUrl.'/quiz?exam='.$exam->id.'&item='.$examItem->id)
            ->assertOk()
            ->assertSee('/quiz?exam='.$exam->id.'&amp;item='.$examItem->id.'&amp;retry=1', false)
            ->assertSee('إعادة الامتحان', false);
    }

    public function test_home_cta_links_guests_to_login(): void
    {
        $provider = $this->provider();

        $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertSeeLivewire(HomeCta::class)
            ->assertSee('جاهز للانطلاق نحو التفوق ؟', false)
            ->assertSee('href="/login"', false)
            ->assertSee('ابدأ رحلتك الآن', false);
    }

    public function test_home_cta_is_hidden_for_authenticated_users(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $this->studentAccount($provider, $user);

        $this->actingAs($user)
            ->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/')
            ->assertOk()
            ->assertDontSee('جاهز للانطلاق نحو التفوق ؟', false);
    }

    public function test_existing_provider_student_with_profile_redirects_home(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create(['phone' => '01012345678']);
        $this->studentAccount($provider, $user);
        $this->studentProfile($user);

        Livewire::test(LoginForm::class, ['providerId' => $provider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01012345678')
            ->call('sendOtp')
            ->set('otp', '1234')
            ->call('verify')
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, Account::query()->where('owner_user_id', $user->id)->count());
    }

    public function test_existing_user_gets_new_account_for_different_provider_and_reuses_profile(): void
    {
        $firstProvider = $this->provider('first-academy');
        $secondProvider = $this->provider('second-academy');
        $user = User::factory()->create(['phone' => '01012345678']);
        $this->studentAccount($firstProvider, $user);
        $this->studentProfile($user);

        Livewire::test(LoginForm::class, ['providerId' => $secondProvider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01012345678')
            ->call('sendOtp')
            ->set('otp', '1234')
            ->call('verify')
            ->assertRedirect('/');

        $this->assertSame(2, Account::query()->where('owner_user_id', $user->id)->count());
        $this->assertSame(1, StudentProfile::query()->where('user_id', $user->id)->count());
    }

    public function test_invalid_otp_does_not_create_user_or_account(): void
    {
        $provider = $this->provider();

        Livewire::test(LoginForm::class, ['providerId' => $provider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01012345678')
            ->call('sendOtp')
            ->set('otp', '9999')
            ->call('verify')
            ->assertHasErrors(['otp']);

        $this->assertGuest();
        $this->assertSame(0, User::query()->where('phone', '01012345678')->count());
        $this->assertSame(0, Account::query()->where('provider_id', $provider->id)->count());
    }

    public function test_registration_disabled_blocks_new_membership_but_allows_existing_student(): void
    {
        $provider = $this->provider(registrationEnabled: false);

        Livewire::test(LoginForm::class, ['providerId' => $provider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01011111111')
            ->call('sendOtp')
            ->set('otp', '1234')
            ->call('verify')
            ->assertHasErrors(['phone']);

        $user = User::factory()->create(['phone' => '01022222222']);
        $this->studentAccount($provider, $user);

        Livewire::test(LoginForm::class, ['providerId' => $provider->id])
            ->set('dialCountryCode', '+20')
            ->set('phone', '01022222222')
            ->call('sendOtp')
            ->set('otp', '1234')
            ->call('verify')
            ->assertRedirect('/register');

        $this->assertAuthenticatedAs($user);
    }

    public function test_register_form_creates_shared_student_profile_once(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create(['first_name' => '']);
        $this->studentAccount($provider, $user);
        $country = Country::query()->create(['name' => 'Egypt', 'code' => 'EG', 'phone_code' => '+20']);
        $city = City::query()->create(['country_id' => $country->id, 'name' => 'Cairo']);
        $stage = EducationStage::query()->create(['name' => 'Primary', 'sort_order' => 1]);
        $grade = Grade::query()->create(['education_stage_id' => $stage->id, 'name' => 'Grade 1', 'sort_order' => 1]);

        $this->actingAs($user);

        Livewire::test(RegisterForm::class, ['providerId' => $provider->id])
            ->set('firstName', 'Nasr')
            ->set('lastName', 'Student')
            ->set('email', 'nasr@example.com')
            ->set('dateOfBirth', '2015-01-01')
            ->set('gender', 'male')
            ->set('countryId', $country->id)
            ->set('cityId', $city->id)
            ->set('educationStageId', $stage->id)
            ->set('gradeId', $grade->id)
            ->set('schoolName', 'Almanasa School')
            ->call('save')
            ->assertRedirect('/');

        $this->assertDatabaseHas(StudentProfile::class, [
            'user_id' => $user->id,
            'email' => 'nasr@example.com',
            'grade_id' => $grade->id,
        ]);
    }

    public function test_logout_clears_auth_session(): void
    {
        $provider = $this->provider();
        $user = User::factory()->create();
        $account = $this->studentAccount($provider, $user);

        $this->actingAs($user)
            ->withSession([
                'current_account_id' => $account->id,
                'current_provider_id' => $provider->id,
            ])
            ->post('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    private function provider(
        string $slug = 'academy',
        ProviderType $type = ProviderType::Academy,
        bool $registrationEnabled = true,
    ): Provider {
        $owner = User::factory()->create();
        $plan = ProviderPlan::query()->create([
            'name' => ['en' => 'Basic'],
            'is_active' => true,
        ]);
        $planOption = ProviderPlanOption::query()->create([
            'provider_plan_id' => $plan->id,
            'billing_period_days' => 30,
            'price' => 0,
        ]);

        $provider = Provider::query()->create([
            'type' => $type,
            'owner_user_id' => $owner->id,
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'subdomain' => $slug,
            'pause_website' => false,
            'is_active' => true,
        ]);

        ProviderSubscription::query()->create([
            'provider_id' => $provider->id,
            'provider_plan_option_id' => $planOption->id,
            'status' => ProviderSubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'amount' => 0,
        ]);

        return $provider;
    }

    private function studentAccount(Provider $provider, User $user): Account
    {
        return Account::query()->create([
            'provider_id' => $provider->id,
            'owner_user_id' => $user->id,
            'type' => AccountType::Student,
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function teacherAccount(Provider $provider, User $user): Account
    {
        return Account::query()->create([
            'provider_id' => $provider->id,
            'owner_user_id' => $user->id,
            'type' => AccountType::AcademyTeacher,
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function studentProfile(User $user, ?Grade $grade = null): StudentProfile
    {
        return StudentProfile::query()->create([
            'user_id' => $user->id,
            'email' => 'student'.$user->id.'@example.com',
            'education_stage_id' => $grade?->education_stage_id,
            'grade_id' => $grade?->id,
        ]);
    }
}
