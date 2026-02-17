<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerCrudController extends Controller
{
    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $q = trim((string) $request->query('q'));

        $editCustomer = null;

        if ($editId && is_numeric($editId)) {
            $editCustomer = Customer::find((int) $editId);
        }

        $customersQuery = Customer::orderByDesc('created_at');

        if ($q !== '') {
            $customersQuery->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhere('badge_override', 'like', "%{$q}%")
                    ->orWhere('badge_computed', 'like', "%{$q}%");
            });
        }

        $customers = $customersQuery->paginate(15)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'editCustomer' => $editCustomer,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Customer::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'badge_computed' => 'NORMAL',
            'badge_override' => null,
            'badge_updated_at' => now(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Client créé.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'badge_override' => ['nullable', Rule::in(['NORMAL', 'VIP', 'RISK'])],
        ]);

        $customer->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'badge_override' => $validated['badge_override'] ?? null,
            'badge_updated_at' => now(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Client modifié.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Client supprimé.');
    }
}

