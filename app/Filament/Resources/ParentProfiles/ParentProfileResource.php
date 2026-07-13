<?php

namespace App\Filament\Resources\ParentProfiles;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ParentProfiles\Schemas\ParentProfileForm;
use App\Filament\Resources\ParentProfiles\Tables\ParentProfilesTable;
use App\Models\ParentProfile;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ParentProfileResource extends BaseResource
{
    protected static ?string $model = ParentProfile::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ParentProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentProfiles::route('/'),
            'create' => Pages\CreateParentProfile::route('/create'),
            'edit' => Pages\EditParentProfile::route('/{record}/edit'),
        ];
    }
}
