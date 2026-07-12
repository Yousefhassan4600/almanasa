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

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'track_id');
    }

    public function accountSubjects(): HasMany
    {
        return $this->hasMany(AccountSubject::class);
    }

    public function getNameAttribute(): string
    {
        $grade = $this->relationLoaded('grade') ? $this->grade?->name : $this->grade()->value('name');
        $subject = $this->relationLoaded('subject') ? $this->subject?->name : $this->subject()->value('name');
        $track = $this->relationLoaded('track') ? $this->track?->name : $this->track()->value('name');

        return collect([$grade, $subject, $track])->filter()->join(' - ');
    }
}
