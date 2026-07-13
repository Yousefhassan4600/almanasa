<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\RelationEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentStudent extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

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
