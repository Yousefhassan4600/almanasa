<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\AccountType;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Support\CurrentAccount;
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
                    ->label(__('admin.labels.Type'))
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
                                $fail(__('admin.messages.account_type_provider_already_exists'));
                            }
                        },
                    ])
                    ->required(),
                CurrentAccount::providerSelect(Select::make('provider_id'))
                    ->label(__('admin.labels.Provider'))
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable(),
                DateTimePicker::make('approved_at')
                    ->label(__('admin.labels.Approved At'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('admin.labels.Is Active'))
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
                    ->label(__('admin.labels.#'))
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->label(__('admin.labels.Provider'))
                    ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('admin.labels.Type'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('admin.labels.Is Active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(__('admin.labels.Approved At'))
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
