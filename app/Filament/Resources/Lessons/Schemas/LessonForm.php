<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Filament\Support\CurrentAccount;
use App\Models\Course;
use App\Models\CoursePeriod;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label(__('admin.labels.Course'))
                    ->options(fn (): array => Course::query()
                        ->with(['provider', 'academyTeacher.teacher.owner'])
                        ->when(CurrentAccount::isAcademyTeacher(), fn ($query) => CurrentAccount::scopeCoursesToAcademyTeacher($query))
                        ->get()
                        ->mapWithKeys(fn (Course $course): array => [
                            $course->id => $course->title.' - '.$course->provider?->name.' - '.$course->academyTeacher?->teacher?->owner?->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('course_period_id')
                    ->label(__('admin.labels.Course Period'))
                    ->options(fn (): array => CoursePeriod::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (CoursePeriod $coursePeriod): array => [
                            $coursePeriod->id => $coursePeriod->name,
                        ])
                        ->all())
                    ->searchable()
                    ->required()
                    ->preload(),
                TextInput::make('title.ar')
                    ->label(__('admin.labels.Title (Arabic)'))
                    ->required(),
                TextInput::make('title.en')
                    ->label(__('admin.labels.Title (English)'))
                    ->required(),
                Textarea::make('description.ar')
                    ->label(__('admin.labels.Description (Arabic)')),
                Textarea::make('description.en')
                    ->label(__('admin.labels.Description (English)')),
                DateTimePicker::make('starts_at')
                    ->label(__('admin.labels.Starts At')),
                DateTimePicker::make('ends_at')
                    ->label(__('admin.labels.Ends At')),
                TextInput::make('num_of_video_views')
                    ->label(__('admin.labels.Number Of Video Views'))
                    ->numeric()
                    ->integer()
                    ->default(1)
                    ->minValue(0)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('admin.labels.Is Active'))
                    ->default(true),
            ])->columns(2);
    }
}
