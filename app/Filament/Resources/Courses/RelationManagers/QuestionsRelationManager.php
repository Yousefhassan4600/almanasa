<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Filament\Actions\ImportQuestionsFromExcelAction;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use App\Models\Lesson;
use App\Models\Question;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class QuestionsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Questions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->questionFormComponents(useRelationshipRepeater: true))
            ->columns(2);
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

    public function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('primary')
                ->schema($this->questionFormComponents(useRelationshipRepeater: false))
                ->action(function (array $data): void {
                    $options = $data['options'] ?? [];
                    unset($data['options']);

                    $question = Question::query()->create($data);

                    foreach ($options as $sortOrder => $option) {
                        $question->options()->create([
                            ...$option,
                            'sort_order' => $sortOrder + 1,
                        ]);
                    }
                }),
            ImportQuestionsFromExcelAction::make(courseId: $this->getOwnerRecord()->getKey()),
        ];
    }

    public function getTableFilters(): array
    {
        return [];
    }

    /**
     * @return array<int, Component>
     */
    private function questionFormComponents(bool $useRelationshipRepeater): array
    {
        $optionsRepeater = Repeater::make('options')
            ->label('Options')
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
            ->columnSpanFull();

        if ($useRelationshipRepeater) {
            $optionsRepeater->relationship();
        }

        return [
            Select::make('lesson_id')
                ->label('Lesson')
                ->options(fn(): array => Lesson::query()
                    ->where('course_id', $this->getOwnerRecord()->getKey())
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(fn(Lesson $lesson): array => [
                        $lesson->id => $lesson->title,
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
                ->directory('questions/media')
                ->columnSpanFull(),
            $optionsRepeater,
        ];
    }
}
