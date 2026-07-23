<?php

namespace App\Filament\Resources\Grades\Schemas;

use App\Models\Subject;
use App\Models\Track;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('education_stage_id')
                    ->dehydrated(true),
                Hidden::make('name.ar')
                    ->dehydrated(true),
                Hidden::make('name.en')
                    ->dehydrated(true),
                Repeater::make('gradeSubjects')
                    ->label(__('admin.labels.Grade Subjects'))
                    ->relationship()
                    ->schema([
                        Select::make('track_id')
                            ->label(__('admin.labels.Track'))
                            ->options(fn (): array => Track::query()
                                ->orderBy('sort_order')
                                ->pluck('name', 'id')
                                ->all())
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('subject_id')
                            ->label(__('admin.labels.Subject'))
                            ->options(fn (): array => Subject::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }
}
