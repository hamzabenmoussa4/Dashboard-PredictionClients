<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\Customer;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class AutomationController extends Controller
{
    public function run()
    {
        // 1) Reset badge_computed pour tout le monde (badge_override garde la priorité)
        Customer::query()->update([
            'badge_computed' => 'NORMAL',
            'badge_updated_at' => now(),
        ]);

        // 2) Récupérer les rules actives "prediction" + "set_badge"
        $rules = AutomationRule::where('is_active', true)
            ->where('trigger_type', 'prediction')
            ->where('action_type', 'set_badge')
            ->orderBy('id')
            ->get();

        // 3) Appliquer chaque rule
        foreach ($rules as $rule) {

            // Vérifications
            if (!$rule->prediction_type || !$rule->operator || $rule->threshold === null) {
                continue;
            }

            $badgeToSet = $rule->action_payload['badge'] ?? null;

            if (!in_array($badgeToSet, ['NORMAL', 'VIP', 'RISK'], true)) {
                continue;
            }

            // Subquery: dernière predicted_at par client pour ce type
            $latestSub = Prediction::select('customer_id', DB::raw('MAX(predicted_at) as max_predicted_at'))
                ->where('type', $rule->prediction_type)
                ->groupBy('customer_id');

            // Dernières predictions par client (du type de la rule)
            $latestPredictions = Prediction::joinSub($latestSub, 'lp', function ($join) {
                    $join->on('predictions.customer_id', '=', 'lp.customer_id');
                    $join->on('predictions.predicted_at', '=', 'lp.max_predicted_at');
                })
                ->where('predictions.type', $rule->prediction_type)
                ->where('predictions.score', $rule->operator, $rule->threshold);

            // Customers concernés
            $customerIds = $latestPredictions
                ->pluck('predictions.customer_id')
                ->unique()
                ->values();

            if ($customerIds->isEmpty()) {
                continue;
            }

            // Mise à jour du badge_computed (on ne touche pas badge_override)
            Customer::whereIn('id', $customerIds)->update([
                'badge_computed' => $badgeToSet,
                'badge_updated_at' => now(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Automation exécutée : badges recalculés selon les rules actives.');
    }
}
