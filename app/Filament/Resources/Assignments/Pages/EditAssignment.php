<?php

namespace App\Filament\Resources\Assignments\Pages;

use App\Actions\Assignments\GenerateAssignmentQuestions;
use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Assignments\AssignmentResource;

class EditAssignment extends BaseEditRecord
{
    protected static string $resource = AssignmentResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->question_ids && ! $this->shouldRegenerateQuestions($data)) {
            return $data;
        }

        return app(GenerateAssignmentQuestions::class)->handle($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function shouldRegenerateQuestions(array $data): bool
    {
        foreach ([
            'course_id',
            'num_of_questions',
            'num_of_easy_questions',
            'num_of_medium_questions',
            'num_of_hard_questions',
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
