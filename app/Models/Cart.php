<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => CartStatus::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->student?->name ?? 'Student').' - '.($this->tenant?->name ?? 'Tenant'));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
