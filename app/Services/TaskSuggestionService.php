<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\TaskSuggestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TaskSuggestionService
{
    public function generateSuggestions()
    {
        try {
            Log::info('Début de la génération des suggestions');
            
            // Vérifier si la table existe
            if (!Schema::hasTable('task_suggestions')) {
                Log::error('La table task_suggestions n\'existe pas');
                throw new \Exception('La table task_suggestions n\'existe pas');
            }

            // Vérifier la structure de la table
            $columns = Schema::getColumnListing('task_suggestions');
            Log::info('Colonnes de la table task_suggestions:', $columns);

            // Analyser les habitudes de l'utilisateur
            $userPatterns = $this->analyzeUserPatterns();
            Log::info('Patterns analysés:', $userPatterns);
            
            // Générer des suggestions basées sur les patterns
            $suggestions = $this->createSuggestionsFromPatterns($userPatterns);
            Log::info('Suggestions générées:', $suggestions);
            
            // Sauvegarder les suggestions
            foreach ($suggestions as $suggestion) {
                try {
                    $created = TaskSuggestion::create($suggestion);
                    Log::info('Suggestion créée:', $created->toArray());
                } catch (\Exception $e) {
                    Log::error('Erreur lors de la création d\'une suggestion: ' . $e->getMessage());
                    Log::error('Données de la suggestion:', $suggestion);
                }
            }
            
            Log::info('Suggestions sauvegardées avec succès');
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération des suggestions: ' . $e->getMessage());
            throw $e;
        }
    }

    private function analyzeUserPatterns()
    {
        try {
            // Vérifier si la table todos existe
            if (!Schema::hasTable('todos')) {
                Log::error('La table todos n\'existe pas');
                return [
                    'time_patterns' => collect([]),
                    'category_patterns' => collect([]),
                    'completion_patterns' => collect([])
                ];
            }

            $patterns = [
                'time_patterns' => $this->analyzeTimePatterns(),
                'category_patterns' => $this->analyzeCategoryPatterns(),
                'completion_patterns' => $this->analyzeCompletionPatterns()
            ];
            
            Log::info('Patterns utilisateur analysés:', $patterns);
            return $patterns;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse des patterns: ' . $e->getMessage());
            return [
                'time_patterns' => collect([]),
                'category_patterns' => collect([]),
                'completion_patterns' => collect([])
            ];
        }
    }

    private function analyzeTimePatterns()
    {
        try {
            $patterns = Todo::select(DB::raw('strftime("%H", created_at) as hour, COUNT(*) as count'))
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->limit(3)
                ->get();
                
            Log::info('Patterns temporels analysés:', $patterns->toArray());
            return $patterns;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse des patterns temporels: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function analyzeCategoryPatterns()
    {
        try {
            $patterns = Todo::select('category', DB::raw('COUNT(*) as count'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->limit(3)
                ->get();
                
            Log::info('Patterns de catégories analysés:', $patterns->toArray());
            return $patterns;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse des patterns de catégories: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function analyzeCompletionPatterns()
    {
        try {
            $patterns = Todo::select(
                DB::raw('strftime("%Y-%m-%d", created_at) as date, COUNT(*) as total, SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();
            
            Log::info('Patterns de complétion analysés:', $patterns->toArray());
            return $patterns;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse des patterns de complétion: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function createSuggestionsFromPatterns($patterns)
    {
        try {
            $suggestions = [];
            $currentTime = Carbon::now();
            
            // Créer des suggestions basées sur les heures préférées
            foreach ($patterns['time_patterns'] as $timePattern) {
                if ($currentTime->hour == $timePattern->hour) {
                    $suggestions[] = [
                        'title' => 'Tâche suggérée basée sur vos habitudes',
                        'description' => 'Cette tâche est suggérée en fonction de votre activité habituelle à cette heure.',
                        'category' => 'Suggestion',
                        'priority' => 'medium',
                        'suggested_at' => $currentTime,
                        'context_data' => [
                            'based_on' => 'time_pattern',
                            'hour' => $timePattern->hour,
                            'confidence' => $timePattern->count / 10
                        ]
                    ];
                }
            }

            // Créer des suggestions basées sur les catégories préférées
            foreach ($patterns['category_patterns'] as $categoryPattern) {
                $suggestions[] = [
                    'title' => "Nouvelle tâche {$categoryPattern->category}",
                    'description' => "Basé sur vos tâches fréquentes dans la catégorie {$categoryPattern->category}",
                    'category' => $categoryPattern->category,
                    'priority' => 'high',
                    'suggested_at' => $currentTime,
                    'context_data' => [
                        'based_on' => 'category_pattern',
                        'category' => $categoryPattern->category,
                        'confidence' => $categoryPattern->count / 10
                    ]
                ];
            }

            Log::info('Suggestions créées:', $suggestions);
            return $suggestions;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des suggestions: ' . $e->getMessage());
            return [];
        }
    }
} 