<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ChatMemberRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMember extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected array $tenantRelations = [
        'chat_room',
    ];

    protected function casts(): array
    {
        return [
            'role' => ChatMemberRole::class,
            'joined_at' => 'datetime',
            'last_read_at' => 'datetime',
        ];
    }

    public function chat_room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
