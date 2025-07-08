@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 to-white">
    <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg transform transition-all hover:scale-[1.01]">
        <!-- En-tête -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-tasks text-blue-600 text-4xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
                Connexion à Todo App
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Ou
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    créez un compte si vous n'en avez pas
                </a>
            </p>
        </div>

        <!-- Formulaire -->
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="space-y-4 -space-y-px">
                <!-- Email -->
                <div class="relative">
                    <label for="email" class="sr-only">Adresse email</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border @error('email') border-red-300 text-red-900 placeholder-red-300 @else border-gray-300 placeholder-gray-500 text-gray-900 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-colors"
                           placeholder="Adresse email"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="relative">
                    <label for="password" class="sr-only">Mot de passe</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" required
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border @error('password') border-red-300 text-red-900 placeholder-red-300 @else border-gray-300 placeholder-gray-500 text-gray-900 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-colors"
                           placeholder="Mot de passe">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Options supplémentaires -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Se souvenir de moi
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Mot de passe oublié ?
                        </a>
                    </div>
                @endif
            </div>

            <!-- Bouton de connexion -->
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 ease-in-out transform hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400 transition-colors"></i>
                    </span>
                    Se connecter
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 