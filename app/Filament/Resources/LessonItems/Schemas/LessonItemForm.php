<?php

namespace App\Filament\Resources\LessonItems\Schemas;

use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Lesson;
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
                Select::make('lesson_id')
                    ->label('Lesson')
                    ->options(fn (): array => Lesson::query()
                        ->with('course')
                        ->get()
                        ->mapWithKeys(fn (Lesson $lesson): array => [
                            $lesson->id => $lesson->title.' - '.$lesson->course?->title,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                Textarea::make('description.ar')
                    ->label('Description (Arabic)')
                    ->columnSpanFull(),
                Textarea::make('description.en')
                    ->label('Description (English)')
                    ->columnSpanFull(),
                TextInput::make('video_url')
                    ->label('Video Url'),
                TextInput::make('file_url')
                    ->label('File Url'),
                TextInput::make('link_url')
                    ->label('Link Url'),
                TextInput::make('duration_minutes')
                    ->label('Duration Minutes')
                    ->numeric(),
                Select::make('assignment_id')
                    ->label('Assignment')
                    ->options(fn (): array => Assignment::query()->pluck('title', 'id')->all())
                    ->searchable()
                    ->preload(),
                Select::make('exam_id')
                    ->label('Exam')
                    ->options(fn (): array => Exam::query()->pluck('title', 'id')->all())
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                Toggle::make('is_free')
                    ->label('Is Free')
                    ->default(false),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ])->columns(2);
    }
}
