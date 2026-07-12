<?php

namespace App\Filament\Resources\StudentAttempts\Schemas;

use App\Enums\AttemptStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('provider_id')
                    ->label('Provider Id')
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
}
