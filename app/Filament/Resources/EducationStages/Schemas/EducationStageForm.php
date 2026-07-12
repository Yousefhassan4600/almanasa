<?php

namespace App\Filament\Resources\EducationStages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EducationStageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
            ]);
    }
}
