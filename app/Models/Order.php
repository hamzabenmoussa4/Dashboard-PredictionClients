<?php

// Namespace du model
namespace App\Models;

// Import du model Eloquent
use Illuminate\Database\Eloquent\Model;

// Import du trait Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Classe Order qui représente la table "orders"
class Order extends Model
{
    // Active les factories
    use HasFactory;

    // Champs qu'on autorise à remplir via create() / update()
    protected $fillable = [
        'customer_id',   // Client lié à la commande
        'order_number',  // Numéro de commande
        'status',        // Statut (pending, paid, etc.)
        'total_amount',  // Total commande
        'currency',      // Devise
        'ordered_at',    // Date réelle de la commande
    ];

    // Casts : conversion automatique des types
    protected $casts = [
        'total_amount' => 'decimal:2', // total_amount toujours avec 2 décimales
        'ordered_at'   => 'datetime',  // ordered_at devient un objet date
        'created_at'   => 'datetime',  // created_at devient un objet date
        'updated_at'   => 'datetime',  // updated_at devient un objet date
    ];

    // Relation N:1 -> une commande appartient à un client
    public function customer()
    {
        // belongsTo(Customer::class) car orders contient customer_id
        return $this->belongsTo(Customer::class);
    }

    // Relation 1:N -> une commande a plusieurs lignes (items)
    public function items()
    {
        // hasMany(OrderItem::class) car order_items contient order_id
        return $this->hasMany(OrderItem::class);
    }
}
