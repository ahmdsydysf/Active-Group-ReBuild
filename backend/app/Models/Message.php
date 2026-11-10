<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['message_type_id', 'name', 'email', 'phone', 'subject', 'body', 'is_read', 'ip', 'user_agent'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MessageType::class, 'message_type_id');
    }
}
