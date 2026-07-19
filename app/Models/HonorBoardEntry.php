<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class HonorBoardEntry extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'student_user_id',
        'name',
        'grade_name',
        'score_percentage',
        'rank_label',
        'image',
        'is_active',
        'deleted_by',
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'score_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
