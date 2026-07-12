<?php

namespace App\Filament\Resources\AcademyTeachers\Schemas;

use App\Enums\AccountStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AcademyTeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('academy_account_id')
                    ->label('Academy Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(AccountStatus::options())
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
            ]);
    }
}
