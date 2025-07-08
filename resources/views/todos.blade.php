<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Liste de Tâches</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .slide-enter {
            transform: translateX(-100%);
            opacity: 0;
        }
        .slide-enter-active {
            transform: translateX(0);
            opacity: 1;
            transition: all 0.3s ease-out;
        }
        .slide-exit {
            transform: translateX(0);
            opacity: 1;
        }
        .slide-exit-active {
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-in;
        }
        .fade-enter {
            opacity: 0;
            transform: translateY(-10px);
        }
        .fade-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-out;
        }
        .fade-exit {
            opacity: 1;
            transform: translateY(0);
        }
        .fade-exit-active {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Barre de navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-tasks text-blue-500 text-2xl mr-2"></i>
                    <span class="text-xl font-semibold text-gray-800">Todo App</span>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Bouton de déconnexion -->
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </button>
                    </form>

                    <!-- Filtres -->
                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-filter"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('todos.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ !request('status') ? 'bg-gray-100' : '' }}">
                                    Toutes les tâches
                                </a>
                                <a href="{{ route('todos.index', ['status' => 'completed']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') === 'completed' ? 'bg-gray-100' : '' }}">
                                    Tâches terminées
                                </a>
                                <a href="{{ route('todos.index', ['status' => 'active']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') === 'active' ? 'bg-gray-100' : '' }}">
                                    Tâches en cours
                                </a>
                            </div>
                        </div>

                        <!-- Tri -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-sort"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('todos.index', ['sort' => 'newest']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'newest' ? 'bg-gray-100' : '' }}">
                                    Plus récentes
                                </a>
                                <a href="{{ route('todos.index', ['sort' => 'oldest']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'oldest' ? 'bg-gray-100' : '' }}">
                                    Plus anciennes
                                </a>
                                <a href="{{ route('todos.index', ['sort' => 'alphabetical']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'alphabetical' ? 'bg-gray-100' : '' }}">
                                    Alphabétique (A-Z)
                                </a>
                                <a href="{{ route('todos.index', ['sort' => 'reverse_alphabetical']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'reverse_alphabetical' ? 'bg-gray-100' : '' }}">
                                    Alphabétique (Z-A)
                                </a>
                            </div>
                        </div>

                        <!-- Recherche -->
                        <form action="{{ route('todos.index') }}" method="GET" class="flex items-center" x-data="{ search: '{{ request('search') }}' }">
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       x-model="search"
                                       @input.debounce.300ms="$el.form.submit()"
                                       placeholder="Rechercher..." 
                                       class="px-3 py-1 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div x-show="search.length > 0" 
                                     @click="search = ''; $el.form.submit()"
                                     class="absolute right-2 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                        </form>
                    </div>

                    <!-- Lien vers les suggestions -->
                    <a href="{{ route('todos.suggestions') }}" 
                       class="flex items-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        <i class="fas fa-robot mr-2"></i>
                        Suggestions IA
                    </a>

                    <span class="text-sm text-gray-600">
                        {{ $todos->count() }} tâche(s)
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Filtres actifs -->
            @if(request('status') || request('sort') || request('search'))
                <div class="mb-4 flex flex-wrap gap-2">
                    @if(request('status'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            {{ request('status') === 'completed' ? 'Tâches terminées' : 'Tâches en cours' }}
                            <a href="{{ route('todos.index', ['sort' => request('sort'), 'search' => request('search')]) }}" 
                               class="ml-2 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('sort'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            {{ request('sort') === 'newest' ? 'Plus récentes' : 
                               (request('sort') === 'oldest' ? 'Plus anciennes' : 
                               (request('sort') === 'alphabetical' ? 'Alphabétique (A-Z)' : 'Alphabétique (Z-A)')) }}
                            <a href="{{ route('todos.index', ['status' => request('status'), 'search' => request('search')]) }}" 
                               class="ml-2 text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                            Recherche: {{ request('search') }}
                            <a href="{{ route('todos.index', ['status' => request('status'), 'sort' => request('sort')]) }}" 
                               class="ml-2 text-purple-600 hover:text-purple-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
            @endif

            <!-- Messages de notification -->
            <div x-data="{ show: true }" 
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 @click.away="show = false">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-4 w-4 text-green-500" role="button" @click="show = false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Fermer</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif
            </div>

            <!-- En-tête -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Les Tâches</h1>
                <p class="text-gray-600">Organisez vos tâches quotidiennes</p>
            </div>
            
            <!-- Formulaire d'ajout -->
            <div x-data="{ isDragging: false }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false"
                 :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                 class="border-2 border-dashed rounded-lg p-6 mb-8 transition-colors duration-200">
                <form action="{{ route('todos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex gap-2">
                            <input type="text" name="title" placeholder="Nouvelle tâche..." 
                                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center transform hover:scale-105">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center px-4 py-2 bg-white border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-paperclip text-gray-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Ajouter des fichiers</span>
                                <input type="file" name="files[]" multiple class="hidden">
                            </label>
                            <span class="text-sm text-gray-500">(Max 10MB par fichier)</span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Liste des tâches -->
            <div class="bg-white rounded-lg shadow-sm">
                @forelse($todos as $todo)
                    <div x-data="{ showDetails: false }"
                         class="p-4 border-b last:border-b-0 hover:bg-gray-50 transition-all duration-200"
                         :class="{ 'bg-blue-50': showDetails }">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <form action="{{ route('todos.toggle', $todo->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="checkbox" {{ $todo->completed ? 'checked' : '' }} 
                                           onChange="this.form.submit()" 
                                           class="w-5 h-5 rounded border-gray-300 text-blue-500 focus:ring-blue-500 transition-colors duration-200">
                                </form>
                                <span class="{{ $todo->completed ? 'line-through text-gray-400' : 'text-gray-700' }} transition-all duration-200">
                                    {{ $todo->title }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">
                                    {{ $todo->created_at->format('d/m/Y H:i') }}
                                </span>
                                <button @click="showDetails = !showDetails" 
                                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                    <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                                <a href="{{ route('todos.edit', $todo) }}" 
                                   class="text-blue-500 hover:text-blue-600 transition-colors duration-200 transform hover:scale-110">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('todos.destroy', $todo->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-600 transition-colors duration-200 transform hover:scale-110">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Détails de la tâche -->
                        <div x-show="showDetails"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2">
                            <!-- Liste des fichiers -->
                            @if($todo->files->count() > 0)
                                <div class="ml-8 mt-2 space-y-1">
                                    @foreach($todo->files as $file)
                                        <div class="flex items-center gap-2 text-sm group">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            <a href="{{ route('todos.download', $file) }}" 
                                               class="text-blue-500 hover:text-blue-600 transition-colors duration-200">
                                                {{ $file->original_filename }}
                                            </a>
                                            <span class="text-gray-400">
                                                ({{ number_format($file->size / 1024, 2) }} KB)
                                            </span>
                                            <form action="{{ route('todos.delete-file', $file) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="ml-8 mt-2 text-sm text-gray-500">Aucun fichier attaché</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Aucune tâche pour le moment</p>
                        <p class="text-sm text-gray-400 mt-2">Ajoutez une nouvelle tâche pour commencer</p>
                    </div>
                @endforelse
            </div>

            <!-- Pied de page -->
            <footer class="mt-8 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} Todo App. Tous droits réservés.</p>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Animation de suppression
        document.querySelectorAll('form[action*="destroy"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                    e.preventDefault();
                }
            });
        });

        // Affichage des noms de fichiers sélectionnés
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                const fileList = Array.from(files).map(file => file.name).join(', ');
                this.parentElement.querySelector('span').textContent = fileList;
            } else {
                this.parentElement.querySelector('span').textContent = 'Ajouter des fichiers';
            }
        });

        // Animation de glisser-déposer
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.querySelector('.border-dashed');
            const fileInput = dropZone.querySelector('input[type="file"]');

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500', 'bg-blue-50');
                const files = e.dataTransfer.files;
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            });
        });

        // Gestion des opérations en masse
        document.addEventListener('DOMContentLoaded', function() {
            const selectedTodos = new Set();
            const bulkActions = document.querySelector('.bulk-actions');

            // Fonction pour mettre à jour l'interface des actions en masse
            function updateBulkActions() {
                if (selectedTodos.size > 0) {
                    bulkActions.classList.remove('hidden');
                    bulkActions.classList.add('flex');
                } else {
                    bulkActions.classList.add('hidden');
                    bulkActions.classList.remove('flex');
                }
            }

            // Gestion de la sélection des tâches
            document.querySelectorAll('.todo-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedTodos.add(this.value);
                    } else {
                        selectedTodos.delete(this.value);
                    }
                    updateBulkActions();
                });
            });

            // Action de marquer comme terminé
            document.querySelector('.bulk-complete').addEventListener('click', async function() {
                if (selectedTodos.size === 0) return;

                try {
                    const response = await fetch('/todos/bulk-toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ids: Array.from(selectedTodos),
                            completed: true
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });

            // Action de marquer comme non terminé
            document.querySelector('.bulk-incomplete').addEventListener('click', async function() {
                if (selectedTodos.size === 0) return;

                try {
                    const response = await fetch('/todos/bulk-toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ids: Array.from(selectedTodos),
                            completed: false
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });

            // Action de suppression en masse
            document.querySelector('.bulk-delete').addEventListener('click', async function() {
                if (selectedTodos.size === 0) return;

                if (!confirm(`Êtes-vous sûr de vouloir supprimer ${selectedTodos.size} tâche(s) ?`)) {
                    return;
                }

                try {
                    const response = await fetch('/todos/bulk-delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ids: Array.from(selectedTodos)
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        });

        // Animations de transition
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des notifications
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 3000);
            });

            // Animation des tâches
            const todos = document.querySelectorAll('.todo-item');
            todos.forEach((todo, index) => {
                todo.style.opacity = '0';
                todo.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    todo.style.transition = 'all 0.3s ease-out';
                    todo.style.opacity = '1';
                    todo.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html> 