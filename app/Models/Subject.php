<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'name',
        'icon',
        'image',
        'description',
        'deleted_by',
    ];

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'grade_subjects')
            ->withPivot(['id', 'track_id'])
            ->withTimestamps();
    }
}
