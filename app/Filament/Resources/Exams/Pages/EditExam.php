<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Actions\Exams\GenerateExamModels;
use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Exams\ExamResource;

class EditExam extends BaseEditRecord
{
    protected static string $resource = ExamResource::class;

    private bool $shouldRegenerateModels = false;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->shouldRegenerateModels = $this->record->models()->doesntExist()
            || $this->shouldRegenerateModels($data);

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->shouldRegenerateModels) {
            return;
        }

        app(GenerateExamModels::class)->handle($this->record);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function shouldRegenerateModels(array $data): bool
    {
        foreach ([
            'course_id',
            'num_of_questions',
            'num_of_easy_questions',
            'num_of_medium_questions',
            'num_of_hard_questions',
            'num_of_models',
        ] as $field) {
            if ((string) $this->record->getAttribute($field) !== (string) ($data[$field] ?? null)) {
                return true;
            }
        }

        return $this->normalizedIds($this->record->lesson_ids ?? []) !== $this->normalizedIds($data['lesson_ids'] ?? []);
    }

    /**
     * @param  array<int, mixed>  $ids
     * @return array<int, int>
     */
    private function normalizedIds(array $ids): array
    {
        return collect($ids)
            ->filter(fn (mixed $id): bool => filled($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
    }
}
