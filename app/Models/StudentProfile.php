<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\Gender;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'user_id',
        'email',
        'avatar',
        'gender',
        'country_id',
        'city_id',
        'education_stage_id',
        'grade_id',
        'school_name',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'user',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function education_stage(): BelongsTo
    {
        return $this->belongsTo(EducationStage::class, 'education_stage_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function parentStudents(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'student_user_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }
}
