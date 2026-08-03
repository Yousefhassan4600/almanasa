<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subject = $this->gradeSubject?->subject;
        $track   = $this->gradeSubject?->track;

        return [
            'id'                     => $this->id,
            'name'                   => $subject?->name,
            'icon'                   => $subject?->icon,
            'description'            => $subject?->description,
            'track'                  => $track ? [
                'id'   => $track->id,
                'name' => $track->name,
            ] : null,
            'active_teachers_count'  => $this->whenHas('active_teachers_count'),
        ];
    }
}
