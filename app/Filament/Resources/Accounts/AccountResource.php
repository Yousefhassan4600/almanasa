<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Accounts\Schemas\AccountForm;
use App\Filament\Resources\Accounts\Tables\AccountsTable;
use App\Models\Account;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AccountResource extends BaseResource
{
    protected static ?string $model = Account::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
