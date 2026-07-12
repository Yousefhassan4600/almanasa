<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\Provider;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_saas_owner_can_see_all_tenant_records(): void
    {
        $saasOwner = $this->account(AccountType::SaasOwner, 'saas-owner');
        $academyProvider = $this->provider(ProviderType::Academy, 'academy');
        $teacherProvider = $this->provider(ProviderType::StandaloneTeacher, 'teacher');

        Role::query()->create(['provider_id' => $academyProvider->id, 'name' => 'academy-role']);
        Role::query()->create(['provider_id' => $teacherProvider->id, 'name' => 'teacher-role']);

        $this->actingAsTenant($saasOwner);

        $this->assertSame(2, Role::query()->forCurrentTenant()->count());
    }

    public function test_academy_can_see_its_provider_records_only(): void
    {
        $academyProvider = $this->provider(ProviderType::Academy, 'academy');
        $otherProvider = $this->provider(ProviderType::Academy, 'other-academy');
        $academy = $this->account(AccountType::Academy, 'academy', provider: $academyProvider);

        Role::query()->create(['provider_id' => $academyProvider->id, 'name' => 'academy-role']);
        Role::query()->create(['provider_id' => $academyProvider->id, 'name' => 'student-support-role']);
        Role::query()->create(['provider_id' => $otherProvider->id, 'name' => 'other-role']);

        $this->actingAsTenant($academy);

        $this->assertEqualsCanonicalizing(
            ['academy-role', 'student-support-role'],
            Role::query()->forCurrentTenant()->pluck('name')->all(),
        );
    }

    public function test_teacher_can_see_records_for_its_provider_teacher_account(): void
    {
        $teacherUser = User::factory()->create();
        $provider = $this->provider(ProviderType::Academy, 'academy');
        $otherProvider = $this->provider(ProviderType::Academy, 'other-academy');
        $teacher = $this->account(AccountType::AcademyTeacher, 'teacher', owner: $teacherUser, provider: $provider);
        $sameUserOtherTeacherAccount = $this->account(
            AccountType::AcademyTeacher,
            'same-user-other-teacher',
            owner: $teacherUser,
            provider: $otherProvider,
        );

        $assignedToTeacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacher->id,
            'status' => AccountStatus::Active,
            'joined_at' => now(),
        ]);
        AcademyTeacher::query()->create([
            'provider_id' => $otherProvider->id,
            'teacher_account_id' => $sameUserOtherTeacherAccount->id,
            'status' => AccountStatus::Active,
            'joined_at' => now(),
        ]);

        $this->actingAsTenant($teacher);

        $this->assertSame(
            [$assignedToTeacher->id],
            AcademyTeacher::query()->forCurrentTenant()->pluck('id')->all(),
        );
    }

    public function test_global_catalog_records_are_not_tenant_filtered(): void
    {
        $academy = $this->account(AccountType::Academy, 'academy', provider: $this->provider(ProviderType::Academy, 'academy'));

        Subject::query()->create([
            'name' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
        ]);
        Subject::query()->create([
            'name' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
        ]);

        $this->actingAsTenant($academy);

        $this->assertSame(2, Subject::query()->forCurrentTenant()->count());
    }

    private function actingAsTenant(Account $account): void
    {
        $account->loadMissing('provider');

        $this->app['request']->attributes->set('current_account', $account);
        $this->app['request']->attributes->set('current_provider', $account->provider);
        $this->actingAs($account->owner);
    }

    private function provider(ProviderType $type, string $slug): Provider
    {
        return Provider::query()->create([
            'type' => $type,
            'owner_user_id' => User::factory()->create()->id,
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
        ]);
    }

    private function account(
        AccountType $type,
        string $slug,
        ?Account $parent = null,
        ?User $owner = null,
        ?Provider $provider = null,
    ): Account {
        return Account::query()->create([
            'provider_id' => $provider?->id,
            'type' => $type,
            'owner_user_id' => ($owner ?? User::factory()->create())->id,
            'parent_account_id' => $parent?->id,
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'status' => AccountStatus::Active,
            'approved_at' => now(),
        ]);
    }
}
