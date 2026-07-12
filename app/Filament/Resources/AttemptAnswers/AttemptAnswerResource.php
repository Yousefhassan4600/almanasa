<?php

namespace App\Filament\Resources\AttemptAnswers;

use App\Filament\Resources\AttemptAnswers\Pages\CreateAttemptAnswer;
use App\Filament\Resources\AttemptAnswers\Pages\EditAttemptAnswer;
use App\Filament\Resources\AttemptAnswers\Pages\ListAttemptAnswers;
use App\Filament\Resources\AttemptAnswers\Pages\ViewAttemptAnswer;
use App\Models\AttemptAnswer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AttemptAnswerResource extends Resource
{
    protected static ?string $model = AttemptAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Assessments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assessment_attempt_id')
                    ->relationship('attempt', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Assessment attempt')
                    ->searchable()
                    ->preload()
                    ->required()
                    ,
                Select::make('question_id')
                    ->relationship('question', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Question')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('answer')
                    ->columnSpanFull(),
                Toggle::make('is_correct'),
                TextInput::make('score')
                    ->numeric(),
                Textarea::make('feedback')
                    ->columnSpanFull(),
                DateTimePicker::make('graded_at'),
                Select::make('graded_by')
                    ->relationship('grader', 'name')
                    ->label('Graded by')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('attempt.display_name')
                    ->label('Assessment attempt'),
                TextEntry::make('question.display_name')
                    ->label('Question'),
                TextEntry::make('answer')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_correct')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('feedback')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('graded_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('grader.name')
                    ->label('Graded by')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attempt.display_name')
                    ->label('Assessment attempt')
                    ->sortable(),
                TextColumn::make('question.display_name')
                    ->label('Question')
                    ->searchable(),
                IconColumn::make('is_correct')
                    ->boolean(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('graded_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('grader.name')
                    ->label('Graded by')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttemptAnswers::route('/'),
            'create' => CreateAttemptAnswer::route('/create'),
            'view' => ViewAttemptAnswer::route('/{record}'),
            'edit' => EditAttemptAnswer::route('/{record}/edit'),
        ];
    }
}
