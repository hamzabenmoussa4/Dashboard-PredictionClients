<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PredictionService;

class RunPredictions extends Command
{
    // Nom de la commande qu'on tapera dans le terminal
    protected $signature = 'predictions:run {--model=rules-v1}';

    // Description affichée dans la liste des commandes
    protected $description = 'Calcule et enregistre les prédictions (scores) pour tous les clients';

    public function handle(): int
    {
        // Message de début
        $this->info('Début des prédictions...');

        // On récupère l'option --model (ex: rules-v1)
        $modelVersion = (string) $this->option('model');

        // On instancie le service de prédiction
        $service = new PredictionService();

        // On lance les prédictions pour tous les clients
        $count = $service->predictForAllCustomers($modelVersion);

        // Message de fin avec le nombre de clients traités
        $this->info("Prédictions terminées. Clients traités : {$count}");

        // Code 0 = succès
        return 0;
    }
}
