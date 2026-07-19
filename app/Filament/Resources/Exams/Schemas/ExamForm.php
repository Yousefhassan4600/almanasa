<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Models\Course;
use App\Models\Lesson;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ExamForm
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
                            ->helperText('Optional. If selected, exam model questions will be randomized only from these lessons.')
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
                            ->columnSpanFull()
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
                            ->columns(3)
                            ->columnSpanFull(),
                        Section::make('Max Degree, Models & Duration')
                            ->schema([
                                TextInput::make('max_degree')
                                    ->label('Max Degree')
                                    ->numeric()
                                    ->required()
                                    ->default(20)
                                    ->minValue(0),
                                TextInput::make('num_of_models')
                                    ->label('Number Of Models')
                                    ->numeric()
                                    ->integer()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),
                                TextInput::make('duration_minutes')
                                    ->label('Duration Minutes')
                                    ->numeric()
                                    ->integer()
                                    ->default(10)
                                    ->required()
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        TextInput::make('num_of_attempts')
                            ->label('Number of Attempts')
                            ->numeric()
                            ->integer()
                            ->default(1)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
