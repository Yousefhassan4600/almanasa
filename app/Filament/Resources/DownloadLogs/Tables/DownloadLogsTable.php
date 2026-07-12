<?php

namespace App\Filament\Resources\DownloadLogs\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class DownloadLogsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('lesson_item_id')
                ->label('Lesson Item Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('downloaded_at')
                ->label('Downloaded At')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
