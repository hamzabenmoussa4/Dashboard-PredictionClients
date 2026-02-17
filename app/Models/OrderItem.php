<?php

// Namespace du model
namespace App\Models;

// Import du model Eloquent
use Illuminate\Database\Eloquent\Model;

// Import du trait Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Classe OrderItem qui représente la table "order_items"
class OrderItem extends Model
{
    // Active les factories
    use HasFactory;

    // Champs qu'on autorise à remplir via create() / update()
    protected $fillable = [
        'order_id',      // Commande liée à cet item
        'product_name',  // Nom du produit
        'quantity',      // Quantité
        'unit_price',    // Prix unitaire
        'line_total',    // Total de la ligne
    ];

    // Casts : conversion automatique des types
    protected $casts = [
        'quantity'   => 'integer',    // quantity devient un entier
        'unit_price' => 'decimal:2',  // unit_price avec 2 décimales
        'line_total' => 'decimal:2',  // line_total avec 2 décimales
        'created_at' => 'datetime',   // created_at devient un objet date
        'updated_at' => 'datetime',   // updated_at devient un objet date
    ];

    // Relation N:1 -> un item appartient à une commande
    public function order()
    {
        // belongsTo(Order::class) car order_items contient order_id
        return $this->belongsTo(Order::class);
    }
}
