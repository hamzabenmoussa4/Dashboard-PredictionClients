<?php

// Namespace du model
namespace App\Models;

// Import du model Eloquent
use Illuminate\Database\Eloquent\Model;

// Import du trait Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Classe Segment qui représente la table "segments"
class Segment extends Model
{
    // Active les factories
    use HasFactory;

    // Champs qu'on autorise à remplir via create() / update()
    protected $fillable = [
        'name',         // Nom du segment
        'description',  // Description du segment
    ];

    // Casts : conversion automatique des types
    protected $casts = [
        'created_at' => 'datetime', // date
        'updated_at' => 'datetime', // date
    ];

    // Relation N:N -> un segment contient plusieurs clients
    public function customers()
    {
        // belongsToMany(Customer::class) via la table pivot customer_segment
        return $this->belongsToMany(Customer::class, 'customer_segment')
            ->withTimestamps(); // garde les timestamps du pivot
    }
}
