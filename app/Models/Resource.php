<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Resource extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_downloadable' => 'boolean',
        ];
    }

    public function getResourceableDisplayNameAttribute(): string
    {
        $resourceable = $this->resourceable;

        return $resourceable?->title ?? $resourceable?->name ?? $resourceable?->display_name ?? class_basename((string) $this->resourceable_type).' #'.$this->resourceable_id;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
