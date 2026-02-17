<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Prediction;
use Illuminate\Support\Carbon;

shows:
class PredictionEngine
{
    public function refreshForCustomer(Customer $customer): void
    {
        // Récupérer stats commandes du client
        $ordersQuery = Order::where('customer_id', $customer->id);

        $ordersCount = (int) $ordersQuery->count(); // nombre de commandes
        $totalAmount = (float) $ordersQuery->sum('total_amount'); // somme dépensée

        $lastOrderDate = $ordersQuery->max('ordered_at'); // date de la dernière commande (string|datetime|null)

        // Calcul recency en jours (si pas de commande => très ancien)
        $recencyDays = 9999;

        if ($lastOrderDate) {
            $recencyDays = Carbon::parse($lastOrderDate)->diffInDays(now());
        }

        // Montant moyen
        $avgOrder = 0.0;

        if ($ordersCount > 0) {
            $avgOrder = $totalAmount / $ordersCount;
        }

        // --- SCORES "réels" (heuristiques) ---
        // Churn : plus recencyDays est grand, plus le churn augmente
        // Normalisation simple: recency 0 jours => 0.05 ; recency >= 90 => ~0.95
        $churnScore = $this->clamp(0.05 + ($recencyDays / 90.0) * 0.90, 0.0, 0.99);

        // Sales : basé sur fréquence + panier moyen
        // plus commandes et plus avgOrder grand => score monte
        $salesScore = $this->clamp(($ordersCount / 10.0) * 0.6 + ($avgOrder / 500.0) * 0.4, 0.0, 0.99);

        // Engagement : basé sur activité récente + fréquence
        // plus récent + plus commandes => engagement monte
        $engagementScore = $this->clamp((1.0 - min($recencyDays, 60) / 60.0) * 0.7 + min($ordersCount, 10) / 10.0 * 0.3, 0.0, 0.99);

        // Labels simples (tu peux les changer après)
        $churnLabel = $churnScore >= 0.7 ? 'high' : ($churnScore >= 0.4 ? 'medium' : 'low');
        $salesLabel = $salesScore >= 0.7 ? 'high' : ($salesScore >= 0.4 ? 'medium' : 'low');
        $engLabel = $engagementScore >= 0.7 ? 'high' : ($engagementScore >= 0.4 ? 'medium' : 'low');

        // Enregistrer / update 3 lignes dans predictions
        $this->upsertPrediction($customer->id, 'churn', $churnScore, $churnLabel);
        $this->upsertPrediction($customer->id, 'sales', $salesScore, $salesLabel);
        $this->upsertPrediction($customer->id, 'engagement', $engagementScore, $engLabel);
    }

    private function upsertPrediction(int $customerId, string $type, float $score, string $label): void
    {
        // On garde une seule prediction "courante" par type/client
        // On update si elle existe, sinon create
        Prediction::updateOrCreate(
            [
                'customer_id' => $customerId,
                'type' => $type,
            ],
            [
                'score' => $score,
                'label' => $label,
                'model_version' => 'heuristic-v1',
                'predicted_at' => now(),
            ]
        );
    }

    private function clamp(float $value, float $min, float $max): float
    {
        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;
    }
}
