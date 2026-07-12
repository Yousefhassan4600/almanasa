<?php

namespace App\Filament\Resources\AssessmentAttempts;

use App\Enums\AssessmentAttemptStatus;
use App\Filament\Resources\AssessmentAttempts\Pages\CreateAssessmentAttempt;
use App\Filament\Resources\AssessmentAttempts\Pages\EditAssessmentAttempt;
use App\Filament\Resources\AssessmentAttempts\Pages\ListAssessmentAttempts;
use App\Filament\Resources\AssessmentAttempts\Pages\ViewAssessmentAttempt;
use App\Models\AssessmentAttempt;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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

class AssessmentAttemptResource extends Resource
{
    protected static ?string $model = AssessmentAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Assessments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('assessment_id')
                    ->relationship('assessment', 'title')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                TextInput::make('attempt_number')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('expires_at'),
                TextInput::make('time_spent_seconds')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('score')
                    ->numeric(),
                TextInput::make('percentage')
                    ->numeric(),
                Toggle::make('is_passed'),
                Select::make('status')
                    ->options(AssessmentAttemptStatus::options())
                    ->required()
                    ->default(AssessmentAttemptStatus::InProgress->value),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('assessment.title')
                    ->label('Assessment'),
                TextEntry::make('student.name')
                    ->label('Student'),
                TextEntry::make('attempt_number')
                    ->numeric(),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('submitted_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('time_spent_seconds')
                    ->numeric(),
                TextEntry::make('score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('percentage')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_passed')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('status'),
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
                TextColumn::make('assessment.title')
                    ->searchable(),
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('attempt_number')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('time_spent_seconds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('percentage')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_passed')
                    ->boolean(),
                TextColumn::make('status')
                    ->searchable(),
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
            'index' => ListAssessmentAttempts::route('/'),
            'create' => CreateAssessmentAttempt::route('/create'),
            'view' => ViewAssessmentAttempt::route('/{record}'),
            'edit' => EditAssessmentAttempt::route('/{record}/edit'),
        ];
    }
}
