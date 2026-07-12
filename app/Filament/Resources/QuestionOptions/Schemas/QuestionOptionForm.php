<?php

namespace App\Filament\Resources\QuestionOptions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question_id')
                    ->label('Question Id')
                    ->numeric()
                    ->required(),
                Textarea::make('title')
                    ->label('Title')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_correct')
                    ->label('Is Correct'),
            ]);
    }
}
