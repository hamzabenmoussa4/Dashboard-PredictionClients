<?php

// Namespace du model
namespace App\Models;

// Import du model Eloquent
use Illuminate\Database\Eloquent\Model;

// Import du trait Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Classe CustomerFeature qui représente la table "customer_features"
class CustomerFeature extends Model
{
    // Active les factories
    use HasFactory;

    // Champs qu'on autorise à remplir via create() / update()
    protected $fillable = [
        'customer_id',           // Client lié à ces features
        'last_order_at',         // Date dernière commande
        'recency_days',          // Jours depuis dernière commande
        'frequency_90d',         // Nb commandes 90 jours
        'monetary_90d',          // Total dépensé 90 jours
        'avg_order_value_90d',   // Panier moyen 90 jours
        'orders_count_total',    // Total commandes depuis début
        'total_spent',           // Total dépensé depuis début
    ];

    // Casts : conversion automatique des types
    protected $casts = [
        'last_order_at'        => 'datetime',   // date dernière commande
        'recency_days'         => 'integer',    // entier
        'frequency_90d'        => 'integer',    // entier
        'monetary_90d'         => 'decimal:2',  // 2 décimales
        'avg_order_value_90d'  => 'decimal:2',  // 2 décimales
        'orders_count_total'   => 'integer',    // entier
        'total_spent'          => 'decimal:2',  // 2 décimales
        'created_at'           => 'datetime',   // date
        'updated_at'           => 'datetime',   // date
    ];

    // Relation N:1 -> ces features appartiennent à un client
    public function customer()
    {
        // belongsTo(Customer::class) car customer_features contient customer_id
        return $this->belongsTo(Customer::class);
    }
}
