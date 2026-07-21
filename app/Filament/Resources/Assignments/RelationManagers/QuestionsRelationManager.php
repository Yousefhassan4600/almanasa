<?php

namespace App\Filament\Resources\Assignments\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'selectedQuestions';

    protected static ?string $title = 'Questions';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    protected function makeTable(): Table
    {
        return Table::make($this)
            ->query(fn (): Builder => $this->getOwnerRecord()->selectedQuestions())
            ->heading(static::$title);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Questions')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('lesson.course'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('admin.labels.#'))
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('admin.labels.Title'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lesson.course.title')
                    ->label(__('admin.labels.Course'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lesson.title')
                    ->label(__('admin.labels.Lesson'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label(__('admin.labels.Type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label(__('admin.labels.Difficulty'))
                    ->badge()
                    ->sortable(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
