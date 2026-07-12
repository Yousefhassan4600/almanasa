<?php

namespace App\Filament\Resources\ParentStudents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ParentStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent_user_id')
                    ->label('Parent User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('relation')
                    ->label('Relation'),
                Toggle::make('is_primary')
                    ->label('Is Primary'),
            ]);
    }
}
