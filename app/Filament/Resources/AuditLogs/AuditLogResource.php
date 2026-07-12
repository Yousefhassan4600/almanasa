<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ManageAuditLogs;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric(),
                TextInput::make('action')
                    ->label('Action')
                    ->required(),
                TextInput::make('auditable_type')
                    ->label('Auditable Type'),
                TextInput::make('auditable_id')
                    ->label('Auditable Id')
                    ->numeric(),
                Textarea::make('old_values')
                    ->label('Old Values')
                    ->columnSpanFull(),
                Textarea::make('new_values')
                    ->label('New Values')
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('Ip Address'),
                Textarea::make('user_agent')
                    ->label('User Agent')
                    ->columnSpanFull(),
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
                TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Auditable Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_id')
                    ->label('Auditable Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('Ip Address')
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
            'index' => ManageAuditLogs::route('/'),
        ];
    }
}
