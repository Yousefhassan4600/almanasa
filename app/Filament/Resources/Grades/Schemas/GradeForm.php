<?php

namespace App\Filament\Resources\Grades\Schemas;

use App\Models\Subject;
use Filament\Forms\Components\Hidden;
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
                Select::make('subject_ids')
                    ->label('Subjects')
                    ->multiple()
                    ->options(fn () => Subject::query()
                        ->with('track')
                        ->get()
                        ->mapWithKeys(fn (Subject $subject): array => [
                            $subject->id => collect([$subject->name, $subject->track?->name])->filter()->join(' - '),
                        ]))
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
