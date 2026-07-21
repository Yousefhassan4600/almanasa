<?php

namespace App\Filament\Resources\Subjects\Tables;

use App\Filament\Base\BaseTable;
use App\Models\Subject;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

class SubjectsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'track',
            'gradeSubjects.grade.educationStage',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),
            ImageColumn::make('icon')
                ->label(__('admin.labels.Icon')),
            ImageColumn::make('image')
                ->label(__('admin.labels.Image')),
            TextColumn::make('track.name')
                ->label(__('admin.labels.Track')),
            TextColumn::make('name')
                ->label(__('admin.labels.Name')),
            TextColumn::make('description')
                ->label(__('admin.labels.Description'))
                ->wrap(),
            TextColumn::make('gradeSubjects')
                ->label(__('admin.labels.Grades'))
                ->wrap()
                ->getStateUsing(function ($record) {
                    if ($record->gradeSubjects->isEmpty()) {
                        return '────';
                    }

                    return new HtmlString(
                        "<div class='flex flex-wrap gap-1'>".
                            $record->gradeSubjects->map(function ($gradeSubject) {
                                return "<span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200'>".e($gradeSubject->grade?->full_name ?? '').'</span>';
                            })->implode('<br />').
                            '</div>'
                    );
                })
                ->html(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
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
                ->label('')
                ->mutateRecordDataUsing(function (array $data, Subject $record): array {
                    $data['grade_ids'] = $record->gradeSubjects()
                        ->pluck('grade_id')
                        ->all();

                    return $data;
                })
                ->using(function (Subject $record, array $data): Subject {
                    $gradeIds = $data['grade_ids'] ?? [];

                    unset($data['grade_ids']);

                    $record->update($data);
                    $record->syncGrades($gradeIds);

                    return $record;
                }),
        ];
    }
}
