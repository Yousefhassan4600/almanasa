<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use App\Models\Role;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
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
