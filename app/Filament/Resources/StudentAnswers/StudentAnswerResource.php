<?php

namespace App\Filament\Resources\StudentAnswers;

use App\Filament\Resources\StudentAnswers\Pages\ManageStudentAnswers;
use App\Models\StudentAnswer;
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

class StudentAnswerResource extends Resource
{
    protected static ?string $model = StudentAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_attempt_id')
                    ->label('Student Attempt Id')
                    ->numeric()
                    ->required(),
                TextInput::make('question_id')
                    ->label('Question Id')
                    ->numeric()
                    ->required(),
                TextInput::make('question_option_id')
                    ->label('Question Option Id')
                    ->numeric(),
                Textarea::make('answer_text')
                    ->label('Answer Text')
                    ->columnSpanFull(),
                Toggle::make('is_correct')
                    ->label('Is Correct'),
                TextInput::make('score')
                    ->label('Score')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_attempt_id')
                    ->label('Student Attempt Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question_id')
                    ->label('Question Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question_option_id')
                    ->label('Question Option Id')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_correct')
                    ->label('Is Correct')
                    ->boolean(),
                TextColumn::make('score')
                    ->label('Score')
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
            'index' => ManageStudentAnswers::route('/'),
        ];
    }
}
