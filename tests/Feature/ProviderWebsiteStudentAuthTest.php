<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Livewire\Website\AuthControls;
use App\Livewire\Website\HomeCta;
use App\Livewire\Website\HomeSubjects;
use App\Livewire\Website\LoginForm;
use App\Livewire\Website\RegisterForm;
use App\Livewire\Website\SubjectsPage;
use App\Livewire\Website\TeachersPage;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\ProviderSubscription;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Course::query()->create([
            'provider_id' => $provider->id,
            'account_subject_id' => $mathAccountSubject->id,
            'teacher_account_id' => $mathTeacherAccount->id,
            'title' => ['en' => 'Math Course', 'ar' => 'كورس الرياضيات'],
            'slug' => 'math-course',
            'price' => 500,
            'monthly_price' => 180,
            'weekly_lectures_count' => 2,
            'status' => 'published',
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
