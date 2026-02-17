<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Customer;
use App\Services\PredictionEngine;

class OrderObserver
{
    public function created(Order $order): void
    {
        $customer = Customer::find($order->customer_id);

        if ($customer) {
            app(PredictionEngine::class)->refreshForCustomer($customer);
        }
    }

    public function updated(Order $order): void
    {
        $customer = Customer::find($order->customer_id);

        if ($customer) {
            app(PredictionEngine::class)->refreshForCustomer($customer);
        }
    }

    public function deleted(Order $order): void
    {
        $customer = Customer::find($order->customer_id);

        if ($customer) {
            app(PredictionEngine::class)->refreshForCustomer($customer);
        }
    }
}
