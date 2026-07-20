<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\AdminPermissionAction;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Livewire\Admin\AccountPicker;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\AccountSubject;
use App\Models\Employee;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\ProviderSubscription;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_owner_has_default_admin_permissions(): void
    {
        $owner = User::factory()->create();
        $provider = $this->provider($owner);
        $account = $this->account(AccountType::Academy, $owner, $provider);

        $this->actingAsTenant($account);

        $this->assertTrue(AdminPermissions::can(CourseResource::class, AdminPermissionAction::Create));
        $this->assertTrue(AdminPermissions::can(CourseResource::class, AdminPermissionAction::Delete));
    }

    public function test_employee_uses_provider_scoped_spatie_role_permissions(): void
    {
        $owner = User::factory()->create();
        $employeeUser = User::factory()->create();
        $provider = $this->provider($owner);
        $account = $this->account(AccountType::Academy, $owner, $provider);

        $createCourses = Permission::findOrCreate('courses.create', 'web');
        Permission::findOrCreate('courses.delete', 'web');

        $role = Role::query()->create([
            'provider_id' => $provider->id,
            'created_by_account_id' => $account->id,
            'name' => 'content_creator',
            'guard_name' => 'web',
            'is_assignable' => true,
        ]);
        $role->syncPermissions([$createCourses]);

        Employee::query()->create([
            'account_id' => $account->id,
            'user_id' => $employeeUser->id,
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAsTenant($account, $employeeUser);

        $this->assertTrue(AdminPermissions::can(CourseResource::class, AdminPermissionAction::Create));
        $this->assertFalse(AdminPermissions::can(CourseResource::class, AdminPermissionAction::Delete));
    }

    public function test_academy_teacher_default_access_is_scoped_to_his_courses(): void
    {
        $academyOwner = User::factory()->create();
        $teacherUser = User::factory()->create();
        $otherTeacherUser = User::factory()->create();
        $provider = $this->provider($academyOwner);
        $accountSubject = $this->accountSubject($provider);

        $teacherAccount = $this->account(AccountType::AcademyTeacher, $teacherUser, $provider);
        $otherTeacherAccount = $this->account(AccountType::AcademyTeacher, $otherTeacherUser, $provider);

        $academyTeacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $teacherAccount->id,
            'is_active' => true,
        ]);

        $otherAcademyTeacher = AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $otherTeacherAccount->id,
            'is_active' => true,
        ]);

        $academyTeacherRole = Role::query()
            ->whereNull('provider_id')
            ->where('name', AdminPermissions::ACADEMY_TEACHER_ROLE)
            ->firstOrFail();

        setPermissionsTeamId($provider->id);

        $this->assertTrue($teacherUser->fresh()->hasRole($academyTeacherRole));
        $this->assertFalse(RoleResource::canEdit($academyTeacherRole));
        $this->assertFalse(RoleResource::canDelete($academyTeacherRole));
        $this->assertTrue($academyTeacherRole->permissions()->where('name', 'courses.viewHis')->exists());
        $this->assertFalse($academyTeacherRole->permissions()->where('name', 'courses.viewAll')->exists());
        $this->assertFalse($academyTeacherRole->permissions()->where('name', 'provider-codes.viewHis')->exists());

        $ownCourse = $provider->courses()->create([
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $academyTeacher->id,
            'title' => ['ar' => 'كورس المعلم', 'en' => 'Teacher Course'],
        ]);

        $otherCourse = $provider->courses()->create([
            'account_subject_id' => $accountSubject->id,
            'academy_teacher_id' => $otherAcademyTeacher->id,
            'title' => ['ar' => 'كورس آخر', 'en' => 'Other Course'],
        ]);

        $this->actingAsTenant($teacherAccount);

        $this->assertTrue(AdminPermissions::can(CourseResource::class, AdminPermissionAction::ViewAny));
        $this->assertTrue(AdminPermissions::can(CourseResource::class, AdminPermissionAction::Create));
        $this->assertTrue(CourseResource::canEdit($ownCourse));
        $this->assertFalse(CourseResource::canEdit($otherCourse));
        $this->assertSame([$ownCourse->id], CourseResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_account_picker_hides_academy_teacher_accounts_without_active_assignment(): void
    {
        $academyOwner = User::factory()->create();
        $teacherUser = User::factory()->create();
        $provider = $this->provider($academyOwner);
        $otherProvider = $this->provider(User::factory()->create());
        $this->activateProvider($provider);
        $this->activateProvider($otherProvider);

        $validTeacherAccount = $this->account(AccountType::AcademyTeacher, $teacherUser, $provider);
        $staleTeacherAccount = $this->account(AccountType::AcademyTeacher, $teacherUser, $otherProvider);

        AcademyTeacher::query()->create([
            'provider_id' => $provider->id,
            'teacher_account_id' => $validTeacherAccount->id,
            'is_active' => true,
        ]);

        AcademyTeacher::query()->create([
            'provider_id' => $otherProvider->id,
            'teacher_account_id' => $staleTeacherAccount->id,
            'is_active' => false,
        ]);

        $this->actingAs($teacherUser);

        $component = new AccountPicker;
        $method = new \ReflectionMethod($component, 'accessibleDashboardAccounts');
        $method->setAccessible(true);

        $this->assertSame(
            [$validTeacherAccount->id],
            $method->invoke($component)->pluck('id')->all(),
        );
    }

    public function test_saas_owner_can_open_academy_teacher_system_role_without_deleting_it(): void
    {
        $saasOwner = User::factory()->create();
        $saasAccount = $this->account(AccountType::SaasOwner, $saasOwner);

        $academyTeacherRole = Role::query()->create([
            'provider_id' => null,
            'created_by_account_id' => null,
            'name' => AdminPermissions::ACADEMY_TEACHER_ROLE,
            'guard_name' => 'web',
            'is_assignable' => false,
        ]);

        $this->actingAsTenant($saasAccount);

        $this->assertTrue(RoleResource::canEdit($academyTeacherRole));
        $this->assertFalse(RoleResource::canDelete($academyTeacherRole));
    }

    private function actingAsTenant(Account $account, ?User $user = null): void
    {
        $account->loadMissing('provider');

        $this->app['request']->attributes->set('current_account', $account);
        $this->app['request']->attributes->set('current_provider', $account->provider);

        setPermissionsTeamId($account->provider_id);

        $this->actingAs($user ?? $account->owner);
    }

    private function provider(User $owner): Provider
    {
        return Provider::query()->create([
            'type' => ProviderType::Academy,
            'owner_user_id' => $owner->id,
            'name' => 'Future Stars Academy',
            'slug' => fake()->unique()->slug(),
        ]);
    }

    private function account(AccountType $type, User $owner, ?Provider $provider = null): Account
    {
        return Account::query()->create([
            'provider_id' => $provider?->id,
            'type' => $type,
            'owner_user_id' => $owner->id,
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function accountSubject(Provider $provider): AccountSubject
    {
        $educationStageId = DB::table('education_stages')->insertGetId([
            'name' => json_encode(['ar' => 'مرحلة', 'en' => 'Stage']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradeId = DB::table('grades')->insertGetId([
            'education_stage_id' => $educationStageId,
            'name' => json_encode(['ar' => 'صف', 'en' => 'Grade']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $trackId = DB::table('tracks')->insertGetId([
            'name' => json_encode(['ar' => 'عام', 'en' => 'General']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subjectId = DB::table('subjects')->insertGetId([
            'track_id' => $trackId,
            'name' => json_encode(['ar' => 'رياضيات', 'en' => 'Math']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradeSubjectId = DB::table('grade_subjects')->insertGetId([
            'grade_id' => $gradeId,
            'subject_id' => $subjectId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return AccountSubject::query()->create([
            'provider_id' => $provider->id,
            'grade_subject_id' => $gradeSubjectId,
            'is_active' => true,
        ]);
    }

    private function activateProvider(Provider $provider): void
    {
        $plan = ProviderPlan::query()->create([
            'name' => ['en' => 'Test Plan', 'ar' => 'باقة اختبار'],
            'is_active' => true,
        ]);

        $option = ProviderPlanOption::query()->create([
            'provider_plan_id' => $plan->id,
            'billing_period_days' => 30,
            'price' => 0,
        ]);

        ProviderSubscription::query()->create([
            'provider_id' => $provider->id,
            'provider_plan_option_id' => $option->id,
            'status' => ProviderSubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'amount' => 0,
        ]);
    }
}
