<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\AccountType;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\Account;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OwnedAccountsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'ownedAccounts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(AccountType::options())
                    ->rules([
                        fn (Get $get, ?Account $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            $accountExists = Account::query()
                                ->where('owner_user_id', $this->getOwnerRecord()->getKey())
                                ->where('type', $value)
                                ->when(
                                    filled($providerId),
                                    fn ($query) => $query->where('provider_id', $providerId),
                                    fn ($query) => $query->whereNull('provider_id'),
                                )
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($accountExists) {
                                $fail('This user already has an account with the same type and provider.');
                            }
                        },
                    ])
                    ->required(),
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable(),
                DateTimePicker::make('approved_at')
                    ->label('Approved At')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters($this->getTableFilters())
            ->headerActions($this->getTableHeaderActions())
            ->recordActions($this->getTableActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTableActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getTableHeaderActions(): array
    {
        return [];
    }
}
