<?php

namespace App\Filament\Resources\CourseReviews\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
                TextInput::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->required(),
                Textarea::make('comment')
                    ->label('Comment')
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Is Approved'),
            ]);
    }
}
