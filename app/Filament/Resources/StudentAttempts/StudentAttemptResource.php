<?php

namespace App\Filament\Resources\StudentAttempts;

use App\Enums\AttemptStatus;
use App\Filament\Resources\StudentAttempts\Pages\ManageStudentAttempts;
use App\Models\StudentAttempt;
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

class StudentAttemptResource extends Resource
{
    protected static ?string $model = StudentAttempt::class;

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
                TextInput::make('attemptable_type')
                    ->label('Attemptable Type')
                    ->required(),
                TextInput::make('attemptable_id')
                    ->label('Attemptable Id')
                    ->numeric()
                    ->required(),
                TextInput::make('attempt_number')
                    ->label('Attempt Number')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(AttemptStatus::options())
                    ->required(),
                TextInput::make('score')
                    ->label('Score')
                    ->numeric(),
                TextInput::make('max_score')
                    ->label('Max Score')
                    ->numeric(),
                TextInput::make('percentage')
                    ->label('Percentage')
                    ->numeric(),
                DateTimePicker::make('started_at')
                    ->label('Started At'),
                DateTimePicker::make('submitted_at')
                    ->label('Submitted At'),
                DateTimePicker::make('graded_at')
                    ->label('Graded At'),
                TextInput::make('time_spent_seconds')
                    ->label('Time Spent Seconds')
                    ->numeric(),
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
                TextColumn::make('attemptable_type')
                    ->label('Attemptable Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('attemptable_id')
                    ->label('Attemptable Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('attempt_number')
                    ->label('Attempt Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
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
            'index' => ManageStudentAttempts::route('/'),
        ];
    }
}
