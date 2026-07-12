<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenantParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory, ScopedByTenantParent;

    protected $guarded = [];

    protected static function tenantParentRelation(): string
    {
        return 'academicYear';
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
