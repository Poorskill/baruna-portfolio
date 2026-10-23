<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';

    protected $fillable = [
        'institution', 'qualification', 'field_of_study',
        'start_date', 'end_date', 'expected_end_date',
        'status', 'description', 'sort_order', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'expected_end_date' => 'date',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
