<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\PredictionEngine;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        // Quand un client est créé, on calcule ses 3 predictions
        app(PredictionEngine::class)->refreshForCustomer($customer);
    }

    public function updated(Customer $customer): void
    {
        // Si tu modifies status/infos, on peut recalculer aussi (optionnel)
        app(PredictionEngine::class)->refreshForCustomer($customer);
    }
}
