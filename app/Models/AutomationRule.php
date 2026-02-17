<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'trigger_type',
        'prediction_type',
        'operator',
        'threshold',
        'action_type',
        'action_payload',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold' => 'decimal:6',
        'action_payload' => 'array',
    ];
}
