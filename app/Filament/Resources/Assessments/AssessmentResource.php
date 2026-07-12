<?php

namespace App\Filament\Resources\Assessments;

use App\Enums\AssessmentType;
use App\Filament\Resources\Assessments\Pages\CreateAssessment;
use App\Filament\Resources\Assessments\Pages\EditAssessment;
use App\Filament\Resources\Assessments\Pages\ListAssessments;
use App\Filament\Resources\Assessments\Pages\ViewAssessment;
use App\Models\Assessment;
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

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Assessments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                Select::make('lesson_id')
                    ->relationship('lesson', 'title'),
                Select::make('type')
                    ->options(AssessmentType::options())
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('instructions')
                    ->columnSpanFull(),
                TextInput::make('duration_minutes')
                    ->numeric(),
                TextInput::make('total_score')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('passing_score')
                    ->numeric(),
                TextInput::make('max_attempts')
                    ->numeric(),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Toggle::make('shuffle_questions')
                    ->required(),
                Toggle::make('shuffle_options')
                    ->required(),
                Toggle::make('show_result_immediately')
                    ->required(),
                Toggle::make('show_correct_answers')
                    ->required(),
                Toggle::make('show_explanations')
                    ->required(),
                Toggle::make('allow_retry')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('course.title')
                    ->label('Course'),
                TextEntry::make('lesson.title')
                    ->label('Lesson')
                    ->placeholder('-'),
                TextEntry::make('type'),
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('instructions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('duration_minutes')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total_score')
                    ->numeric(),
                TextEntry::make('passing_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_attempts')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('starts_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('ends_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('shuffle_questions')
                    ->boolean(),
                IconEntry::make('shuffle_options')
                    ->boolean(),
                IconEntry::make('show_result_immediately')
                    ->boolean(),
                IconEntry::make('show_correct_answers')
                    ->boolean(),
                IconEntry::make('show_explanations')
                    ->boolean(),
                IconEntry::make('allow_retry')
                    ->boolean(),
                IconEntry::make('is_published')
                    ->boolean(),
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
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('course.title')
                    ->searchable(),
                TextColumn::make('lesson.title')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('passing_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_attempts')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('shuffle_questions')
                    ->boolean(),
                IconColumn::make('shuffle_options')
                    ->boolean(),
                IconColumn::make('show_result_immediately')
                    ->boolean(),
                IconColumn::make('show_correct_answers')
                    ->boolean(),
                IconColumn::make('show_explanations')
                    ->boolean(),
                IconColumn::make('allow_retry')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->boolean(),
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
            'index' => ListAssessments::route('/'),
            'create' => CreateAssessment::route('/create'),
            'view' => ViewAssessment::route('/{record}'),
            'edit' => EditAssessment::route('/{record}/edit'),
        ];
    }
}
