<?php

namespace App\Filament\Resources\StudentAttempts\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\StudentAttempts\StudentAttemptResource;
use App\Models\AttemptStatusType;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStudentAttempts extends BaseListRecords
{
    protected static string $resource = StudentAttemptResource::class;

    public function hasCreateAction(): bool
    {
        return false;
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        AttemptStatusType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (AttemptStatusType $statusType) use (&$tabs): void {
                $tabs[$statusType->slug] = Tab::make($statusType->name)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas(
                        'statuses',
                        fn (Builder $query): Builder => $query
                            ->where('attempt_status_type_id', $statusType->id)
                            ->where('is_current', true)
                    ));
            });

        return $tabs;
    }
}
