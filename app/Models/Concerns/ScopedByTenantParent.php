<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByTenantParent
{
    protected static function bootScopedByTenantParent(): void
    {
        static::addGlobalScope('tenantAccess', function (Builder $builder): void {
            if (! Auth::guard()->hasUser()) {
                return;
            }

            if ((Auth::user()?->accessibleTenantIds() ?? []) === []) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->whereHas(static::tenantParentRelation());
        });

        static::saving(function (object $model): void {
            if (! Auth::guard()->hasUser()) {
                return;
            }

            $relation = static::tenantParentRelation();

            if (! $model->{$relation}()->exists()) {
                abort(403, 'You cannot save data for another tenant.');
            }
        });
    }

    abstract protected static function tenantParentRelation(): string;
}
