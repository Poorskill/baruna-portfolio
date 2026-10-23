<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'name', 'issuer', 'issued_at', 'credential_url',
        'credential_id', 'image', 'is_visible', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
