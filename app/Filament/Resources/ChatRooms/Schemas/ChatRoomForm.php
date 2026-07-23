<?php

namespace App\Filament\Resources\ChatRooms\Schemas;

use App\Filament\Support\CurrentAccount;
use App\Models\Course;
use App\Models\Lesson;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ChatRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CurrentAccount::providerTextInput(TextInput::make('provider_id'))
                    ->label(__('admin.labels.Provider Id'))
                    ->numeric()
                    ->required(),
                Select::make('course_id')
                    ->label(__('admin.labels.Course'))
                    ->options(fn (Get $get): array => Course::query()
                        ->with('provider')
                        ->when($get('provider_id'), fn ($query, int $providerId) => $query->where('provider_id', $providerId))
                        ->tap(fn ($query) => CurrentAccount::scopeCoursesToCurrentAccount($query))
                        ->get()
                        ->mapWithKeys(fn (Course $course): array => [
                            $course->id => collect([$course->title, $course->provider?->name])->filter()->join(' - '),
                        ])
                        ->all())
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('lesson_id', null))
                    ->searchable()
                    ->preload(),
                Select::make('lesson_id')
                    ->label(__('admin.labels.Lesson'))
                    ->options(fn (Get $get): array => Lesson::query()
                        ->when($get('course_id'), fn ($query, int $courseId) => $query->where('course_id', $courseId))
                        ->when(blank($get('course_id')), fn ($query) => $query->whereRaw('1 = 0'))
                        ->tap(fn ($query) => CurrentAccount::scopeLessonsToCurrentAccount($query))
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (Lesson $lesson): array => [
                            $lesson->id => $lesson->title,
                        ])
                        ->all())
                    ->disabled(fn (Get $get): bool => blank($get('course_id')))
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label(__('admin.labels.Title'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('admin.labels.Is Active')),
            ]);
    }
}
