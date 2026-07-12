<?php

namespace App\Filament\Resources\AccountMemberships;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AccountMemberships\Schemas\AccountMembershipForm;
use App\Filament\Resources\AccountMemberships\Tables\AccountMembershipsTable;
use App\Models\AccountMembership;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AccountMembershipResource extends BaseResource
{
    protected static ?string $model = AccountMembership::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return AccountMembershipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountMembershipsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountMemberships::route('/'),
            'create' => Pages\CreateAccountMembership::route('/create'),
            'edit' => Pages\EditAccountMembership::route('/{record}/edit'),
        ];
    }
}
