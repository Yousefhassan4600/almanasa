<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use App\Models\ExamModel;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'models';

    protected static ?string $title = 'Exam Models';

    protected function makeTable(): Table
    {
        return Table::make($this)
            ->query(fn (): Builder => $this->getOwnerRecord()->courseQuestions())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->heading(static::$title);
    }

    public function getTabs(): array
    {
        return $this->getOwnerRecord()
            ->models()
            ->oldest('model_number')
            ->get()
            ->mapWithKeys(fn (ExamModel $examModel): array => [
                (string) $examModel->getKey() => Tab::make("Model {$examModel->model_number}")
                    ->badge(count($examModel->question_ids ?? []))
                    ->modifyQueryUsing(fn (Builder $query): Builder => $this->scopeQueryToExamModel($query, $examModel)),
            ])
            ->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Model Questions')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lesson.course.title')
                    ->label('Course')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lesson.title')
                    ->label('Lesson')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label('Difficulty')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private function scopeQueryToExamModel(Builder $query, ExamModel $examModel): Builder
    {
        $questionIds = collect($examModel->question_ids ?? [])
            ->filter()
            ->map(fn ($questionId): int => (int) $questionId)
            ->values();

        if ($questionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $orderSql = $questionIds
            ->map(fn (int $questionId, int $index): string => "when {$questionId} then {$index}")
            ->implode(' ');

        return $query
            ->whereIn('id', $questionIds)
            ->orderByRaw("case id {$orderSql} end");
    }
}
