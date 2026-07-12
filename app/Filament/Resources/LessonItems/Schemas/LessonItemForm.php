<?php

namespace App\Filament\Resources\LessonItems\Schemas;

use App\Enums\LessonItemType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lesson_id')
                    ->label('Lesson Id')
                    ->numeric()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(LessonItemType::options())
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('video_url')
                    ->label('Video Url'),
                TextInput::make('file_url')
                    ->label('File Url'),
                TextInput::make('duration_seconds')
                    ->label('Duration Seconds')
                    ->numeric(),
                TextInput::make('assignment_id')
                    ->label('Assignment Id')
                    ->numeric(),
                TextInput::make('exam_id')
                    ->label('Exam Id')
                    ->numeric(),
                Toggle::make('is_required')
                    ->label('Is Required'),
            ]);
    }
}
