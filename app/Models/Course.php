<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use FiltersByTenant, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public array $translatable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'monthly_price' => 'decimal:2',
            'status' => ContentStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'teacher_account_id');
    }

    public function accountSubject(): BelongsTo
    {
        return $this->belongsTo(AccountSubject::class, 'account_subject_id');
    }
}
