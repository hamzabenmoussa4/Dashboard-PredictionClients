<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderCrudController extends Controller
{
    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $q = trim((string) $request->query('q'));

        $editOrder = null;

        if ($editId && is_numeric($editId)) {
            $editOrder = Order::find((int) $editId);
        }

        $customers = Customer::orderBy('last_name')->orderBy('first_name')->get();

        $ordersQuery = Order::with('customer')->orderByDesc('ordered_at')->orderByDesc('id');

        if ($q !== '') {
            $ordersQuery->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhere('currency', 'like', "%{$q}%")
                    ->orWhere('total_amount', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('first_name', 'like', "%{$q}%")
                          ->orWhere('last_name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $orders = $ordersQuery->paginate(15)->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'customers' => $customers,
            'editOrder' => $editOrder,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_number' => ['required', 'string', 'max:255', 'unique:orders,order_number'],
            'status' => ['required', Rule::in(['pending', 'paid', 'cancelled', 'refunded'])],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'ordered_at' => ['required', 'date'],
        ]);

        Order::create($validated);

        return redirect()->route('orders.index')->with('success', 'Commande créée.');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_number' => ['required', 'string', 'max:255', Rule::unique('orders', 'order_number')->ignore($order->id)],
            'status' => ['required', Rule::in(['pending', 'paid', 'cancelled', 'refunded'])],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'ordered_at' => ['required', 'date'],
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'Commande modifiée.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Commande supprimée.');
    }
}
