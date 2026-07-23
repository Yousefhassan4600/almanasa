<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeSubject extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'grade_id',
        'track_id',
        'subject_id',
        'deleted_by',
    ];

    protected $appends = [
        'full_name',
        'name',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'track_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function accountSubjects(): HasMany
    {
        return $this->hasMany(AccountSubject::class);
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getFullNameAttribute(): string
    {
        $grade = $this->relationLoaded('grade') ? $this->grade : $this->grade()->with('educationStage')->first();
        $grade?->loadMissing('educationStage');

        $track = $this->relationLoaded('track') ? $this->track : $this->track()->first();
        $subject = $this->relationLoaded('subject') ? $this->subject : $this->subject()->first();

        return collect([$grade?->full_name, $track?->name, $subject?->name])->filter()->join(' / ');
    }
}
