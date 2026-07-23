<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Filament\Support\CurrentAccount;
use App\Models\Course;
use App\Models\Lesson;
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
                Section::make(__('admin.labels.Assignment Details'))
                    ->schema([
                        Select::make('course_id')
                            ->label(__('admin.labels.Course'))
                            ->options(fn (): array => self::courseOptions())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn (Set $set): mixed => $set('lesson_ids', [])),
                        TextInput::make('title.ar')
                            ->label(__('admin.labels.Title (Arabic)'))
                            ->required(),
                        TextInput::make('title.en')
                            ->label(__('admin.labels.Title (English)'))
                            ->required(),
                        Select::make('lesson_ids')
                            ->label(__('admin.labels.Lessons'))
                            ->multiple()
                            ->options(fn (Get $get): array => self::lessonOptions($get('course_id')))
                            ->searchable()
                            ->preload()
                            ->helperText('Optional. If selected, exam model questions will be randomized only from these lessons.')
                            ->columnSpanFull(),
                        Textarea::make('description.ar')
                            ->label(__('admin.labels.Description (Arabic)')),
                        Textarea::make('description.en')
                            ->label(__('admin.labels.Description (English)')),
                    ])
                    ->columns(1),
                Section::make(__('admin.labels.Questions & Date Details'))
                    ->schema([
                        TextInput::make('num_of_questions')
                            ->label(__('admin.labels.Number Of Questions'))
                            ->numeric()
                            ->integer()
                            ->required()
                            ->columnSpanFull()
                            ->default(10)
                            ->minValue(0),
                        Section::make(__('admin.labels.Questions Difficulty'))
                            ->schema([
                                TextInput::make('num_of_easy_questions')
                                    ->label(__('admin.labels.Easy Questions'))
                                    ->numeric()
                                    ->integer()
                                    ->default(5)
                                    ->minValue(0),
                                TextInput::make('num_of_medium_questions')
                                    ->label(__('admin.labels.Medium Questions'))
                                    ->numeric()
                                    ->integer()
                                    ->default(3)
                                    ->minValue(0),
                                TextInput::make('num_of_hard_questions')
                                    ->label(__('admin.labels.Hard Questions'))
                                    ->numeric()
                                    ->integer()
                                    ->default(2)
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        Section::make(__('admin.labels.Max Degree, Models & Duration'))
                            ->schema([
                                TextInput::make('max_degree')
                                    ->label(__('admin.labels.Max Degree'))
                                    ->numeric()
                                    ->required()
                                    ->default(20)
                                    ->minValue(0),
                                TextInput::make('num_of_models')
                                    ->label(__('admin.labels.Number Of Models'))
                                    ->numeric()
                                    ->integer()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),
                                TextInput::make('duration_minutes')
                                    ->label(__('admin.labels.Duration Minutes'))
                                    ->numeric()
                                    ->integer()
                                    ->default(10)
                                    ->required()
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        TextInput::make('num_of_attempts')
                            ->label(__('admin.labels.Number of Attempts'))
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
            ->tap(fn ($query) => CurrentAccount::scopeCoursesToCurrentAccount($query))
            ->get()
            ->mapWithKeys(fn (Course $course): array => [
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
            ->tap(fn ($query) => CurrentAccount::scopeLessonsToCurrentAccount($query))
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Lesson $lesson): array => [
                $lesson->id => $lesson->title,
            ])
            ->all();
    }
}
