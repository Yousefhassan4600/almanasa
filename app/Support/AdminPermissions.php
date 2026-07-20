<?php

namespace App\Support;

use App\Enums\AccountType;
use App\Enums\AdminPermissionAction;
use App\Filament\Base\BaseResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class AdminPermissions
{
    public const ACADEMY_TEACHER_ROLE = 'academy_teacher';

    private const RESOURCE_ORDER = [
        'roles',
        'employees',
        'academy-teachers',
        'courses',
        'lessons',
        'questions',
        'assignments',
        'exams',
        'students',
        'student-attempts',
        'lesson-progresses',
        'course-reviews',
        'provider-codes',
        'carts',
        'orders',
        'chat-rooms',
        'support-tickets',
        'notifications',
        'audit-logs',
    ];

    private const ACADEMY_TEACHER_RESOURCE_KEYS = [
        'assignments',
        'chat-rooms',
        'course-reviews',
        'courses',
        'exams',
        'lessons',
        'lesson-progresses',
        'questions',
        'student-attempts',
        'students',
    ];

    /**
     * @return array<string, string>
     */
    public static function permissionOptions(): array
    {
        $options = [];

        foreach (self::resourceKeys() as $key => $label) {
            foreach (AdminPermissionAction::cases() as $action) {
                $options[self::permissionName($key, $action)] = "{$label}: {$action->label()}";
            }
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function resourceKeys(): array
    {
        return collect(File::glob(app_path('Filament/Resources/*/*Resource.php')) ?: [])
            ->mapWithKeys(function (string $path): array {
                $class = 'App\\'.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($path, app_path().DIRECTORY_SEPARATOR),
                );

                if (is_subclass_of($class, BaseResource::class) && ! $class::usesAdminPermissions()) {
                    return [];
                }

                $name = Str::of(pathinfo($path, PATHINFO_FILENAME))->beforeLast('Resource');
                $key = $name->kebab()->plural()->toString();
                $label = is_subclass_of($class, BaseResource::class)
                    ? $class::getPluralModelLabel()
                    : $name->headline()->plural()->toString();

                return [$key => $label];
            })
            ->sortBy(fn (string $label, string $key): string => self::resourceSortKey($key))
            ->all();
    }

    private static function resourceSortKey(string $resourceKey): string
    {
        $position = array_search($resourceKey, self::RESOURCE_ORDER, true);

        return $position === false
            ? '999-'.$resourceKey
            : str_pad((string) $position, 3, '0', STR_PAD_LEFT).'-'.$resourceKey;
    }

    public static function permissionNameForResource(string $resourceClass, AdminPermissionAction $action): string
    {
        return self::permissionName(self::resourceKeyForClass($resourceClass), $action);
    }

    public static function resourceKeyForClass(string $resourceClass): string
    {
        return Str::of(class_basename($resourceClass))
            ->beforeLast('Resource')
            ->kebab()
            ->plural()
            ->toString();
    }

    public static function permissionName(string $resourceKey, AdminPermissionAction $action): string
    {
        return "{$resourceKey}.{$action->value}";
    }

    public static function can(string $resourceClass, AdminPermissionAction $action, ?Model $record = null): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        $account = self::currentAccount();

        if (! $account) {
            return false;
        }

        if (self::hasDefaultRoleAccess($account)) {
            return true;
        }

        if (! $account->provider_id) {
            return false;
        }

        setPermissionsTeamId($account->provider_id);
        $user->unsetRelation('roles')->unsetRelation('permissions');

        if (self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, $action))) {
            return true;
        }

        return $action === AdminPermissionAction::ViewAny
            && (
                self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, AdminPermissionAction::ViewAll))
                || self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, AdminPermissionAction::ViewHis))
            );
    }

    public static function hasViewHisOnly(string $resourceClass): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        $account = self::currentAccount();

        if (! $account || self::hasDefaultRoleAccess($account) || ! $account->provider_id) {
            return false;
        }

        setPermissionsTeamId($account->provider_id);
        $user->unsetRelation('roles')->unsetRelation('permissions');

        return self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, AdminPermissionAction::ViewHis))
            && ! self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, AdminPermissionAction::ViewAny))
            && ! self::hasPermission($user, $account, self::permissionNameForResource($resourceClass, AdminPermissionAction::ViewAll));
    }

    public static function hasDefaultRoleAccess(Account $account): bool
    {
        $type = $account->type instanceof AccountType
            ? $account->type
            : AccountType::tryFrom((string) $account->type);

        return in_array($type, [
            AccountType::SaasOwner,
            AccountType::Academy,
            AccountType::StandaloneTeacher,
        ], true) && (int) $account->owner_user_id === (int) auth()->id();
    }

    /**
     * @return array<string>
     */
    public static function academyTeacherDefaultPermissionNames(): array
    {
        return collect(self::ACADEMY_TEACHER_RESOURCE_KEYS)
            ->flatMap(fn (string $resourceKey): array => [
                self::permissionName($resourceKey, AdminPermissionAction::ViewHis),
                self::permissionName($resourceKey, AdminPermissionAction::Create),
                self::permissionName($resourceKey, AdminPermissionAction::Edit),
            ])
            ->all();
    }

    public static function currentAccount(): ?Account
    {
        if (! app()->bound('request')) {
            return null;
        }

        $account = request()->attributes->get('current_account');

        if ($account instanceof Account) {
            return $account;
        }

        if (! request()->hasSession()) {
            return null;
        }

        $accountId = (int) request()->session()->get('current_account_id');

        return $accountId > 0 ? Account::query()->with('provider')->find($accountId) : null;
    }

    private static function employeeRoleAllows(User $user, Account $account, string $permissionName): bool
    {
        $employee = $user->activeEmployees()
            ->where('account_id', $account->id)
            ->with('role.permissions')
            ->first();

        try {
            return (bool) $employee?->role?->hasPermissionTo($permissionName, 'web');
        } catch (PermissionDoesNotExist) {
            return false;
        }
    }

    private static function hasPermission(User $user, Account $account, string $permissionName): bool
    {
        return $user->can($permissionName) || self::employeeRoleAllows($user, $account, $permissionName);
    }
}
