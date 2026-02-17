<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Prediction;
use App\Models\CustomerFeature;
use Carbon\Carbon;

class PredictionService
{
    // Calcule et sauvegarde 3 scores (churn, sales, engagement) pour un client
    public function predictForCustomer(Customer $customer, string $modelVersion = 'rules-v1'): array
    {
        // On récupère les features du client
        $features = $customer->features;

        // Si le client n'a pas encore de features, on ne peut pas prédire
        if (!$features) {
            return [];
        }

        // On calcule le score churn
        $churn = $this->computeChurnScore($features);

        // On calcule le score sales
        $sales = $this->computeSalesScore($features);

        // On calcule le score engagement
        $engagement = $this->computeEngagementScore($features);

        // On sauvegarde les 3 prédictions en base et on retourne les objets créés
        return [
            $this->storePrediction($customer->id, 'churn', $churn['score'], $churn['label'], $modelVersion),
            $this->storePrediction($customer->id, 'sales', $sales['score'], $sales['label'], $modelVersion),
            $this->storePrediction($customer->id, 'engagement', $engagement['score'], $engagement['label'], $modelVersion),
        ];
    }

    // Calcule et sauvegarde les prédictions pour tous les clients
    public function predictForAllCustomers(string $modelVersion = 'rules-v1'): int
    {
        // Compteur de clients traités
        $count = 0;

        // On traite par batch pour éviter de saturer la mémoire
        Customer::query()->chunk(200, function ($customers) use (&$count, $modelVersion) {

            // Pour chaque client du batch
            foreach ($customers as $customer) {

                // On lance les prédictions pour ce client
                $this->predictForCustomer($customer, $modelVersion);

                // On incrémente le compteur
                $count++;
            }
        });

        // On retourne le nombre de clients traités
        return $count;
    }

    // Calcule un score de churn (risque de partir) à partir des features
    private function computeChurnScore(CustomerFeature $f): array
    {
        // On commence avec un score de base
        $score = 0.0;

        // Si le client n'a jamais commandé, risque très élevé
        if ($f->orders_count_total === 0) {
            $score = 0.95;
            return [
                'score' => $this->clamp($score),
                'label' => $this->labelFromScore($score),
            ];
        }

        // Plus la récence est grande, plus le risque augmente
        // Exemple: au-delà de 60 jours, on devient très à risque
        if ($f->recency_days !== null) {
            if ($f->recency_days >= 90) {
                $score += 0.7;
            } elseif ($f->recency_days >= 60) {
                $score += 0.5;
            } elseif ($f->recency_days >= 30) {
                $score += 0.3;
            } else {
                $score += 0.1;
            }
        }

        // Si peu d'achats récents, risque augmente
        if ($f->frequency_90d <= 1) {
            $score += 0.2;
        } elseif ($f->frequency_90d <= 3) {
            $score += 0.1;
        } else {
            $score += 0.05;
        }

        // Les clients qui dépensent beaucoup sont souvent plus "fidèles"
        // Donc on réduit légèrement le risque si total_spent est élevé
        if ((float) $f->total_spent >= 5000) {
            $score -= 0.15;
        } elseif ((float) $f->total_spent >= 1000) {
            $score -= 0.10;
        } elseif ((float) $f->total_spent >= 300) {
            $score -= 0.05;
        }

        // On limite le score entre 0 et 1
        $score = $this->clamp($score);

        // On génère un label simple
        $label = $this->labelFromScore($score);

        // On retourne score + label
        return [
            'score' => $score,
            'label' => $label,
        ];
    }

