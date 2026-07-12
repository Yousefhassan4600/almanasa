<?php

namespace App\Filament\Resources\CourseOutcomes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseOutcomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
            ]);
    }
}
