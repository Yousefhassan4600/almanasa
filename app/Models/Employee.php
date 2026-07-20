<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\PermissionRegistrar;

class Employee extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'account_id',
        'user_id',
        'predefined_role',
        'role_id',
        'created_by_user_id',
        'is_active',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Employee $employee): void {
            $employee->syncSpatieRole();
        });

        static::deleted(function (Employee $employee): void {
            $employee->removeSpatieRole();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function syncSpatieRole(): void
    {
        $this->loadMissing('account', 'user', 'role');

        if (! $this->account?->provider_id || ! $this->user) {
            return;
        }

        setPermissionsTeamId($this->account->provider_id);
        $this->user->unsetRelation('roles')->unsetRelation('permissions');

        if (! $this->is_active || ! $this->role) {
            $this->user->syncRoles([]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return;
        }

        $this->user->syncRoles([$this->role]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function removeSpatieRole(): void
    {
        $this->loadMissing('account', 'user');

        if (! $this->account?->provider_id || ! $this->user) {
            return;
        }

        setPermissionsTeamId($this->account->provider_id);
        $this->user->unsetRelation('roles')->unsetRelation('permissions');
        $this->user->syncRoles([]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
