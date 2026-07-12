<?php

namespace App\Filament\Resources\AccountMemberships;

use App\Enums\AccountMemberRole;
use App\Filament\Resources\AccountMemberships\Pages\ManageAccountMemberships;
use App\Models\AccountMembership;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AccountMembershipResource extends Resource
{
    protected static ?string $model = AccountMembership::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                Select::make('predefined_role')
                    ->label('Predefined Role')
                    ->options(AccountMemberRole::options())
                    ->required(),
                TextInput::make('role_id')
                    ->label('Custom Role Id')
                    ->numeric(),
                TextInput::make('created_by_user_id')
                    ->label('Created By User Id')
                    ->numeric(),
                TextInput::make('status')
                    ->label('Status')
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label('User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('predefined_role')
                    ->label('Predefined Role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role_id')
                    ->label('Custom Role Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccountMemberships::route('/'),
        ];
    }
}
