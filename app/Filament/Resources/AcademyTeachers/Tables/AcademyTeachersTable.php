<?php

namespace App\Filament\Resources\AcademyTeachers\Tables;

use App\Enums\AccountType;
use App\Filament\Base\BaseTable;
use App\Models\AcademyTeacher;
use App\Models\Account;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AcademyTeachersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('teacher.owner.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean()
                ->sortable(),
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
                ->mutateRecordDataUsing(function (array $data, AcademyTeacher $record): array {
                    $data['teacher_user_id'] = $record->teacher?->owner_user_id;

                    return $data;
                })
                ->mutateDataUsing(function (array $data): array {
                    $data['teacher_account_id'] = $this->teacherAccountId($data);
                    unset($data['teacher_user_id']);

                    return $data;
                }),
        ];
    }

    private function teacherAccountId(array $data): int
    {
        return Account::query()->firstOrCreate([
            'provider_id' => $data['provider_id'],
            'type' => AccountType::AcademyTeacher->value,
            'owner_user_id' => $data['teacher_user_id'],
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ])->id;
    }
}
