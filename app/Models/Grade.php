<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Grade extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'education_stage_id',
        'name',
        'sort_order',
        'deleted_by',
    ];

    protected $appends = [
        'full_name',
    ];

    public array $translatable = [
        'name',
    ];

    public function educationStage(): BelongsTo
    {
        return $this->belongsTo(EducationStage::class, 'education_stage_id');
    }

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'grade_subjects')
            ->withPivot(['id', 'track_id'])
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        $educationStage = $this->relationLoaded('educationStage')
            ? $this->educationStage?->name
            : $this->educationStage()->first()?->name;

        return collect([$educationStage, $this->name])->filter()->join(' - ');
    }
}
