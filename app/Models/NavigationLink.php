<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationLink extends Model
{
    protected $fillable = ['placement', 'label', 'url', 'is_external', 'sort_order', 'is_visible'];

    protected function casts(): array
    {
        return [
            'is_external' => 'boolean',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function scopeHeader($query)
    {
        return $query->where('placement', 'header');
    }

    public function scopeFooter($query)
    {
        return $query->where('placement', 'footer');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
