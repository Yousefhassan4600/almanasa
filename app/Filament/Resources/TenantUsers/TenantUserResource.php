<?php

namespace App\Filament\Resources\TenantUsers;

use App\Enums\MembershipStatus;
use App\Enums\TenantRole;
use App\Filament\Resources\TenantUsers\Pages\CreateTenantUser;
use App\Filament\Resources\TenantUsers\Pages\EditTenantUser;
use App\Filament\Resources\TenantUsers\Pages\ListTenantUsers;
use App\Filament\Resources\TenantUsers\Pages\ViewTenantUser;
use App\Models\TenantUser;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TenantUserResource extends Resource
{
    protected static ?string $model = TenantUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tenant Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('role')
                    ->options(TenantRole::options())
                    ->required(),
                Select::make('status')
                    ->options(MembershipStatus::options())
                    ->required()
                    ->default(MembershipStatus::Active->value),
                DateTimePicker::make('joined_at'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('role'),
                TextEntry::make('status'),
                TextEntry::make('joined_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('joined_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsers::route('/'),
            'create' => CreateTenantUser::route('/create'),
            'view' => ViewTenantUser::route('/{record}'),
            'edit' => EditTenantUser::route('/{record}/edit'),
        ];
    }
}
