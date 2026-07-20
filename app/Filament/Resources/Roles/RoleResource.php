<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use App\Filament\Support\CurrentAccount;
use App\Models\Role;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class RoleResource extends BaseResource
{
    protected static ?string $model = Role::class;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Accounts';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function canEdit(Model $record): bool
    {
        if ($record instanceof Role && RoleForm::isAcademyTeacherSystemRole($record)) {
            return CurrentAccount::isSaasOwner();
        }

        return parent::canEdit($record);
    }

    public static function canDelete(Model $record): bool
    {
        if ($record instanceof Role && RoleForm::isAcademyTeacherSystemRole($record)) {
            return false;
        }

        return parent::canDelete($record);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canRestore(Model $record): bool
    {
        return ! ($record instanceof Role && RoleForm::isAcademyTeacherSystemRole($record)) && parent::canRestore($record);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
