<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AttemptStatusType extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(AttemptStatus::class, 'attempt_status_type_id');
    }
}
