<?php

namespace App\Filament\Resources\LessonProgress\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\LessonProgress\LessonProgressResource;
use App\Models\LessonProgressStatusType;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLessonProgress extends BaseListRecords
{
    protected static string $resource = LessonProgressResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        LessonProgressStatusType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (LessonProgressStatusType $statusType) use (&$tabs): void {
                $tabs[$statusType->slug] = Tab::make($statusType->name)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas(
                        'statuses',
                        fn (Builder $query): Builder => $query
                            ->where('lesson_progress_status_type_id', $statusType->id)
                            ->where('is_current', true)
                    ));
            });

        return $tabs;
    }
}
