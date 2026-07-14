<?php

namespace App\Filament\Resources\AcademyTeachers\Pages;

use App\Enums\AccountType;
use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\AcademyTeachers\AcademyTeacherResource;
use App\Models\Account;
use Filament\Actions\CreateAction;

class ListAcademyTeachers extends BaseListRecords
{
    protected static string $resource = AcademyTeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
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
