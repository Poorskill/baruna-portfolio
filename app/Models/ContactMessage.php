<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'email', 'subject', 'message', 'status', 'read_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }
}
