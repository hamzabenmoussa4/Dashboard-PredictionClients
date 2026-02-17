<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'badge_computed',
        'badge_override',
        'badge_updated_at',
    ];

    protected $casts = [
        'badge_updated_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function getBadgeAttribute(): string
    {
        return $this->badge_override ?? $this->badge_computed ?? 'NORMAL';
    }
}
