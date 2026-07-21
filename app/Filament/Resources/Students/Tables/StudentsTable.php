<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use App\Models\Account;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class StudentsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'provider',
            'owner.studentProfile.country',
            'owner.studentProfile.city',
            'owner.studentProfile.grade.educationStage',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner()),
            ImageColumn::make('owner.studentProfile.avatar')
                ->label(__('admin.labels.Image')),
            TextColumn::make('owner.name')
                ->label(__('admin.labels.Student'))
                ->searchable(),
            TextColumn::make('owner.phone')
                ->label(__('admin.labels.Phone'))
                ->searchable(),
            TextColumn::make('owner.studentProfile.country_id')
                ->label(__('admin.labels.Location'))
                ->formatStateUsing(function ($state, Account $record) {
                    $country = $record->owner?->studentProfile?->country?->name;
                    $city = $record->owner?->studentProfile?->city?->name;

                    if ($country && $city) {
                        return "{$city}, {$country}";
                    } elseif ($country) {
                        return $country;
                    } elseif ($city) {
                        return $city;
                    }

                    return '-';
                }),
            TextColumn::make('owner.studentProfile.grade.full_name')
                ->label(__('admin.labels.Grade'))
                ->badge()
                ->color('info')
                ->placeholder('-'),
            TextColumn::make('owner.studentProfile.gender')
                ->label(__('admin.labels.Gender'))
                ->badge(),
            TextColumn::make('owner.studentProfile.school_name')
                ->label(__('admin.labels.School'))
                ->wrap(),
            IconColumn::make('is_active')
                ->label(__('admin.labels.Active'))
                ->boolean()
                ->sortable(),
            TextColumn::make('created_at')
                ->label(__('admin.labels.Joined At'))
                ->dateTime()
                ->sortable(),
        ];
    }
}
