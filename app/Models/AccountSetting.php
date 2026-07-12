<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'website_enabled' => 'boolean',
            'registration_enabled' => 'boolean',
            'chat_enabled' => 'boolean',
            'payment_enabled' => 'boolean',
            'tax_percentage' => 'decimal:2',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
