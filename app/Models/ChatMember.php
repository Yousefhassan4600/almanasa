<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ChatMemberRole;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMember extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'last_read_at',
        'deleted_by',
    ];

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
