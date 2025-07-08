<?php

namespace App\Console\Commands;

use App\Services\TaskSuggestionService;
use Illuminate\Console\Command;

class GenerateTaskSuggestions extends Command
{
    protected $signature = 'tasks:generate-suggestions';
    protected $description = 'Génère des suggestions de tâches basées sur les habitudes de l\'utilisateur';

    public function handle(TaskSuggestionService $suggestionService)
    {
        $this->info('Génération des suggestions de tâches...');
        
        try {
            $suggestionService->generateSuggestions();
            $this->info('Suggestions générées avec succès !');
        } catch (\Exception $e) {
            $this->error('Erreur lors de la génération des suggestions : ' . $e->getMessage());
        }
    }
} 