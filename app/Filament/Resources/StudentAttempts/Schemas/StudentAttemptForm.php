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
                    ->label(__('admin.labels.Student User Id'))
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label(__('admin.labels.Course Id'))
                    ->numeric()
                    ->required(),
                TextInput::make('exam_model_id')
                    ->label(__('admin.labels.Exam Model Id'))
                    ->numeric(),
                TextInput::make('attemptable_type')
                    ->label(__('admin.labels.Attemptable Type'))
                    ->required(),
                TextInput::make('attemptable_id')
                    ->label(__('admin.labels.Attemptable Id'))
                    ->numeric()
                    ->required(),
                TextInput::make('attempt_number')
                    ->label(__('admin.labels.Attempt Number'))
                    ->numeric()
                    ->required(),
                TextInput::make('max_score')
                    ->label(__('admin.labels.Max Score'))
                    ->numeric(),
            ]);
    }
}
