<?php

namespace App\Filament\Base;

use App\Concerns\FiltersByTenant;
use App\Enums\AdminPermissionAction;
use App\Filament\Support\CurrentAccount;
use App\Support\AdminPermissions;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use UnitEnum;

abstract class BaseResource extends Resource
{
    use FiltersByTenant;

    protected static bool $isScopedToTenant = false;

    protected static bool $isSaasOwnerOnly = false;

    public static function getModelLabel(): string
    {
        return __('admin.resources.'.static::resourceTranslationKey().'.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.'.static::resourceTranslationKey().'.plural');
    }

    public static function getNavigationLabel(): string
    {
        return static::getPluralModelLabel();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return parent::getNavigationGroup();
    }

    protected static function resourceTranslationKey(): string
    {
        return str(class_basename(static::class))
            ->beforeLast('Resource')
            ->toString();
    }

    public static function usesAdminPermissions(): bool
    {
        return ! static::$isSaasOwnerOnly;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::$isSaasOwnerOnly || ! AdminPermissions::hasViewHisOnly(static::class)) {
            return $query;
        }

        return static::scopeQueryToCurrentAccountRecords($query);
    }

    public static function canViewAny(): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        $account = CurrentAccount::account();

        if ($account && AdminPermissions::hasViewHisOnly(static::class)) {
            return static::supportsCurrentAccountRecordScope();
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::ViewAny);
    }

    public static function canCreate(): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::Create);
    }

    public static function canView(Model $record): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        if (AdminPermissions::hasViewHisOnly(static::class)) {
            return static::recordBelongsToCurrentAccountScope($record);
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::ViewAny, $record);
    }

    public static function canEdit(Model $record): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        if (AdminPermissions::hasViewHisOnly(static::class) && ! static::recordBelongsToCurrentAccountScope($record)) {
            return false;
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::Edit, $record);
    }

    public static function canDelete(Model $record): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        if (AdminPermissions::hasViewHisOnly(static::class) && ! static::recordBelongsToCurrentAccountScope($record)) {
            return false;
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::Delete, $record);
    }

    public static function canDeleteAny(): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::Delete);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canDeleteAny();
    }

    public static function canRestore(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canRestoreAny(): bool
    {
        return static::canDeleteAny();
    }

    public static function canReorder(): bool
    {
        if (static::$isSaasOwnerOnly) {
            return CurrentAccount::isSaasOwner();
        }

        return AdminPermissions::can(static::class, AdminPermissionAction::Edit);
    }

    protected static function scopeQueryToCurrentAccountRecords(Builder $query): Builder
    {
        $account = CurrentAccount::account();

        if (! $account) {
            return $query->whereRaw('1 = 0');
        }

        $model = $query->getModel();
        $table = $model->getTable();
        $scoped = false;

        return $query->where(function (Builder $query) use ($account, $model, $table, &$scoped): void {
            if (Schema::hasColumn($table, 'teacher_account_id')) {
                $scoped = true;
                $query->orWhere($model->qualifyColumn('teacher_account_id'), $account->id);
            }

            if (Schema::hasColumn($table, 'created_by_account_id')) {
                $scoped = true;
                $query->orWhere($model->qualifyColumn('created_by_account_id'), $account->id);
            }

            if (Schema::hasColumn($table, 'academy_teacher_id') && method_exists($model, 'academyTeacher')) {
                $scoped = true;
                $query->orWhereHas('academyTeacher', fn (Builder $query): Builder => $query
                    ->where('teacher_account_id', $account->id));
            }

            if (Schema::hasColumn($table, 'course_id') && method_exists($model, 'course')) {
                $scoped = true;
                $query->orWhereHas('course', fn (Builder $query): Builder => static::scopeCourseQueryToAccount($query, $account->id));
            }

            if (Schema::hasColumn($table, 'lesson_id') && method_exists($model, 'lesson')) {
                $scoped = true;
                $query->orWhereHas('lesson', fn (Builder $query): Builder => $query
                    ->whereHas('course', fn (Builder $query): Builder => static::scopeCourseQueryToAccount($query, $account->id)));
            }

            if (! $scoped) {
                $query->whereRaw('1 = 0');
            }
        });
    }

    protected static function scopeCourseQueryToAccount(Builder $query, int $accountId): Builder
    {
        return $query->whereHas('academyTeacher', fn (Builder $query): Builder => $query
            ->where('teacher_account_id', $accountId));
    }

    protected static function recordBelongsToCurrentAccountScope(Model $record): bool
    {
        return static::getEloquentQuery()
            ->whereKey($record->getKey())
            ->exists();
    }

    protected static function supportsCurrentAccountRecordScope(): bool
    {
        $modelClass = static::getModel();

        if (! class_exists($modelClass)) {
            return false;
        }

        /** @var Model $model */
        $model = app($modelClass);
        $table = $model->getTable();

        return Schema::hasColumn($table, 'teacher_account_id')
            || Schema::hasColumn($table, 'created_by_account_id')
            || (Schema::hasColumn($table, 'academy_teacher_id') && method_exists($model, 'academyTeacher'))
            || (Schema::hasColumn($table, 'course_id') && method_exists($model, 'course'))
            || (Schema::hasColumn($table, 'lesson_id') && method_exists($model, 'lesson'));
    }
}
