<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Model */
class AccountResource extends JsonResource
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
            'provider_id' => $this->provider_id,
            'owner_user_id' => $this->owner_user_id,
            'provider_name' => $this->provider->name ?? null,
             'logo' => $this->provider->logo ? $this->storageUrl($this->provider->logo) : null,
             'cover_image' => $this->cover_image ? $this->storageUrl($this->cover_image) : null,

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
