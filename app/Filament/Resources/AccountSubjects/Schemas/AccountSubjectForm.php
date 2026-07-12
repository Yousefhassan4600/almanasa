<?php

namespace App\Filament\Resources\AccountSubjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountSubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                Select::make('grade_subject_id')
                    ->relationship('gradeSubject', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                    ->searchable()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
