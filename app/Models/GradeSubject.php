<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeSubject extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected $appends = [
        'full_name',
        'name',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
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

        $subject = $this->relationLoaded('subject') ? $this->subject : $this->subject()->with('track')->first();
        $subject?->loadMissing('track');

        return collect([$grade?->full_name, $subject?->full_name])->filter()->join(' / ');
    }
}
