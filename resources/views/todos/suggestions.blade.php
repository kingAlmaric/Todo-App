<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggestions de Tâches Intelligentes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Barre de navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-robot text-blue-500 text-2xl mr-2"></i>
                    <span class="text-xl font-semibold text-gray-800">Suggestions Intelligentes</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('todos.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux tâches
                    </a>
                    <form action="{{ route('todos.generate-suggestions') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            <i class="fas fa-sync-alt mr-2"></i>Générer de nouvelles suggestions
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- En-tête -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Suggestions de Tâches</h1>
                <p class="text-gray-600">Basées sur vos habitudes et préférences</p>
            </div>

            <!-- Liste des suggestions -->
            <div class="space-y-4">
                @forelse($suggestions as $suggestion)
                    <div x-data="{ showDetails: false }"
                         class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $suggestion->title }}</h3>
                                <p class="text-gray-600 mb-4">{{ $suggestion->description }}</p>
                                
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                        {{ $suggestion->category }}
                                    </span>
                                    <span class="px-3 py-1 bg-{{ $suggestion->priority === 'high' ? 'red' : ($suggestion->priority === 'medium' ? 'yellow' : 'green') }}-100 
                                               text-{{ $suggestion->priority === 'high' ? 'red' : ($suggestion->priority === 'medium' ? 'yellow' : 'green') }}-800 
                                               rounded-full text-sm">
                                        {{ ucfirst($suggestion->priority) }}
                                    </span>
                                </div>

                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    Suggéré le {{ $suggestion->suggested_at->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button @click="showDetails = !showDetails" 
                                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                    <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                                <form action="{{ route('todos.accept-suggestion', $suggestion) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-green-500 hover:text-green-600 transition-colors duration-200 transform hover:scale-110">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('todos.reject-suggestion', $suggestion) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-600 transition-colors duration-200 transform hover:scale-110">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Détails de la suggestion -->
                        <div x-show="showDetails"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="mt-4 pt-4 border-t">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Pourquoi cette suggestion ?</h4>
                            <div class="text-sm text-gray-600">
                                @if($suggestion->context_data['based_on'] === 'time_pattern')
                                    <p>Cette suggestion est basée sur votre activité habituelle à cette heure de la journée.</p>
                                    <p class="mt-1">Confiance : {{ number_format($suggestion->context_data['confidence'] * 100, 0) }}%</p>
                                @elseif($suggestion->context_data['based_on'] === 'category_pattern')
                                    <p>Cette suggestion est basée sur vos tâches fréquentes dans la catégorie "{{ $suggestion->context_data['category'] }}".</p>
                                    <p class="mt-1">Confiance : {{ number_format($suggestion->context_data['confidence'] * 100, 0) }}%</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-robot text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Aucune suggestion disponible pour le moment</p>
                        <p class="text-sm text-gray-400 mt-2">Générez de nouvelles suggestions pour voir des recommandations personnalisées</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html> 