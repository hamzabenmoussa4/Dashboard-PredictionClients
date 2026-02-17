<?php

namespace App\Services;

use App\Models\AutomationRule;
use App\Models\AutomationAction;
use App\Models\Customer;
use App\Models\Prediction;
use App\Models\CustomerFeature;
use Carbon\Carbon;

class RuleEngineService
{
    public function runForAllCustomers(): int
    {
        $actionsCount = 0;

        $rules = AutomationRule::where('is_active', true)->get();

        if ($rules->isEmpty()) {
            return 0;
        }

        Customer::query()->chunk(200, function ($customers) use ($rules, &$actionsCount) {

            foreach ($customers as $customer) {

                $features = $customer->features;

                if (!$features) {
                    continue;
                }

                foreach ($rules as $rule) {

                    if ($this->ruleMatchesCustomer($rule, $customer, $features)) {
                        $this->createActionFromRule($rule, $customer);
                        $actionsCount++;
                    }
                }
            }
        });

        return $actionsCount;
    }

    private function ruleMatchesCustomer(
        AutomationRule $rule,
        Customer $customer,
        CustomerFeature $features
    ): bool
    {
        // ======================
        // 1) TRIGGER: PREDICTION
        // ======================
        if ($rule->trigger_type === 'prediction') {

            if (!$rule->prediction_type) {
                return false;
            }

            $prediction = Prediction::where('customer_id', $customer->id)
                ->where('type', $rule->prediction_type)
                ->orderByDesc('predicted_at')
                ->first();

            if (!$prediction) {
                return false;
            }

            return $this->compareValues(
                (float) $prediction->score,
                $rule->operator,
                $rule->threshold
            );
        }

        // ======================
        // 2) TRIGGER: FEATURE
        // ======================
        if ($rule->trigger_type === 'feature') {

            // Cas A : on supporte l'ancien style recency_days_threshold
            if ($rule->recency_days_threshold !== null) {

                if ($features->recency_days === null) {
                    return false;
                }

                return $this->compareValues(
                    (int) $features->recency_days,
                    $rule->operator,
                    $rule->recency_days_threshold
                );
            }

            // Cas B : style générique via action_payload.feature_key
            // Exemple: feature_key = "total_spent" OU "frequency_90d" etc.
            $featureKey = $rule->action_payload['feature_key'] ?? null;

            if (!$featureKey) {
                return false;
            }

            // On vérifie que la propriété existe sur l'objet features
            if (!isset($features->{$featureKey})) {
                return false;
            }

            // Valeur de la feature
            $value = $features->{$featureKey};

            // Si la valeur est null, on ne compare pas
            if ($value === null) {
                return false;
            }

            // Ici on utilise rule->threshold comme seuil numérique
            return $this->compareValues(
                (float) $value,
                $rule->operator,
                $rule->threshold
            );
        }

        // ======================
        // 3) TRIGGER: SEGMENT
        // ======================
        if ($rule->trigger_type === 'segment') {

            $segmentId = $rule->action_payload['segment_id'] ?? null;

            if (!$segmentId) {
                return false;
            }

            // Ici, on déclenche si le client N'EST PAS encore dans le segment
            return !$customer->segments()
                ->where('segments.id', $segmentId)
                ->exists();
        }

        return false;
    }

    private function createActionFromRule(AutomationRule $rule, Customer $customer): void
    {
        // Option anti-spam simple: éviter de créer 50 fois la même action
        // Ici: on évite un doublon si une action identique "pending" existe déjà pour ce client et cette règle
        $alreadyExists = AutomationAction::where('customer_id', $customer->id)
            ->where('rule_id', $rule->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $action = new AutomationAction();

        $action->customer_id = $customer->id;
        $action->rule_id = $rule->id;
        $action->type = $rule->action_type;
        $action->payload = $rule->action_payload;
        $action->status = 'pending';
        $action->scheduled_for = Carbon::now();

        $action->save();

        // Si action = add_segment, on applique immédiatement le segment
        if ($rule->action_type === 'add_segment') {

            $segmentId = $rule->action_payload['segment_id'] ?? null;

            if ($segmentId) {
                $customer->segments()->syncWithoutDetaching([$segmentId]);
            }
        }
    }

    private function compareValues($value, ?string $operator, $threshold): bool
    {
        if ($operator === null || $threshold === null) {
            return false;
        }

        switch ($operator) {
            case '>':
                return $value > $threshold;

            case '>=':
                return $value >= $threshold;

            case '<':
                return $value < $threshold;

            case '<=':
                return $value <= $threshold;

            case '==':
                return $value == $threshold;

            default:
                return false;
        }
    }
}
