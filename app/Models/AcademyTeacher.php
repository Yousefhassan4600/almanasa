<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use App\Support\AdminPermissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AcademyTeacher extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'teacher_account_id',
        'image',
        'experience_years',
        'is_active',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'experience_years' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (AcademyTeacher $academyTeacher): void {
            $academyTeacher->syncSpatieRole();
        });

        static::deleted(function (AcademyTeacher $academyTeacher): void {
            $academyTeacher->removeSpatieRole();
        });
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'teacher_account_id');
    }

    public function gradeSubjectAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacherGradeSubject::class);
    }

    public function accountSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AccountSubject::class, 'academy_teacher_grade_subjects')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }

    public function syncSpatieRole(): void
    {
        $this->loadMissing('teacher.owner');

        if (! $this->provider_id || ! $this->teacher?->owner) {
            return;
        }

        $role = self::academyTeacherRole();

        setPermissionsTeamId($this->provider_id);
        $this->teacher->owner->unsetRelation('roles')->unsetRelation('permissions');

        if (! $this->is_active) {
            $this->teacher->owner->removeRole($role);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return;
        }

        $this->teacher->owner->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function removeSpatieRole(): void
    {
        $this->loadMissing('teacher.owner');

        if (! $this->provider_id || ! $this->teacher?->owner) {
            return;
        }

        $role = Role::query()
            ->whereNull('provider_id')
            ->where('name', AdminPermissions::ACADEMY_TEACHER_ROLE)
            ->first();

        if (! $role) {
            return;
        }

        setPermissionsTeamId($this->provider_id);
        $this->teacher->owner->unsetRelation('roles')->unsetRelation('permissions');
        $this->teacher->owner->removeRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function academyTeacherRole(): Role
    {
        $role = Role::query()->firstOrCreate([
            'provider_id' => null,
            'name' => AdminPermissions::ACADEMY_TEACHER_ROLE,
        ], [
            'guard_name' => 'web',
            'created_by_account_id' => null,
            'is_assignable' => false,
        ]);

        $permissions = collect(AdminPermissions::academyTeacherDefaultPermissionNames())
            ->map(fn (string $permissionName): Permission => Permission::findOrCreate($permissionName, 'web'))
            ->all();

        $role->syncPermissions($permissions);

        return $role;
    }
}
