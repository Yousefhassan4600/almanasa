<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
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
}
