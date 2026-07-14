<?php

namespace App\Filament\Resources\ProviderPlans\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProviderPlansTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table);
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('options')
                ->label('Options')
                ->html()
                ->getStateUsing(function ($record) {
                    if ($record->options->isEmpty()) {
                        return '────';
                    }

                    return new HtmlString(
                        "<div class='flex flex-wrap gap-1'>" .
                            $record->options->map(function ($option) {
                                return "<span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200'>" . e($option->billing_period_days ?? '') . ' days - ' . e($option->price ?? '') . ' EGP</span>';
                            })->implode(' <br /> ') .
                            '</div>'
                    );
                }),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean(),
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
