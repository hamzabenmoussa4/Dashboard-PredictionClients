<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationAction extends Model
{
    protected $table = 'automation_actions';

    protected $fillable = [
        'customer_id',
        'customer_badge',
        'type',            // IMPORTANT: champ obligatoire dans ta table
        'action_type',      // on le garde aussi (même si redondant)
        'payload',
        'status',
        'scheduled_for',
        'executed_at',
        'message',
        'discount_percent',
        'rule_id',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'payload' => 'array',
        'discount_percent' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
