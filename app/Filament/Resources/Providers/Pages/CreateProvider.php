<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Providers\ProviderResource;
use App\Models\Account;

class CreateProvider extends BaseCreateRecord
{
    protected static string $resource = ProviderResource::class;

    /**
     * @var array<int, int|string>
     */
    protected array $gradeSubjectIds = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->gradeSubjectIds = $data['grade_subject_ids'] ?? [];

        unset($data['grade_subject_ids']);

        $data['subject_id'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        Account::query()->firstOrCreate([
            'provider_id' => $this->record->id,
            'type' => $this->accountType()->value,
            'owner_user_id' => $this->record->owner_user_id,
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $this->record->syncGradeSubjects($this->gradeSubjectIds);
    }

    private function accountType(): AccountType
    {
        return match ($this->record->type) {
            ProviderType::Academy => AccountType::Academy,
            ProviderType::StandaloneTeacher => AccountType::StandaloneTeacher,
        };
    }
}
