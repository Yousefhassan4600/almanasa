<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Track extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'deleted_by',
    ];

    public array $translatable = [
        'name',
    ];

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }
}
