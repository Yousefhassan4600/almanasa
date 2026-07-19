<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\RelationEnum;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentStudent extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'parent_user_id',
        'student_user_id',
        'relation',
        'is_primary',
        'occupation',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'parent',
        'student',
    ];

    protected function casts(): array
    {
        return [
            'relation' => RelationEnum::class,
            'is_primary' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
