<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Tâche - Todo App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Barre de navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('todos.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <i class="fas fa-tasks text-blue-500 text-2xl mr-2"></i>
                    <span class="text-xl font-semibold text-gray-800">Modifier la Tâche</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Messages de notification -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-4 w-4 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Fermer</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            <!-- Formulaire de modification -->
            <form action="{{ route('todos.update', $todo) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre de la tâche</label>
                        <input type="text" name="title" id="title" value="{{ $todo->title }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fichiers actuels</label>
                        @if($todo->files->count() > 0)
                            <div class="space-y-2">
                                @foreach($todo->files as $file)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            <span class="text-sm">{{ $file->original_filename }}</span>
                                            <span class="text-xs text-gray-500">({{ number_format($file->size / 1024, 2) }} KB)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('todos.download', $file) }}" 
                                               class="text-blue-500 hover:text-blue-600">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('todos.delete-file', $file) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-600">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucun fichier attaché</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ajouter des fichiers</label>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center px-4 py-2 bg-white border rounded-lg cursor-pointer hover:bg-gray-50">
                                <i class="fas fa-paperclip text-gray-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Sélectionner des fichiers</span>
                                <input type="file" name="files[]" multiple class="hidden">
                            </label>
                            <span class="text-sm text-gray-500">(Max 10MB par fichier)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('todos.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Affichage des noms de fichiers sélectionnés
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                const fileList = Array.from(files).map(file => file.name).join(', ');
                this.parentElement.querySelector('span').textContent = fileList;
            } else {
                this.parentElement.querySelector('span').textContent = 'Sélectionner des fichiers';
            }
        });

        // Auto-fermeture des notifications
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 3000);
            });
        });
    </script>
</body>
</html> 