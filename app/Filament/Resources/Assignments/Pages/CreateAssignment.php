<?php

namespace App\Filament\Resources\Assignments\Pages;

use App\Actions\Assignments\GenerateAssignmentQuestions;
use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Assignments\AssignmentResource;

class CreateAssignment extends BaseCreateRecord
{
    protected static string $resource = AssignmentResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return app(GenerateAssignmentQuestions::class)->handle($data);
    }
}
