<?php

namespace App\Filament\Resources\Subjects\Schemas;

use App\Models\Grade;
use App\Models\Track;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('admin.labels.Name'))
                    ->required(),
                FileUpload::make('icon')
                    ->label(__('admin.labels.Icon'))
                    ->image()
                    ->directory('subjects/icons'),
                FileUpload::make('image')
                    ->label(__('admin.labels.Image'))
                    ->image()
                    ->directory('subjects/images'),
                Textarea::make('description')
                    ->label(__('admin.labels.Description'))
                    ->columnSpanFull(),
                Repeater::make('gradeSubjects')
                    ->label(__('admin.labels.Grade Subjects'))
                    ->relationship()
                    ->schema([
                        Select::make('grade_id')
                            ->label(__('admin.labels.Grade'))
                            ->options(fn (): array => Grade::query()
                                ->with('educationStage')
                                ->orderBy('sort_order')
                                ->get()
                                ->mapWithKeys(fn (Grade $grade): array => [
                                    $grade->id => $grade->full_name,
                                ])
                                ->all())
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('track_id')
                            ->label(__('admin.labels.Track'))
                            ->options(fn (): array => Track::query()
                                ->orderBy('sort_order')
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
