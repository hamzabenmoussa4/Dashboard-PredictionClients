<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeatureCalculatorService;

class ComputeCustomerFeatures extends Command
{
    // Nom de la commande qu'on tapera dans le terminal
    protected $signature = 'features:compute';

    // Description affichée quand on liste les commandes
    protected $description = 'Calcule et enregistre les features pour tous les clients';

    // Méthode exécutée quand on lance la commande
    public function handle(): int
    {
        // Message d'information au début
        $this->info('Début du calcul des features clients...');

        // On instancie le service de calcul des features
        $service = new FeatureCalculatorService();

        // On lance le calcul pour tous les clients et on récupère le nombre traité
        $count = $service->computeForAllCustomers();

        // Message de fin avec le résultat
        $this->info("Calcul terminé. Clients traités : {$count}");

        // On retourne 0 pour dire "succès"
        return 0;
    }
}
