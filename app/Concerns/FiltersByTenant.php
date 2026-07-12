<?php

namespace App\Concerns;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait FiltersByTenant
{
    /** @var array<class-string, array<string, bool>> */
    private static array $tenantColumnCache = [];

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (method_exists($query->getModel(), 'scopeForCurrentTenant')) {
            $query->forCurrentTenant();
        }

        return $query;
    }

    public function scopeForCurrentTenant(Builder $query, ?Account $account = null): Builder
    {
        $account ??= $this->currentTenantAccount();

        if (! $account || $this->isSaasOwnerTenant($account)) {
            return $query;
        }

        return $this->applyTenantProviderScope($query, $account);
    }

    protected function currentTenantAccount(): ?Account
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = request();
        $account = $request->attributes->get('current_account');

        if ($account instanceof Account) {
            return $account;
        }

        if (! $request->hasSession()) {
            return null;
        }

        $accountId = (int) $request->session()->get('current_account_id');

        if ($accountId <= 0) {
            return null;
        }

        return Account::query()->with('provider')->find($accountId);
    }

    protected function applyTenantProviderScope(Builder $query, Account $account): Builder
    {
        $model = $query->getModel();
        $provider = $this->currentTenantProvider($account);

        if ($model instanceof Account) {
            return $this->applyTenantAccountScope($query, $account, $provider);
        }

        if ($model instanceof Provider) {
            return $provider
                ? $query->whereKey($provider->id)
                : $query->whereRaw('1 = 0');
        }

        $query->where(function (Builder $query) use ($account, $provider, $model): void {
            $hasDirectScope = false;

            foreach ($this->tenantScopeColumns($model) as $column) {
                $hasDirectScope = true;
                $ids = $column === 'teacher_account_id'
                    ? [$account->id]
                    : array_filter([$provider?->id]);

                if ($ids !== []) {
                    $query->orWhereIn($model->qualifyColumn($column), $ids);
                } elseif ($column === 'provider_id') {
                    $query->orWhereRaw('1 = 0');
                }
            }

            foreach ($this->tenantScopeRelations() as $relation) {
                $hasDirectScope = true;
                $query->orWhereHas($relation, function (Builder $query) use ($account): void {
                    if (method_exists($query->getModel(), 'scopeForCurrentTenant')) {
                        $query->forCurrentTenant($account);
                    }
                });
            }

            if (! $hasDirectScope) {
                $query->whereRaw('1 = 1');
            }
        });

        return $query;
    }

    protected function currentTenantProvider(Account $account): ?Provider
    {
        if ($account->relationLoaded('provider')) {
            return $account->provider;
        }

        return $account->provider()->first();
    }

    protected function applyTenantAccountScope(Builder $query, Account $account, ?Provider $provider): Builder
    {
        return $query->where(function (Builder $query) use ($account, $provider): void {
            $query->whereKey($account->id);

            if ($provider) {
                $query->orWhere($query->getModel()->qualifyColumn('provider_id'), $provider->id);
            }
        });
    }

    /**
     * @return array<int, string>
     */
    protected function tenantScopeColumns(Model $model): array
    {
        $columns = [
            'provider_id',
            'teacher_account_id',
        ];

        return array_values(array_filter(
            $columns,
            fn (string $column): bool => $this->hasTenantColumn($model, $column),
        ));
    }

    /**
     * @return array<int, string>
     */
    protected function tenantScopeRelations(): array
    {
        return property_exists($this, 'tenantRelations') ? $this->tenantRelations : [];
    }

    protected function isSaasOwnerTenant(Account $account): bool
    {
        $type = $account->type instanceof AccountType
            ? $account->type
            : AccountType::tryFrom((string) $account->type);

        return $type === AccountType::SaasOwner;
    }

    protected function hasTenantColumn(Model $model, string $column): bool
    {
        $class = $model::class;

        if (! array_key_exists($class, self::$tenantColumnCache)) {
            self::$tenantColumnCache[$class] = [];
        }

        if (! array_key_exists($column, self::$tenantColumnCache[$class])) {
            self::$tenantColumnCache[$class][$column] = Schema::hasColumn($model->getTable(), $column);
        }

        return self::$tenantColumnCache[$class][$column];
    }
}
