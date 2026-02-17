<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\AutomationAction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $badge = $request->query('badge', 'RISK');
        $q = trim((string) $request->query('q'));

        if (!in_array($badge, ['RISK', 'NORMAL', 'VIP'], true)) {
            $badge = 'RISK';
        }

        // KPI
        $totalCustomers = (int) Customer::count();
        $totalOrders = (int) Order::count();
        $totalRevenue = (float) Order::sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0.0;

        // Actions pending (si tu utilises status pending)
        $pendingActionsCount = (int) AutomationAction::where('status', 'pending')->count();

        // Comptage badges (override sinon computed)
        $countRisk = (int) Customer::where(function ($qq) {
            $qq->where('badge_override', 'RISK')
               ->orWhere(function ($q2) {
                   $q2->whereNull('badge_override')->where('badge_computed', 'RISK');
               });
        })->count();

        $countNormal = (int) Customer::where(function ($qq) {
            $qq->where('badge_override', 'NORMAL')
               ->orWhere(function ($q2) {
                   $q2->whereNull('badge_override')->where('badge_computed', 'NORMAL');
               });
        })->count();

        $countVip = (int) Customer::where(function ($qq) {
            $qq->where('badge_override', 'VIP')
               ->orWhere(function ($q2) {
                   $q2->whereNull('badge_override')->where('badge_computed', 'VIP');
               });
        })->count();

        // Clients filtrés pour le tableau principal
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

        $filteredCustomers = $customersQuery
            ->orderByDesc('badge_updated_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        // Dernières commandes
        $recentOrders = Order::with('customer')
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // ✅ IMPORTANT : Dernières actions = uniquement tes actions "réelles"
        // On filtre sur type OU action_type (au cas où)
        $recentActions = AutomationAction::with('customer')
            ->where(function ($qq) {
                $qq->whereIn('type', ['email', 'notify', 'discount'])
                   ->orWhereIn('action_type', ['email', 'notify', 'discount']);
            })
            ->orderByDesc('executed_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard.index', [
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'avgOrderValue' => $avgOrderValue,
            'pendingActionsCount' => $pendingActionsCount,

            'countRisk' => $countRisk,
            'countNormal' => $countNormal,
            'countVip' => $countVip,

            'badge' => $badge,
            'q' => $q,
            'filteredCustomers' => $filteredCustomers,

            'recentOrders' => $recentOrders,
            'recentActions' => $recentActions,
        ]);
    }
}
