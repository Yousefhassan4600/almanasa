<?php

namespace App\Filament\Resources\ProviderCodes\Schemas;

use App\Models\ProviderCode;
use App\Models\PurchaseUnit;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProviderCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->live()
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('purchase_unit_id')
                    ->label('Purchase Unit')
                    ->options(fn (): array => PurchaseUnit::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (PurchaseUnit $purchaseUnit): array => [
                            $purchaseUnit->id => $purchaseUnit->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->label('Code')
                    ->maxLength(255)
                    ->rules([
                        fn (Get $get, ?ProviderCode $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($providerId) || blank($value)) {
                                return;
                            }

                            $exists = ProviderCode::query()
                                ->where('provider_id', $providerId)
                                ->where('code', $value)
                                ->when($record?->exists, fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($exists) {
                                $fail('This provider already has a code with the same value.');
                            }
                        },
                    ])
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->native(false),
                TextInput::make('num_of_uses')
                    ->label('Number Of Uses')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
            ])
            ->columns(2);
    }
}
