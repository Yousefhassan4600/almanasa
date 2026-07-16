<?php

namespace App\Filament\Resources\Lessons\RelationManagers;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Filament\Actions\ImportQuestionsFromExcelAction;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class QuestionsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Questions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->directory('questions/media')
                    ->columnSpanFull(),
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
                    ->grid(2)
                    ->orderColumn('sort_order')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return QuestionsTable::configure($table)
            ->heading('Questions')
            ->recordTitleAttribute('title')
            ->headerActions($this->getTableHeaderActions())
            ->filters([])
            ->recordActions($this->getTableActions());
    }

    public function getTableFilters(): array
    {
        return [];
    }

    protected function extraHeaderActions(): array
    {
        return [
            ImportQuestionsFromExcelAction::make(lessonId: $this->getOwnerRecord()->getKey()),
        ];
    }
}
