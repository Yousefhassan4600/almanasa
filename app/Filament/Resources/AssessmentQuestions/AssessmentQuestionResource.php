<?php

namespace App\Filament\Resources\AssessmentQuestions;

use App\Filament\Resources\AssessmentQuestions\Pages\CreateAssessmentQuestion;
use App\Filament\Resources\AssessmentQuestions\Pages\EditAssessmentQuestion;
use App\Filament\Resources\AssessmentQuestions\Pages\ListAssessmentQuestions;
use App\Filament\Resources\AssessmentQuestions\Pages\ViewAssessmentQuestion;
use App\Models\AssessmentQuestion;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AssessmentQuestionResource extends Resource
{
    protected static ?string $model = AssessmentQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Assessments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assessment_id')
                    ->relationship('assessment', 'title')
                    ->required(),
                Select::make('question_id')
                    ->relationship('question', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Question')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('score')
                    ->numeric(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('assessment.title')
                    ->label('Assessment'),
                TextEntry::make('question.display_name')
                    ->label('Question'),
                TextEntry::make('score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
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
                TextColumn::make('assessment.title')
                    ->searchable(),
                TextColumn::make('question.display_name')
                    ->label('Question')
                    ->searchable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->numeric()
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
            'index' => ListAssessmentQuestions::route('/'),
            'create' => CreateAssessmentQuestion::route('/create'),
            'view' => ViewAssessmentQuestion::route('/{record}'),
            'edit' => EditAssessmentQuestion::route('/{record}/edit'),
        ];
    }
}
