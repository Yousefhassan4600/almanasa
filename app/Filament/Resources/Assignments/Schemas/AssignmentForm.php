<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Models\Course;
use App\Models\Lesson;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignment Details')
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->options(fn(): array => self::courseOptions())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn(Set $set): mixed => $set('lesson_ids', [])),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required(),
                        Select::make('lesson_ids')
                            ->label('Lessons')
                            ->multiple()
                            ->options(fn(Get $get): array => self::lessonOptions($get('course_id')))
                            ->searchable()
                            ->preload()
                            ->helperText('Optional. If selected, assignment questions will be randomized only from these lessons.')
                            ->columnSpanFull(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)'),
                        Textarea::make('description.en')
                            ->label('Description (English)'),
                    ])
                    ->columns(1),
                Section::make('Questions & Date Details')
                    ->schema([
                        TextInput::make('num_of_questions')
                            ->label('Number Of Questions')
                            ->numeric()
                            ->integer()
                            ->required()
                            ->default(10)
                            ->minValue(0),
                        Section::make('Questions Difficulty')
                            ->schema([
                                TextInput::make('num_of_easy_questions')
                                    ->label('Easy Questions')
                                    ->numeric()
                                    ->integer()
                                    ->default(5)
                                    ->minValue(0),
                                TextInput::make('num_of_medium_questions')
                                    ->label('Medium Questions')
                                    ->numeric()
                                    ->integer()
                                    ->default(3)
                                    ->minValue(0),
                                TextInput::make('num_of_hard_questions')
                                    ->label('Hard Questions')
                                    ->numeric()
                                    ->integer()
                                    ->default(2)
                                    ->minValue(0),
                            ])
                            ->columns(3),
                        TextInput::make('duration_minutes')
                            ->label('Duration Minutes')
                            ->numeric()
                            ->integer()
                            ->minValue(0),
                        TextInput::make('num_of_attempts')
                            ->label('Number of Attempts')
                            ->numeric()
                            ->integer(),
                    ])
                    ->columns(1),
            ])
            ->columns(2);
    }

    private static function courseOptions(): array
    {
        return Course::query()
            ->with(['provider'])
            ->get()
            ->mapWithKeys(fn(Course $course): array => [
                $course->id => collect([$course->title, $course->provider?->name])->filter()->join(' - '),
            ])
            ->all();
    }

    private static function lessonOptions(null|int|string $courseId): array
    {
        if (blank($courseId)) {
            return [];
        }

        return Lesson::query()
            ->where('course_id', $courseId)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn(Lesson $lesson): array => [
                $lesson->id => $lesson->title,
            ])
            ->all();
    }
}
