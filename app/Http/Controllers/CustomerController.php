<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    // Page: liste des clients "At Risk" (paginée)
    public function atRisk(Request $request)
    {
        // On récupère uniquement les clients qui appartiennent au segment "At Risk"
        $customers = Customer::whereHas('segments', function ($q) {
                $q->where('segments.name', 'At Risk');
            })
            // On charge les features pour afficher récence, fréquence, total_spent
            ->with(['features'])
            // On charge la dernière prédiction churn (on prend juste la plus récente)
            ->with(['predictions' => function ($q) {
                $q->where('type', 'churn')
                  ->orderByDesc('predicted_at');
            }])
            // Tri: clients les plus “anciens” (récence grande) en premier
            ->orderByDesc(
                // Si recency_days est null, MySQL peut mettre null en dernier selon config
                // On garde un tri simple, on ajustera si besoin
                Customer::select('customer_features.recency_days')
                    ->join('customer_features', 'customer_features.customer_id', '=', 'customers.id')
                    ->whereColumn('customer_features.customer_id', 'customers.id')
                    ->limit(1)
            )
            // Pagination (20 par page)
            ->paginate(20);

        // On retourne la vue
        return view('customers.at_risk', [
            'customers' => $customers,
        ]);
    }
}
