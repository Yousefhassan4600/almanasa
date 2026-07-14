<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Livewire\Website\LoginForm;
use App\Livewire\Website\RegisterForm;
use App\Models\Account;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderSubscription;
use App\Models\StudentProfile;
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

    public function test_login_page_uses_public_template_with_plain_post_form(): void
    {
        $provider = $this->provider();

        $response = $this->get('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/login');

        $response
            ->assertOk()
            ->assertSee('phone-verification-form', false)
            ->assertSee('method="POST"', false)
            ->assertSee('action="/login/send-otp"', false)
            ->assertDontSee('src="/livewire', false)
            ->assertDontSee('wire:submit', false)
            ->assertDontSee('http://127.0.0.1:8000/livewire', false)
            ->assertSee('href="/login"', false)
            ->assertSee('href="/subjects"', false)
            ->assertDontSee('.html"', false);
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

    public function test_plain_html_post_fallback_sends_and_verifies_otp(): void
    {
        $provider = $this->provider();

        $this->post('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/login/send-otp', [
            'dial_country_code' => '+20',
            'phone' => '01012345678',
        ])->assertRedirect('/otp');

        $this->assertTrue(session()->has(LoginForm::challengeKeyFor($provider->id)));

        $this->post('http://'.$provider->subdomain.'.'.config('almanasa.root_domain').'/otp/verify', [
            'otp1' => '1',
            'otp2' => '2',
            'otp3' => '3',
            'otp4' => '4',
        ])->assertRedirect('/register');

        $this->assertAuthenticated();
        $this->assertDatabaseHas(Account::class, [
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
            ->assertSee('action="/logout"', false)
            ->assertDontSee('href="/profile"', false)
            ->assertDontSee('href="/cart"', false)
            ->assertDontSee('id="openSidebarBtn"', false)
            ->assertDontSee('id="mobileSidebar"', false);
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
        $country = Country::query()->create(['name' => 'Egypt', 'code' => 'EG', 'phone_code' => '+20', 'currency_code' => 'EGP']);
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
            'code' => $slug.'-basic',
            'price' => 0,
            'billing_period_days' => 30,
            'is_active' => true,
        ]);

        $provider = Provider::query()->create([
            'type' => $type,
            'owner_user_id' => $owner->id,
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'subdomain' => $slug,
            'website_enabled' => true,
            'registration_enabled' => $registrationEnabled,
            'is_active' => true,
        ]);

        ProviderSubscription::query()->create([
            'provider_id' => $provider->id,
            'provider_plan_id' => $plan->id,
            'status' => ProviderSubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'amount' => 0,
            'currency_code' => 'EGP',
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

    private function studentProfile(User $user): StudentProfile
    {
        return StudentProfile::query()->create([
            'user_id' => $user->id,
            'email' => 'student'.$user->id.'@example.com',
        ]);
    }
}
