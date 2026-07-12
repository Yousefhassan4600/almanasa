<?php

namespace App\Filament\Resources\AccountSettings;

use App\Filament\Resources\AccountSettings\Pages\ManageAccountSettings;
use App\Models\AccountSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AccountSettingResource extends Resource
{
    protected static ?string $model = AccountSetting::class;

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
                TextInput::make('primary_color')
                    ->label('Primary Color'),
                TextInput::make('secondary_color')
                    ->label('Secondary Color'),
                Toggle::make('website_enabled')
                    ->label('Website Enabled'),
                Toggle::make('registration_enabled')
                    ->label('Registration Enabled'),
                Toggle::make('chat_enabled')
                    ->label('Chat Enabled'),
                Toggle::make('payment_enabled')
                    ->label('Payment Enabled'),
                TextInput::make('tax_percentage')
                    ->label('Tax Percentage')
                    ->numeric()
                    ->required(),
                TextInput::make('completion_watch_percentage')
                    ->label('Completion Watch Percentage')
                    ->numeric()
                    ->required(),
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
                TextColumn::make('primary_color')
                    ->label('Primary Color')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('secondary_color')
                    ->label('Secondary Color')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('website_enabled')
                    ->label('Website Enabled')
                    ->boolean(),
                IconColumn::make('registration_enabled')
                    ->label('Registration Enabled')
                    ->boolean(),
                IconColumn::make('chat_enabled')
                    ->label('Chat Enabled')
                    ->boolean(),
                IconColumn::make('payment_enabled')
                    ->label('Payment Enabled')
                    ->boolean(),
                TextColumn::make('tax_percentage')
                    ->label('Tax Percentage')
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
            'index' => ManageAccountSettings::route('/'),
        ];
    }
}
