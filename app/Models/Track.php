<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Track extends Model
{
    use FiltersByTenant, HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'name',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
