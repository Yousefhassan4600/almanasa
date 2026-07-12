<?php

namespace App\Filament\Resources\StudentAnswers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StudentAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_attempt_id')
                    ->label('Student Attempt Id')
                    ->numeric()
                    ->required(),
                TextInput::make('question_id')
                    ->label('Question Id')
                    ->numeric()
                    ->required(),
                TextInput::make('question_option_id')
                    ->label('Question Option Id')
                    ->numeric(),
                Textarea::make('answer_text')
                    ->label('Answer Text')
                    ->columnSpanFull(),
                Toggle::make('is_correct')
                    ->label('Is Correct'),
                TextInput::make('score')
                    ->label('Score')
                    ->numeric(),
            ]);
    }
}
