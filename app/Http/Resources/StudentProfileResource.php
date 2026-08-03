<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Model */
class StudentProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'phone' => $this->user->phone,
            'email' => $this->email ?? null,
            'avatar' => $this->avatar ? $this->storageUrl($this->avatar) : null,
            'country' => $this->country ? $this->country->name : null,
            'city' => $this->city ? $this->city->name : null,
            'education_stage' => $this->education_stage ? $this->education_stage->name : null,
            'grade' => $this->grade ? $this->grade->name : null,
            'school_name' => $this->school_name ?? null,
        ];
    }
    private function storageUrl(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
