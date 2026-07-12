<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
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
                Toggle::make('allow_retake')
                    ->label('Allow Retake'),
                TextInput::make('max_attempts')
                    ->label('Max Attempts')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(ContentStatus::options())
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
            ]);
    }
}
