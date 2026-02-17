<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RuleCrudController extends Controller
{
    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $q = trim((string) $request->query('q'));

        $editRule = null;

        if ($editId && is_numeric($editId)) {
            $editRule = AutomationRule::find((int) $editId);
        }

        $rulesQuery = AutomationRule::orderByDesc('created_at');

        if ($q !== '') {
            $rulesQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('prediction_type', 'like', "%{$q}%")
                    ->orWhere('operator', 'like', "%{$q}%")
                    ->orWhere('threshold', 'like', "%{$q}%")
                    ->orWhere('action_type', 'like', "%{$q}%");
            });
        }

        $rules = $rulesQuery->paginate(15)->withQueryString();

        return view('automation.rules', [
            'rules' => $rules,
            'editRule' => $editRule,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prediction_type' => ['required', Rule::in(['churn', 'sales', 'engagement'])],
            'operator' => ['required', Rule::in(['>', '>=', '<', '<=', '='])],
            'threshold' => ['required', 'numeric'],
            'result_badge' => ['required', Rule::in(['NORMAL', 'VIP', 'RISK'])],
        ]);

        AutomationRule::create([
            'name' => $validated['name'],
            'is_active' => true,
            'trigger_type' => 'prediction',
            'prediction_type' => $validated['prediction_type'],
            'operator' => $validated['operator'],
            'threshold' => $validated['threshold'],
            'action_type' => 'set_badge',
            'action_payload' => [
                'badge' => $validated['result_badge'],
            ],
        ]);

        return redirect()->route('automation.rules')->with('success', 'Règle créée.');
    }

    public function update(Request $request, AutomationRule $rule)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prediction_type' => ['required', Rule::in(['churn', 'sales', 'engagement'])],
            'operator' => ['required', Rule::in(['>', '>=', '<', '<=', '='])],
            'threshold' => ['required', 'numeric'],
            'result_badge' => ['required', Rule::in(['NORMAL', 'VIP', 'RISK'])],
        ]);

        $rule->update([
            'name' => $validated['name'],
            'trigger_type' => 'prediction',
            'prediction_type' => $validated['prediction_type'],
            'operator' => $validated['operator'],
            'threshold' => $validated['threshold'],
            'action_type' => 'set_badge',
            'action_payload' => [
                'badge' => $validated['result_badge'],
            ],
        ]);

        return redirect()->route('automation.rules')->with('success', 'Règle modifiée.');
    }

    public function toggle(AutomationRule $rule)
    {
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return redirect()->route('automation.rules')->with('success', 'Statut mis à jour.');
    }

    public function destroy(AutomationRule $rule)
    {
        $rule->delete();

        return redirect()->route('automation.rules')->with('success', 'Règle supprimée.');
    }
}
