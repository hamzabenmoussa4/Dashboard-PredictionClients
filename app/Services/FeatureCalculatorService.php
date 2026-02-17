<?php

// Namespace du service
namespace App\Services;

// Import du model Customer
use App\Models\Customer;

// Import du model CustomerFeature
use App\Models\CustomerFeature;

// Import de Carbon pour gérer les dates facilement
use Carbon\Carbon;

// Import du support DB pour certaines requêtes si besoin
use Illuminate\Support\Facades\DB;

// Service responsable du calcul des features d'un client
class FeatureCalculatorService
{
    // Nombre de jours utilisés pour la fenêtre "90 jours"
    private int $windowDays = 90;

    // Calcule et sauvegarde les features pour un client donné
    public function computeForCustomer(Customer $customer): CustomerFeature
    {
        // On récupère la date actuelle
        $now = Carbon::now();

        // On calcule la date de début de la fenêtre (maintenant - 90 jours)
        $windowStart = $now->copy()->subDays($this->windowDays);

        // On récupère toutes les commandes du client (pour le total global)
        $allOrdersQuery = $customer->orders();

        // On récupère la dernière commande du client (par date ordered_at)
        $lastOrder = $allOrdersQuery
            ->orderByDesc('ordered_at')
            ->first();

        // Si le client n'a jamais commandé, last_order_at reste null
        $lastOrderAt = $lastOrder ? $lastOrder->ordered_at : null;

        // Si last_order_at existe, on calcule le nombre de jours depuis la dernière commande
        // Sinon, on laisse recency_days à null (ou on pourrait mettre une valeur grande)
        $recencyDays = $lastOrderAt ? Carbon::parse($lastOrderAt)->diffInDays($now) : null;

        // On récupère les commandes dans la fenêtre des 90 jours (filtrage par ordered_at)
        $orders90dQuery = $customer->orders()
            ->whereNotNull('ordered_at')
            ->where('ordered_at', '>=', $windowStart);

        // On calcule la fréquence (nombre de commandes sur 90 jours)
        $frequency90d = (int) $orders90dQuery->count();

        // On calcule le montant dépensé sur 90 jours (somme total_amount)
        $monetary90d = (float) $customer->orders()
            ->whereNotNull('ordered_at')
            ->where('ordered_at', '>=', $windowStart)
            ->sum('total_amount');

        // On calcule le panier moyen sur 90 jours
        // Si frequency = 0, on évite la division par zéro
        $avgOrderValue90d = $frequency90d > 0 ? ($monetary90d / $frequency90d) : 0;

        // On calcule le nombre total de commandes du client (toutes périodes)
        $ordersCountTotal = (int) $customer->orders()->count();

        // On calcule le total dépensé (toutes périodes)
        $totalSpent = (float) $customer->orders()->sum('total_amount');

        // On cherche la ligne de features existante du client (1 seule ligne par client)
        $features = CustomerFeature::where('customer_id', $customer->id)->first();

        // Si aucune ligne n'existe, on en crée une nouvelle
        if (!$features) {
            $features = new CustomerFeature();
        }

        // On assigne le client
        $features->customer_id = $customer->id;

        // On enregistre la dernière date de commande
        $features->last_order_at = $lastOrderAt;

        // On enregistre la récence en jours
        $features->recency_days = $recencyDays;

        // On enregistre la fréquence 90 jours
        $features->frequency_90d = $frequency90d;

        // On enregistre le montant dépensé sur 90 jours
        $features->monetary_90d = $monetary90d;

        // On enregistre le panier moyen 90 jours
        $features->avg_order_value_90d = $avgOrderValue90d;

        // On enregistre le nombre total de commandes
        $features->orders_count_total = $ordersCountTotal;

        // On enregistre le total dépensé
        $features->total_spent = $totalSpent;

        // On sauvegarde en base
        $features->save();

        // On retourne l'objet features (utile pour enchaîner avec predictions)
        return $features;
    }

    // Calcule et sauvegarde les features pour tous les clients
    public function computeForAllCustomers(): int
    {
        // Compteur pour savoir combien de clients ont été traités
        $count = 0;

        // On récupère les clients par batch pour éviter de charger toute la table en mémoire
        Customer::query()->chunk(200, function ($customers) use (&$count) {

            // Pour chaque client du batch
            foreach ($customers as $customer) {

                // On calcule et sauvegarde ses features
                $this->computeForCustomer($customer);

                // On incrémente le compteur
                $count++;
            }
        });

        // On retourne le nombre de clients traités
        return $count;
    }
}
