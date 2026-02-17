<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Aucun service à enregistrer pour le moment.
    }

    public function boot(): void
    {
        // On force Laravel à utiliser notre vue de pagination custom (sans SVG énorme).
        Paginator::defaultView('vendor.pagination.app');

        // Même chose pour la pagination simple si tu l’utilises un jour.
        Paginator::defaultSimpleView('vendor.pagination.simple');
    }
}