    // Calcule un score "sales" (probabilité d'achat prochainement)
    private function computeSalesScore(CustomerFeature $f): array
    {
        // Score de base
        $score = 0.0;

        // Si le client n'a jamais commandé, faible probabilité d'achat
        if ($f->orders_count_total === 0) {
            $score = 0.10;
            return [
                'score' => $this->clamp($score),
                'label' => $this->labelFromScore($score),
            ];
        }

        // Si le client a acheté récemment, probabilité d'achat plus élevée
        if ($f->recency_days !== null) {
            if ($f->recency_days <= 7) {
                $score += 0.6;
            } elseif ($f->recency_days <= 15) {
                $score += 0.45;
            } elseif ($f->recency_days <= 30) {
                $score += 0.3;
            } else {
                $score += 0.1;
            }
        }

        // Si le client achète souvent sur 90 jours, probabilité augmente
        if ($f->frequency_90d >= 10) {
            $score += 0.25;
        } elseif ($f->frequency_90d >= 5) {
            $score += 0.15;
        } elseif ($f->frequency_90d >= 2) {
            $score += 0.08;
        }

        // Si le panier moyen est bon, probabilité d'achat intéressant augmente
        if ((float) $f->avg_order_value_90d >= 300) {
            $score += 0.15;
        } elseif ((float) $f->avg_order_value_90d >= 100) {
            $score += 0.08;
        }

        // On limite entre 0 et 1
        $score = $this->clamp($score);

        // On génère un label
        $label = $this->labelFromScore($score);

        return [
            'score' => $score,
            'label' => $label,
        ];
    }

    // Calcule un score "engagement" (activité / interaction)
    private function computeEngagementScore(CustomerFeature $f): array
    {
        // Score de base
        $score = 0.0;

        // Si le client n'a jamais commandé, engagement faible
        if ($f->orders_count_total === 0) {
            $score = 0.15;
            return [
                'score' => $this->clamp($score),
                'label' => $this->labelFromScore($score),
            ];
        }

        // Récence faible => engagement plus fort
        if ($f->recency_days !== null) {
            if ($f->recency_days <= 7) {
                $score += 0.55;
            } elseif ($f->recency_days <= 30) {
                $score += 0.35;
            } elseif ($f->recency_days <= 60) {
                $score += 0.20;
            } else {
                $score += 0.10;
            }
        }

        // Fréquence sur 90 jours reflète l'engagement
        if ($f->frequency_90d >= 8) {
            $score += 0.25;
        } elseif ($f->frequency_90d >= 3) {
            $score += 0.15;
        } elseif ($f->frequency_90d >= 1) {
            $score += 0.08;
        }

        // Panier moyen : un client qui dépense plus est souvent plus "engagé"
        if ((float) $f->avg_order_value_90d >= 200) {
            $score += 0.10;
        } elseif ((float) $f->avg_order_value_90d >= 80) {
            $score += 0.05;
        }

        // On limite entre 0 et 1
        $score = $this->clamp($score);

        // On génère un label
        $label = $this->labelFromScore($score);

        return [
            'score' => $score,
            'label' => $label,
        ];
    }

    // Sauvegarde une prédiction dans la table predictions
    private function storePrediction(int $customerId, string $type, float $score, string $label, string $modelVersion): Prediction
    {
        // On crée une nouvelle ligne Prediction
        $prediction = new Prediction();

        // On assigne le client
        $prediction->customer_id = $customerId;

        // On assigne le type
        $prediction->type = $type;

        // On assigne le score
        $prediction->score = $score;

        // On assigne le label
        $prediction->label = $label;

        // On assigne la version du modèle
        $prediction->model_version = $modelVersion;

        // On met la date de prédiction à maintenant
        $prediction->predicted_at = Carbon::now();

        // On sauvegarde en base
        $prediction->save();

        // On retourne l'objet créé
        return $prediction;
    }

    // Transforme un score en label simple
    private function labelFromScore(float $score): string
    {
        // Si score très faible
        if ($score < 0.33) {
            return 'low';
        }

        // Si score moyen
        if ($score < 0.66) {
            return 'medium';
        }

        // Sinon score élevé
        return 'high';
    }

    // Clamp : force une valeur à rester entre 0 et 1
    private function clamp(float $value): float
    {
        // Si inférieur à 0, on met 0
        if ($value < 0) {
            return 0.0;
        }

        // Si supérieur à 1, on met 1
        if ($value > 1) {
            return 1.0;
        }

        // Sinon on garde la valeur
        return $value;
    }
}
