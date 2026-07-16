<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Lesson;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lesson_id')
                    ->label('Lesson')
                    ->options(fn (): array => Lesson::query()
                        ->with(['course'])
                        ->get()
                        ->mapWithKeys(fn (Lesson $lesson): array => [
                            $lesson->id => collect([$lesson->course?->title, $lesson->title])->filter()->join(' - '),
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(QuestionType::options())
                    ->required(),
                Select::make('difficulty')
                    ->label('Difficulty')
                    ->options(QuestionDifficulty::options())
                    ->required(),
                Textarea::make('title')
                    ->label('Title')
                    ->required(),
                FileUpload::make('media')
                    ->label('Media')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('questions/media'),
                Repeater::make('options')
                    ->label('Options')
                    ->relationship()
                    ->schema([
                        Textarea::make('title')
                            ->label('Title')
                            ->required(),
                        FileUpload::make('media')
                            ->label('Media')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('questions/options')
                            ->columnSpanFull(),
                        Toggle::make('is_correct')
                            ->label('Correct')
                            ->default(false),
                    ])
                    ->columns(1)
                    ->grid(4)
                    ->orderColumn('sort_order')
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
