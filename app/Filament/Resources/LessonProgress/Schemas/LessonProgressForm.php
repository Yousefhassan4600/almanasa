<?php

namespace App\Filament\Resources\LessonProgress\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LessonProgressForm
{
    public static function configure(Schema $schema): Schema
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
}
