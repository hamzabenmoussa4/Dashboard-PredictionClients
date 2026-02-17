<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RuleEngineService;

class RunAutomationRules extends Command
{
    // Nom de la commande à exécuter
    protected $signature = 'automation:run';

    // Description affichée dans la liste des commandes
    protected $description = 'Exécute les règles d’automatisation et génère des actions';

    public function handle(): int
    {
        // Message de début
        $this->info('Démarrage du moteur d’automatisation...');

        // On instancie le service du moteur
        $service = new RuleEngineService();

        // On exécute le moteur sur tous les clients
        $actionsCount = $service->runForAllCustomers();

        // Message de fin avec le nombre d'actions créées
        $this->info("Automatisation terminée. Actions créées : {$actionsCount}");

        // 0 = succès
        return 0;
    }
}
