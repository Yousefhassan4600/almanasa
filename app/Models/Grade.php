<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function educationStage(): BelongsTo
    {
        return $this->belongsTo(EducationStage::class);
    }

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }
}
