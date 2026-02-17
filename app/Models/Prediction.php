<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'score',
        'label',
        'model_version',
        'predicted_at',
    ];

    protected $casts = [
        'predicted_at' => 'datetime',
        'score' => 'decimal:6',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
