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
            ->withPivot(['id'])
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        $educationStage = $this->relationLoaded('educationStage')
            ? $this->educationStage?->name
            : $this->educationStage()->first()?->name;

        return collect([$educationStage, $this->name])->filter()->join(' - ');
    }

    /**
     * @param  array<int, int|string>  $subjectIds
     */
    public function syncSubjects(array $subjectIds): void
    {
        $subjectIds = collect($subjectIds)
            ->filter()
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();

        Subject::query()
            ->whereKey($subjectIds)
            ->get(['id'])
            ->each(function (Subject $subject): void {
                $this->gradeSubjects()->firstOrCreate([
                    'subject_id' => $subject->id,
                ]);
            });

        $this->gradeSubjects()
            ->when(
                $subjectIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('subject_id', $subjectIds),
            )
            ->delete();
    }
}
