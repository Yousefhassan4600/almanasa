<?php

namespace App\Filament\Resources\LessonProgress\Tables;

use App\Filament\Base\BaseTable;
use App\Models\LessonProgress;
use App\Models\LessonProgressStatusType;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

class LessonProgressTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),
            TextColumn::make('student.name')
                ->label(__('admin.labels.Student'))
                ->searchable(),
            TextColumn::make('course.title')
                ->label(__('admin.labels.Course'))
                ->searchable(),
            TextColumn::make('lesson.title')
                ->label(__('admin.labels.Lesson'))
                ->searchable(),
            TextColumn::make('currentStatus.type.name')
                ->label(__('admin.labels.Status'))
                ->badge()
                ->color(fn (LessonProgress $record): string => $this->statusBadgeColor($record))
                ->placeholder('-'),
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
            Action::make('logs')
                ->label('')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading(__('admin.labels.Status History'))
                ->modalContent(fn (LessonProgress $record): View => view('filament.resources.lesson-progress.status-logs-modal', [
                    'statusLogs' => $record->statuses()
                        ->with(['type', 'createdBy'])
                        ->latest('status_at')
                        ->latest()
                        ->get(),
                ])),
        ];
    }

    private function statusBadgeColor(LessonProgress $record): string
    {
        $sortOrder = $record->currentStatus?->type?->sort_order;

        if ($sortOrder === null) {
            return 'gray';
        }

        [$firstSortOrder, $lastSortOrder] = $this->statusTypeSortBounds();

        return match ($sortOrder) {
            $firstSortOrder => 'warning',
            $lastSortOrder => 'success',
            default => 'info',
        };
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function statusTypeSortBounds(): array
    {
        static $bounds = null;

        if ($bounds !== null) {
            return $bounds;
        }

        $sortOrders = LessonProgressStatusType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('sort_order');

        return $bounds = [
            $sortOrders->first(),
            $sortOrders->last(),
        ];
    }
}
