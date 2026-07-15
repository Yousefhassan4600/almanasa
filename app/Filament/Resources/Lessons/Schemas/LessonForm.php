<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Models\Course;
use App\Models\CoursePeriod;
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
                    ->label('Course')
                    ->options(fn (): array => Course::query()
                        ->with(['provider'])
                        ->get()
                        ->mapWithKeys(fn (Course $course): array => [
                            $course->id => $course->title.' - '.$course->provider?->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('course_period_id')
                    ->label('Course Period')
                    ->options(fn (): array => CoursePeriod::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (CoursePeriod $coursePeriod): array => [
                            $coursePeriod->id => $coursePeriod->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload(),
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
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ])->columns(2);
    }
}
