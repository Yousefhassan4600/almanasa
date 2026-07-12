<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenantAccess', function (Builder $builder): void {
            if (! Auth::guard()->hasUser()) {
                return;
            }

            $tenantIds = Auth::user()?->accessibleTenantIds() ?? [];

            if ($tenantIds === []) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->whereIn($builder->getModel()->qualifyColumn('tenant_id'), $tenantIds);
        });

        static::saving(function (object $model): void {
            if (! Auth::guard()->hasUser()) {
                return;
            }

            $tenantIds = Auth::user()?->accessibleTenantIds() ?? [];
            $tenantId = $model->tenant_id ?? null;

            if ($tenantId === null || ! in_array((int) $tenantId, $tenantIds, true)) {
                abort(403, 'You cannot save data for another tenant.');
            }
        });
    }
}
