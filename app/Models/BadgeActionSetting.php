<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeActionSetting extends Model
{
    protected $fillable = [
        'badge',
        'action_type',
        'message',
        'discount_percent',
    ];
}
