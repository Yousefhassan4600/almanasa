<?php

namespace App\Filament\Resources\GradeSubjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GradeSubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_id')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->required(),
                Select::make('track_id')
                    ->relationship('track', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }
}
