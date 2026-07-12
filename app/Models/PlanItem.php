<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenantParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlanItem extends Model
{
    use HasFactory, ScopedByTenantParent;

    protected $guarded = [];

    protected static function tenantParentRelation(): string
    {
        return 'plan';
    }

    public function getItemDisplayNameAttribute(): string
    {
        $item = $this->item;

        return $item?->title ?? $item?->name ?? $item?->display_name ?? class_basename((string) $this->item_type).' #'.$this->item_id;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
