<?php

namespace App\Concerns;

use App\Enums\AccountType;
use App\Models\Account;
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

        return $this->applyTenantAccountScope($query, $account);
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

        return Account::query()->find($accountId);
    }

    protected function applyTenantAccountScope(Builder $query, Account $account): Builder
    {
        $model = $query->getModel();
        $accountIds = $this->tenantAccountIds($account);

        if ($model instanceof Account) {
            return $query->where(function (Builder $query) use ($accountIds): void {
                $query
                    ->whereIn($query->getModel()->qualifyColumn('id'), $accountIds)
                    ->orWhereIn($query->getModel()->qualifyColumn('parent_account_id'), $accountIds);
            });
        }

        $query->where(function (Builder $query) use ($account, $accountIds, $model): void {
            $hasDirectScope = false;

            foreach ($this->tenantScopeColumns($model) as $column) {
                $hasDirectScope = true;
                $ids = $column === 'teacher_account_id' ? [$account->id] : $accountIds;
                $query->orWhereIn($model->qualifyColumn($column), $ids);
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

    /**
     * @return array<int, string>
     */
    protected function tenantScopeColumns(Model $model): array
    {
        $columns = [
            'account_id',
            'academy_account_id',
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

    /**
     * @return array<int, int>
     */
    protected function tenantAccountIds(Account $account): array
    {
        return Account::query()
            ->whereKey($account->id)
            ->orWhere('parent_account_id', $account->id)
            ->pluck('id')
            ->map(fn (int|string $id): int => (int) $id)
            ->all();
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
