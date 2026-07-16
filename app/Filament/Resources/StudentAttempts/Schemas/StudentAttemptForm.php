<?php

namespace App\Filament\Resources\StudentAttempts\Schemas;

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
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
                TextInput::make('exam_model_id')
                    ->label('Exam Model Id')
                    ->numeric(),
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
                TextInput::make('max_score')
                    ->label('Max Score')
                    ->numeric(),
            ]);
    }
}
