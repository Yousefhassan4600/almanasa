<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class QuestionsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('questionable_type')
                ->label('Questionable Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('questionable_id')
                ->label('Questionable Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('points')
                ->label('Points')
                ->searchable()
                ->sortable(),
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

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
