<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Providers\ProviderResource;

class EditProvider extends BaseEditRecord
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
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->gradeSubjectIds = $data['grade_subject_ids'] ?? [];

        unset($data['grade_subject_ids']);

        $data['subject_id'] = null;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncGradeSubjects($this->gradeSubjectIds);
    }
}
