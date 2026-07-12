<?php

namespace App\Models\Concerns;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopesTenantUsers
{
    protected static function bootScopesTenantUsers(): void
    {
        static::addGlobalScope('tenantAccess', function (Builder $builder): void {
            if (! Auth::guard()->hasUser()) {
                return;
            }

            $tenantIds = Auth::user()?->accessibleTenantIds() ?? [];
            $userId = Auth::id();

            $builder->where(function (Builder $query) use ($tenantIds, $userId): void {
                $query->whereKey($userId);

                if ($tenantIds !== []) {
                    $query->orWhereHas('tenantMemberships', function (Builder $membershipQuery) use ($tenantIds): void {
                        $membershipQuery
                            ->withoutGlobalScope('tenantAccess')
                            ->whereIn('tenant_id', $tenantIds)
                            ->where('status', MembershipStatus::Active->value);
                    });
                }
            });
        });
    }
}
