<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A contact form submission. */
class Message extends Model
{
    public const TYPES = ['quote', 'consult', 'complaint', 'jobs', 'other'];

    public const STATUSES = ['new', 'read', 'archived'];

    protected $fillable = ['type', 'name', 'phone', 'email', 'governorate', 'body', 'locale', 'status', 'is_spam', 'reply', 'replied_at', 'ip'];

    protected $hidden = ['ip'];

    protected function casts(): array
    {
        return ['is_spam' => 'boolean', 'replied_at' => 'datetime'];
    }
}
