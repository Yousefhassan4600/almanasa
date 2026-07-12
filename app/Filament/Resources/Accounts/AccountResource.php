<?php

namespace App\Filament\Resources\Accounts;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Filament\Resources\Accounts\Pages\ManageAccounts;
use App\Models\Account;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(AccountType::options())
                    ->required(),
                TextInput::make('owner_user_id')
                    ->label('Owner User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('parent_account_id')
                    ->label('Parent Account Id')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                TextInput::make('subdomain')
                    ->label('Subdomain'),
                TextInput::make('custom_domain')
                    ->label('Custom Domain'),
                TextInput::make('logo')
                    ->label('Logo'),
                TextInput::make('cover_image')
                    ->label('Cover Image'),
                Textarea::make('bio')
                    ->label('Bio')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label('Phone'),
                TextInput::make('email')
                    ->label('Email'),
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric(),
                TextInput::make('city_id')
                    ->label('City Id')
                    ->numeric(),
                Textarea::make('address')
                    ->label('Address')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(AccountStatus::options())
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label('Approved At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('owner_user_id')
                    ->label('Owner User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subdomain')
                    ->label('Subdomain')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('custom_domain')
                    ->label('Custom Domain')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('logo')
                    ->label('Logo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cover_image')
                    ->label('Cover Image')
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
            'index' => ManageAccounts::route('/'),
        ];
    }
}
