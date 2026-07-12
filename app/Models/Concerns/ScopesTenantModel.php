<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopesTenantModel
{
    protected static function bootScopesTenantModel(): void
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

            $builder->whereIn($builder->getModel()->qualifyColumn('id'), $tenantIds);
        });

        static::saving(function (object $model): void {
            if (! Auth::guard()->hasUser() || ! $model->exists) {
                return;
            }

            $tenantIds = Auth::user()?->accessibleTenantIds() ?? [];

            if (! in_array((int) $model->getKey(), $tenantIds, true)) {
                abort(403, 'You cannot save data for another tenant.');
            }
        });
    }
}
