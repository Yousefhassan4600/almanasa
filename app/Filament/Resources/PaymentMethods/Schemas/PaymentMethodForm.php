<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use App\Enums\PaymentMethodSlugs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name.ar')
                    ->label('Name (Arabic)')
                    ->required(),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required(),
                Select::make('slug')
                    ->label('Slug')
                    ->options([
                        PaymentMethodSlugs::Bank->value => 'Bank Transfer',
                        PaymentMethodSlugs::InstaPay->value => 'InstaPay',
                        PaymentMethodSlugs::VodafoneCash->value => 'Vodafone Cash',
                        PaymentMethodSlugs::OrangeCash->value => 'Orange Cash',
                        PaymentMethodSlugs::ECash->value => 'e& Cash',
                        PaymentMethodSlugs::Code->value => 'Code',
                    ])
                    ->unique(ignoreRecord: true)
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->directory('payment-methods')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                Toggle::make('is_bank')
                    ->label('Is Bank')
                    ->default(false),
                Toggle::make('require_proof')
                    ->label('Require Proof')
                    ->default(false),
                Toggle::make('is_code')
                    ->label('Is Code')
                    ->default(false),
            ]);
    }
}
