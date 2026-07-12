<?php

namespace App\Filament\Resources\Exams;

use App\Enums\ContentStatus;
use App\Filament\Resources\Exams\Pages\ManageExams;
use App\Models\Exam;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
                TextInput::make('lesson_id')
                    ->label('Lesson Id')
                    ->numeric(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('duration_minutes')
                    ->label('Duration Minutes')
                    ->numeric(),
                TextInput::make('max_score')
                    ->label('Max Score')
                    ->numeric()
                    ->required(),
                TextInput::make('pass_score')
                    ->label('Pass Score')
                    ->numeric(),
                TextInput::make('max_attempts')
                    ->label('Max Attempts')
                    ->numeric()
                    ->required(),
                Toggle::make('stop_on_page_leave')
                    ->label('Stop On Page Leave'),
                Select::make('status')
                    ->label('Status')
                    ->options(ContentStatus::options())
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_id')
                    ->label('Course Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lesson_id')
                    ->label('Lesson Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Duration Minutes')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label('Max Score')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pass_score')
                    ->label('Pass Score')
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
            'index' => ManageExams::route('/'),
        ];
    }
}
