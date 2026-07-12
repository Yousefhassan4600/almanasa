<?php

namespace App\Filament\Resources\PackageCourses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PackageCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('package_id')
                    ->label('Package Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
            ]);
    }
}
