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
                    ->label('Track')
                    ->relationship('track', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('name.ar')
                    ->label('Name (Arabic)')
                    ->required(),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required(),
                FileUpload::make('icon')
                    ->label('Icon')
                    ->image()
                    ->directory('subjects/icons'),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->directory('subjects/images'),
                Textarea::make('description.ar')
                    ->label('Description (Arabic)')
                    ->columnSpanFull(),
                Textarea::make('description.en')
                    ->label('Description (English)')
                    ->columnSpanFull(),
                Select::make('grade_ids')
                    ->label('Grades')
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
