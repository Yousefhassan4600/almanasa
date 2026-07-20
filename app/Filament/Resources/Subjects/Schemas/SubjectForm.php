<?php

namespace App\Filament\Resources\Subjects\Schemas;

use App\Models\Grade;
use Filament\Forms\Components\FileUpload;
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
                Select::make('track_id')
                    ->label(__('admin.labels.Track'))
                    ->relationship('track', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('name.ar')
                    ->label(__('admin.labels.Name (Arabic)'))
                    ->required(),
                TextInput::make('name.en')
                    ->label(__('admin.labels.Name (English)'))
                    ->required(),
                FileUpload::make('icon')
                    ->label(__('admin.labels.Icon'))
                    ->image()
                    ->directory('subjects/icons'),
                FileUpload::make('image')
                    ->label(__('admin.labels.Image'))
                    ->image()
                    ->directory('subjects/images'),
                Textarea::make('description.ar')
                    ->label(__('admin.labels.Description (Arabic)'))
                    ->columnSpanFull(),
                Textarea::make('description.en')
                    ->label(__('admin.labels.Description (English)'))
                    ->columnSpanFull(),
                Select::make('grade_ids')
                    ->label(__('admin.labels.Grades'))
                    ->multiple()
                    ->options(fn () => Grade::query()
                        ->with('educationStage')
                        ->get()
                        ->mapWithKeys(fn (Grade $grade): array => [
                            $grade->id => collect([$grade->educationStage?->name, $grade->name])->filter()->join(' - '),
                        ]))
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
