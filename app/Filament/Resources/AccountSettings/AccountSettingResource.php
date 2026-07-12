<?php

namespace App\Filament\Resources\AccountSettings;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AccountSettings\Schemas\AccountSettingForm;
use App\Filament\Resources\AccountSettings\Tables\AccountSettingsTable;
use App\Models\AccountSetting;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AccountSettingResource extends BaseResource
{
    protected static ?string $model = AccountSetting::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AccountSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountSettings::route('/'),
            'create' => Pages\CreateAccountSetting::route('/create'),
            'edit' => Pages\EditAccountSetting::route('/{record}/edit'),
        ];
    }
}
