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
                Section::make(__('admin.labels.Basic Information'))
                    ->schema([
                        TextInput::make('name.ar')
                            ->label(__('admin.labels.Name (Arabic)'))
                            ->required(),
                        TextInput::make('name.en')
                            ->label(__('admin.labels.Name (English)'))
                            ->required(),
                        Textarea::make('description.ar')
                            ->label(__('admin.labels.Description (Arabic)')),
                        Textarea::make('description.en')
                            ->label(__('admin.labels.Description (English)')),
                        Toggle::make('is_active')
                            ->label(__('admin.labels.Is Active'))
                            ->default(true),
                    ]),
                Section::make(__('admin.labels.Features & Options'))
                    ->schema([
                        TextInput::make('max_students')
                            ->label(__('admin.labels.Max Students'))
                            ->numeric(),
                        TextInput::make('max_courses')
                            ->label(__('admin.labels.Max Courses'))
                            ->numeric(),
                        TextInput::make('max_teachers')
                            ->label(__('admin.labels.Max Teachers'))
                            ->numeric(),
                        RichEditor::make('features')
                            ->label(__('admin.labels.Features'))
                            ->columnSpanFull(),
                    ]),

                Repeater::make('options')
                    ->label(__('admin.labels.Plan Options'))
                    ->relationship()
                    ->schema([
                        TextInput::make('billing_period_days')
                            ->label(__('admin.labels.Billing Period Days'))
                            ->numeric()
                            ->required(),
                        TextInput::make('price')
                            ->label(__('admin.labels.Price'))
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
