<?php

namespace App\Filament\Resources\ProviderPlans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required(),
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)'),
                        Textarea::make('description.en')
                            ->label('Description (English)'),
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),
                    ]),
                Section::make('Features & Options')
                    ->schema([
                        TextInput::make('max_students')
                            ->label('Max Students')
                            ->numeric(),
                        TextInput::make('max_courses')
                            ->label('Max Courses')
                            ->numeric(),
                        TextInput::make('max_teachers')
                            ->label('Max Teachers')
                            ->numeric(),
                        RichEditor::make('features')
                            ->label('Features')
                            ->columnSpanFull(),
                    ]),

                Repeater::make('options')
                    ->label('Plan Options')
                    ->relationship()
                    ->schema([
                        TextInput::make('billing_period_days')
                            ->label('Billing Period Days')
                            ->numeric()
                            ->required(),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->orderColumn('sort_order')
                    ->columnSpanFull(),
            ]);
    }
}
