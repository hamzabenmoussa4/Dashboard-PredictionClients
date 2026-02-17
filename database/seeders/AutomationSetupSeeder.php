<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Segment;
use App\Models\AutomationRule;

class AutomationSetupSeeder extends Seeder
{
    public function run(): void
    {
        $atRiskSegment = Segment::firstOrCreate(
            ['name' => 'At Risk'],
            ['description' => 'Clients à risque de churn (score élevé)']
        );

        $vipSegment = Segment::firstOrCreate(
            ['name' => 'VIP'],
            ['description' => 'Clients à forte valeur (dépenses élevées)']
        );

        // Règle 1 : churn >= 0.66 => add_segment At Risk
        AutomationRule::updateOrCreate(
            ['name' => 'Churn High -> Add At Risk Segment'],
            [
                'is_active' => true,
                'trigger_type' => 'prediction',
                'prediction_type' => 'churn',
                'operator' => '>=',
                'threshold' => 0.66,
                'recency_days_threshold' => null,
                'action_type' => 'add_segment',
                'action_payload' => [
                    'segment_id' => $atRiskSegment->id
                ],
            ]
        );

        // Règle 2 : total_spent >= 10000 => add_segment VIP
        AutomationRule::updateOrCreate(
            ['name' => 'High Spender -> Add VIP Segment'],
            [
                'is_active' => true,
                'trigger_type' => 'feature',
                'prediction_type' => null,
                'operator' => '>=',
                'threshold' => 10000,
                'recency_days_threshold' => null,
                'action_type' => 'add_segment',
                'action_payload' => [
                    'segment_id' => $vipSegment->id,
                    'feature_key' => 'total_spent'
                ],
            ]
        );
    }
}
