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

class Subject extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'track_id',
        'name',
        'icon',
        'image',
        'description',
        'deleted_by',
    ];

    protected $appends = [
        'full_name',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'grade_subjects')
            ->withPivot(['id'])
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        $track = $this->relationLoaded('track')
            ? $this->track?->name
            : $this->track()->first()?->name;

        return collect([$this->name, $track])->filter()->join(' - ');
    }

    /**
     * @param  array<int, int|string>  $gradeIds
     */
    public function syncGrades(array $gradeIds): void
    {
        $gradeIds = collect($gradeIds)
            ->filter()
            ->map(fn (int|string $gradeId): int => (int) $gradeId)
            ->unique()
            ->values();

        Grade::query()
            ->whereKey($gradeIds)
            ->get(['id'])
            ->each(function (Grade $grade): void {
                $this->gradeSubjects()->firstOrCreate([
                    'grade_id' => $grade->id,
                ]);
            });

        $this->gradeSubjects()
            ->when(
                $gradeIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('grade_id', $gradeIds),
            )
            ->delete();
    }
}
