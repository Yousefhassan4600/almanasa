<?php

namespace Tests\Feature;

use App\Enums\MembershipStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PlanType;
use App\Enums\TenantRole;
use App\Enums\TenantStatus;
use App\Enums\TenantType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantAccessScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_only_read_records_for_their_tenants(): void
    {
        [$academyOwner, $academyTenant, $academyPlan] = $this->tenantWithOwner('academy-owner@example.com', 'academy');
        [, $teacherTenant, $teacherPlan] = $this->tenantWithOwner('teacher-owner@example.com', 'teacher');

        $student = User::factory()->create(['email' => 'student@example.com']);
        TenantUser::create([
            'tenant_id' => $academyTenant->id,
            'user_id' => $student->id,
            'role' => TenantRole::Student->value,
            'status' => MembershipStatus::Active->value,
            'joined_at' => now(),
        ]);

        $academyOrder = $this->orderFor($academyTenant, $student);
        $teacherOrder = $this->orderFor($teacherTenant, $student);

        OrderItem::create([
            'order_id' => $academyOrder->id,
            'item_type' => Plan::class,
            'item_id' => $academyPlan->id,
            'title' => 'Academy plan',
            'unit_price' => 100,
            'quantity' => 1,
            'total' => 100,
        ]);

        OrderItem::create([
            'order_id' => $teacherOrder->id,
            'item_type' => Plan::class,
            'item_id' => $teacherPlan->id,
            'title' => 'Teacher plan',
            'unit_price' => 100,
            'quantity' => 1,
            'total' => 100,
        ]);

        $this->actingAs($academyOwner);

        $this->assertSame([$academyPlan->id], Plan::query()->pluck('id')->all());
        $this->assertSame([$academyOrder->id], Order::query()->pluck('id')->all());
        $this->assertSame(['Academy plan'], OrderItem::query()->pluck('title')->all());
        $this->assertSame(['academy-owner@example.com', 'student@example.com'], User::query()->orderBy('email')->pluck('email')->all());
    }

    public function test_authenticated_users_cannot_save_records_for_another_tenant(): void
    {
        [$academyOwner] = $this->tenantWithOwner('academy-owner@example.com', 'academy');
        [, $teacherTenant] = $this->tenantWithOwner('teacher-owner@example.com', 'teacher');

        $this->actingAs($academyOwner);

        $this->expectException(HttpException::class);

        Plan::create([
            'tenant_id' => $teacherTenant->id,
            'name' => 'Forbidden plan',
            'type' => PlanType::Course->value,
            'price' => 100,
        ]);
    }

    /**
     * @return array{User, Tenant, Plan}
     */
    private function tenantWithOwner(string $email, string $slug): array
    {
        $owner = User::factory()->create(['email' => $email]);

        $tenant = Tenant::create([
            'owner_user_id' => $owner->id,
            'name' => ucfirst($slug),
            'slug' => $slug,
            'type' => $slug === 'teacher' ? TenantType::StandaloneTeacher->value : TenantType::Academy->value,
            'status' => TenantStatus::Active->value,
        ]);

        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => $owner->id,
            'role' => TenantRole::Owner->value,
            'status' => MembershipStatus::Active->value,
            'joined_at' => now(),
        ]);

        $plan = Plan::create([
            'tenant_id' => $tenant->id,
            'name' => ucfirst($slug).' plan',
            'type' => PlanType::Course->value,
            'price' => 100,
        ]);

        return [$owner, $tenant, $plan];
    }

    private function orderFor(Tenant $tenant, User $student): Order
    {
        return Order::create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'order_number' => $tenant->slug.'-order',
            'subtotal' => 100,
            'total' => 100,
            'status' => OrderStatus::Pending->value,
            'payment_status' => PaymentStatus::Pending->value,
        ]);
    }
}
