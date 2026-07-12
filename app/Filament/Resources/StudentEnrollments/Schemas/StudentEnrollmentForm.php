<?php

namespace App\Filament\Resources\StudentEnrollments\Schemas;

use App\Enums\EnrollmentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentEnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric(),
                TextInput::make('package_id')
                    ->label('Package Id')
                    ->numeric(),
                TextInput::make('subscription_id')
                    ->label('Subscription Id')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(EnrollmentStatus::options())
                    ->required(),
                DateTimePicker::make('started_at')
                    ->label('Started At'),
                DateTimePicker::make('expires_at')
                    ->label('Expires At'),
            ]);
    }
}
