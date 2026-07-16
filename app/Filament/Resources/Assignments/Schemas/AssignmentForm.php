<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Models\Course;
use App\Models\Question;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('Course')
                    ->options(fn (): array => self::courseOptions())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                Textarea::make('description.ar')
                    ->label('Description (Arabic)'),
                Textarea::make('description.en')
                    ->label('Description (English)'),
                TextInput::make('num_of_questions')
                    ->label('Number Of Questions')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('num_of_easy_questions')
                    ->label('Easy Questions')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('num_of_medium_questions')
                    ->label('Medium Questions')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('num_of_hard_questions')
                    ->label('Hard Questions')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('duration_minutes')
                    ->label('Duration Minutes')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                DateTimePicker::make('starts_at')
                    ->label('Starts At'),
                Toggle::make('is_today_only')
                    ->label('Today Only')
                    ->default(false),
                Select::make('question_ids')
                    ->label('Questions')
                    ->multiple()
                    ->options(fn (Get $get): array => self::questionOptions($get('course_id')))
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function courseOptions(): array
    {
        return Course::query()
            ->with(['provider'])
            ->get()
            ->mapWithKeys(fn (Course $course): array => [
                $course->id => collect([$course->title, $course->provider?->name])->filter()->join(' - '),
            ])
            ->all();
    }

    private static function questionOptions(null|int|string $courseId): array
    {
        if (blank($courseId)) {
            return [];
        }

        return Question::query()
            ->whereHas('lesson', fn ($query) => $query->where('course_id', $courseId))
            ->with(['lesson'])
            ->get()
            ->mapWithKeys(fn (Question $question): array => [
                $question->id => collect([$question->lesson?->title, $question->title])->filter()->join(' - '),
            ])
            ->all();
    }
}
