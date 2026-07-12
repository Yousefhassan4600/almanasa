<?php

namespace App\Filament\Resources\QuestionOptions;

use App\Filament\Resources\QuestionOptions\Pages\ManageQuestionOptions;
use App\Models\QuestionOption;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class QuestionOptionResource extends Resource
{
    protected static ?string $model = QuestionOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question_id')
                    ->label('Question Id')
                    ->numeric()
                    ->required(),
                Textarea::make('title')
                    ->label('Title')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_correct')
                    ->label('Is Correct'),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question_id')
                    ->label('Question Id')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_correct')
                    ->label('Is Correct')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
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
            'index' => ManageQuestionOptions::route('/'),
        ];
    }
}
