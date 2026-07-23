<?php

namespace App\Filament\Resources\Grades\Tables;

use App\Filament\Base\BaseTable;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

class GradesTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'educationStage',
            'gradeSubjects.track',
            'gradeSubjects.subject',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),

            TextColumn::make('name')
                ->label(__('admin.labels.Name')),

            TextColumn::make('educationStage.name')
                ->label(__('admin.labels.Education Stage')),

            TextColumn::make('gradeSubjects')
                ->label(__('admin.labels.Subjects'))
                ->wrap()
                ->getStateUsing(function ($record) {
                    if ($record->gradeSubjects->isEmpty()) {
                        return '────';
                    }

                    return new HtmlString(
                        "<div class='flex flex-wrap gap-1'>".
                            $record->gradeSubjects->map(function ($gradeSubject) {
                                return "<span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200'>".e($gradeSubject->full_name ?? '').'</span>';
                            })->implode('<br />').
                            '</div>'
                    );
                })
                ->html(),

        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return false;
    }

    protected function extraRecordActions(): array
    {
        return [
            EditAction::make()
                ->label(''),
        ];
    }
}
