<?php

namespace App\Filament\Resources\Providers\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Providers\RelationManagers\Tables\ProviderPaymentMethodsTable;
use App\Models\ProviderPaymentMethod;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProviderPaymentMethodsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'providerPaymentMethods';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_method_id')
                    ->label(__('admin.labels.Payment Method'))
                    ->relationship('paymentMethod', 'name')
                    ->preload()
                    ->searchable()
                    ->rules([
                        fn (?ProviderPaymentMethod $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                            if (blank($value)) {
                                return;
                            }

                            $paymentMethodExists = ProviderPaymentMethod::query()
                                ->where('provider_id', $this->getOwnerRecord()->getKey())
                                ->where('payment_method_id', $value)
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($paymentMethodExists) {
                                $fail(__('admin.messages.payment_method_already_configured'));
                            }
                        },
                    ])
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('account_number')
                    ->label(__('admin.labels.Account Number')),
                TextInput::make('account_holder')
                    ->label(__('admin.labels.Account Holder')),
                TextInput::make('phone_number')
                    ->label(__('admin.labels.Phone Number'))
                    ->tel(),
                TextInput::make('phone_holder')
                    ->label(__('admin.labels.Phone Holder')),
            ]);
    }

    public function table(Table $table): Table
    {
        return ProviderPaymentMethodsTable::configure($table)
            ->heading('Payment Methods')
            ->recordTitleAttribute('payment_method_id')
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }

    public function getTableFilters(): array
    {
        return [];
    }
}
