<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // churn/sales/engagement
        $q = trim((string) $request->query('q')); // recherche

        $query = Prediction::with('customer')
            ->orderByDesc('predicted_at')
            ->orderByDesc('id');

        if ($type && in_array($type, ['churn', 'sales', 'engagement'], true)) {
            $query->where('type', $type);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('type', 'like', "%{$q}%")
                    ->orWhere('label', 'like', "%{$q}%")
                    ->orWhere('score', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('first_name', 'like', "%{$q}%")
                          ->orWhere('last_name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    });
            });
        }

        $predictions = $query->paginate(20)->withQueryString();

        return view('predictions.index', [
            'predictions' => $predictions,
            'type' => $type,
            'q' => $q,
        ]);
    }
}
