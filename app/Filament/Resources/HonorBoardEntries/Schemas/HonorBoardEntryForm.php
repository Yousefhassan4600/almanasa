<?php

namespace App\Filament\Resources\HonorBoardEntries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HonorBoardEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('grade_name')
                    ->label('Grade Name'),
                TextInput::make('score_percentage')
                    ->label('Score Percentage')
                    ->numeric(),
                TextInput::make('rank_label')
                    ->label('Rank Label'),
                TextInput::make('image')
                    ->label('Image'),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
