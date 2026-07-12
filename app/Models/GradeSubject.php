<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeSubject extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        $track = $this->educationTrack?->name ? " ({$this->educationTrack->name})" : '';

        return trim(($this->grade?->name ?? 'Grade').' - '.($this->subject?->name ?? 'Subject').$track);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function educationTrack(): BelongsTo
    {
        return $this->belongsTo(EducationTrack::class);
    }

    public function tenantOfferings(): HasMany
    {
        return $this->hasMany(TenantGradeSubject::class);
    }
}
