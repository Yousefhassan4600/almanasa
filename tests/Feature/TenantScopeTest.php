<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Models\AcademyTeacher;
use App\Models\Account;
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
        $academy = $this->account(AccountType::Academy, 'academy');
        $teacher = $this->account(AccountType::StandaloneTeacher, 'teacher');

        Role::query()->create(['account_id' => $academy->id, 'name' => 'academy-role']);
        Role::query()->create(['account_id' => $teacher->id, 'name' => 'teacher-role']);

        $this->actingAsTenant($saasOwner);

        $this->assertSame(2, Role::query()->forCurrentTenant()->count());
    }

    public function test_academy_can_see_its_own_and_child_account_records_only(): void
    {
        $academy = $this->account(AccountType::Academy, 'academy');
        $student = $this->account(AccountType::Student, 'student', $academy);
        $otherAcademy = $this->account(AccountType::Academy, 'other-academy');

        Role::query()->create(['account_id' => $academy->id, 'name' => 'academy-role']);
        Role::query()->create(['account_id' => $student->id, 'name' => 'student-role']);
        Role::query()->create(['account_id' => $otherAcademy->id, 'name' => 'other-role']);

        $this->actingAsTenant($academy);

        $this->assertEqualsCanonicalizing(
            ['academy-role', 'student-role'],
            Role::query()->forCurrentTenant()->pluck('name')->all(),
        );
    }

    public function test_teacher_can_see_records_assigned_to_teacher_account(): void
    {
        $academy = $this->account(AccountType::Academy, 'academy');
        $otherAcademy = $this->account(AccountType::Academy, 'other-academy');
        $teacher = $this->account(AccountType::AcademyTeacher, 'teacher');
        $otherTeacher = $this->account(AccountType::AcademyTeacher, 'other-teacher');

        $assignedToTeacher = AcademyTeacher::query()->create([
            'academy_account_id' => $academy->id,
            'teacher_account_id' => $teacher->id,
            'status' => AccountStatus::Active,
            'joined_at' => now(),
        ]);
        AcademyTeacher::query()->create([
            'academy_account_id' => $otherAcademy->id,
            'teacher_account_id' => $otherTeacher->id,
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
        $academy = $this->account(AccountType::Academy, 'academy');

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
        $this->app['request']->attributes->set('current_account', $account);
        $this->actingAs($account->owner);
    }

    private function account(AccountType $type, string $slug, ?Account $parent = null): Account
    {
        return Account::query()->create([
            'type' => $type,
            'owner_user_id' => User::factory()->create()->id,
            'parent_account_id' => $parent?->id,
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'status' => AccountStatus::Active,
            'approved_at' => now(),
        ]);
    }
}
