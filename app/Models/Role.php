<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_assignable' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
