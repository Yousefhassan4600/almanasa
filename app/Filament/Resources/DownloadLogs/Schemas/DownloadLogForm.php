<?php

namespace App\Filament\Resources\DownloadLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DownloadLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('lesson_item_id')
                    ->label('Lesson Item Id')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('downloaded_at')
                    ->label('Downloaded At'),
            ]);
    }
}
