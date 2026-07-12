<?php

namespace App\Filament\Resources\LessonProgress;

use App\Enums\AttendanceStatus;
use App\Filament\Resources\LessonProgress\Pages\ManageLessonProgress;
use App\Models\LessonProgress;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LessonProgressResource extends Resource
{
    protected static ?string $model = LessonProgress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
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
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(AttendanceStatus::options())
                    ->required(),
                TextInput::make('watched_seconds')
                    ->label('Watched Seconds')
                    ->numeric()
                    ->required(),
                TextInput::make('required_seconds')
                    ->label('Required Seconds')
                    ->numeric(),
                TextInput::make('completion_percentage')
                    ->label('Completion Percentage')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('completed_at')
                    ->label('Completed At'),
                DateTimePicker::make('last_watched_at')
                    ->label('Last Watched At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('watched_seconds')
                    ->label('Watched Seconds')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('required_seconds')
                    ->label('Required Seconds')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('completion_percentage')
                    ->label('Completion Percentage')
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
            'index' => ManageLessonProgress::route('/'),
        ];
    }
}
