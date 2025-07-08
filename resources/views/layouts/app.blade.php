<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Todo App') }}</title>
    
    <!-- Optimisation du chargement des ressources -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <script src="https://cdn.tailwindcss.com" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Optimisation des styles */
        :root {
            --primary-color: #1a73e8;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .nav {
            background: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .main-content {
            padding: 2rem 0;
        }

        /* Animation des transitions */
        .fade-enter {
            opacity: 0;
        }

        .fade-enter-active {
            opacity: 1;
            transition: opacity 200ms ease-in;
        }

        /* Optimisation pour mobile */
        @media (max-width: 640px) {
            .container {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2 hover:opacity-90 transition-opacity">
                        <i class="fas fa-tasks text-2xl"></i>
                        <span class="text-xl font-semibold">Todo App</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm font-medium hidden sm:inline">{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger flex items-center space-x-2">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="hidden sm:inline">Déconnexion</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn bg-white text-blue-600 hover:bg-blue-50">Connexion</a>
                        <a href="{{ route('register') }}" class="btn bg-blue-500 text-white hover:bg-blue-600">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Script d'optimisation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Préchargement des images
            const images = document.querySelectorAll('img[data-src]');
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    });
                });

                images.forEach(img => imageObserver.observe(img));
            }
        });
    </script>
</body>
</html> 