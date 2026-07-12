<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenantParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory, ScopedByTenantParent;

    protected $guarded = [];

    protected static function tenantParentRelation(): string
    {
        return 'course';
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'parent_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
