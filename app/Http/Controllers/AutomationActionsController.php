<?php

namespace App\Http\Controllers;

use App\Models\AutomationAction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AutomationActionsController extends Controller
{
    public function index(Request $request)
    {
        $badge = $request->query('badge', 'RISK');
        $q = trim((string) $request->query('q'));

        if (!in_array($badge, ['RISK', 'NORMAL', 'VIP'], true)) {
            $badge = 'RISK';
        }

        $customersQuery = Customer::where(function ($qq) use ($badge) {
            $qq->where('badge_override', $badge)
               ->orWhere(function ($q2) use ($badge) {
                   $q2->whereNull('badge_override')->where('badge_computed', $badge);
               });
        });

        if ($q !== '') {
            $customersQuery->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $customersQuery->orderBy('last_name')->orderBy('first_name')->get();

        $historyQuery = AutomationAction::with('customer')
            ->where('customer_badge', $badge);

        if ($q !== '') {
            $historyQuery->where(function ($sub) use ($q) {
                $sub->where('type', 'like', "%{$q}%")
                    ->orWhere('action_type', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('first_name', 'like', "%{$q}%")
                          ->orWhere('last_name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $history = $historyQuery
            ->orderByDesc('executed_at')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('automation.actions', [
            'badge' => $badge,
            'q' => $q,
            'customers' => $customers,
            'history' => $history,
        ]);
    }

    public function run(Request $request)
    {
        $validated = $request->validate([
            'badge' => ['required', Rule::in(['RISK', 'NORMAL', 'VIP'])],
            'customer_ids' => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['integer', 'min:1'],
            'action_type' => ['required', Rule::in(['email', 'notify', 'discount'])],
            'message' => ['nullable', 'string', 'max:500'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $badge = $validated['badge'];
        $customerIds = $validated['customer_ids'];
        $actionType = $validated['action_type'];

        $customers = Customer::whereIn('id', $customerIds)
            ->where(function ($q) use ($badge) {
                $q->where('badge_override', $badge)
                  ->orWhere(function ($q2) use ($badge) {
                      $q2->whereNull('badge_override')->where('badge_computed', $badge);
                  });
            })
            ->get();

        if ($customers->count() === 0) {
            return redirect()->route('automation.actions', ['badge' => $badge])->with('success', 'Aucun client valide.');
        }

        $message = $validated['message'] ?? null;
        $discount = $validated['discount_percent'] ?? null;

        if (in_array($actionType, ['email', 'notify'], true) && (!$message || trim($message) === '')) {
            return redirect()->route('automation.actions', ['badge' => $badge])->withErrors(['message' => 'Message obligatoire.']);
        }

        if ($actionType === 'discount' && ($discount === null || $discount === '')) {
            return redirect()->route('automation.actions', ['badge' => $badge])->withErrors(['discount_percent' => 'Discount obligatoire.']);
        }

        foreach ($customers as $c) {
            $payload = [
                'message' => in_array($actionType, ['email', 'notify'], true) ? $message : null,
                'discount_percent' => $actionType === 'discount' ? (float) $discount : null,
                'badge' => $badge,
            ];

            AutomationAction::create([
                'customer_id' => $c->id,
                'customer_badge' => $badge,
                'type' => $actionType,
                'action_type' => $actionType,
                'message' => $payload['message'],
                'discount_percent' => $payload['discount_percent'],
                'payload' => $payload,
                'status' => 'done',
                'executed_at' => now(),
            ]);
        }

        return redirect()->route('automation.actions', ['badge' => $badge])->with('success', 'Action enregistrée.');
    }
}
