@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 to-white">
    <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg transform transition-all hover:scale-[1.01]" x-data="{ loading: false }">
        <!-- En-tête -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-user-plus text-blue-600 text-4xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
                Créez votre compte
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Ou
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    connectez-vous si vous avez déjà un compte
                </a>
            </p>
        </div>

        <!-- Formulaire -->
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" @submit="loading = true">
            @csrf
            <div class="space-y-4">
                <!-- Nom -->
                <div class="relative" x-data="{ focused: false }">
                    <label for="name" class="sr-only">Nom</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user" :class="{ 'text-blue-500': focused, 'text-gray-400': !focused }"></i>
                    </div>
                    <input id="name" name="name" type="text" required
                           @focus="focused = true"
                           @blur="focused = false"
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border @error('name') border-red-300 text-red-900 placeholder-red-300 @else border-gray-300 placeholder-gray-500 text-gray-900 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all"
                           placeholder="Votre nom"
                           value="{{ old('name') }}"
                           autocomplete="name">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="relative" x-data="{ focused: false }">
                    <label for="email" class="sr-only">Adresse email</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope" :class="{ 'text-blue-500': focused, 'text-gray-400': !focused }"></i>
                    </div>
                    <input id="email" name="email" type="email" required
                           @focus="focused = true"
                           @blur="focused = false"
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border @error('email') border-red-300 text-red-900 placeholder-red-300 @else border-gray-300 placeholder-gray-500 text-gray-900 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all"
                           placeholder="Adresse email"
                           value="{{ old('email') }}"
                           autocomplete="email">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="relative" x-data="{ focused: false }">
                    <label for="password" class="sr-only">Mot de passe</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock" :class="{ 'text-blue-500': focused, 'text-gray-400': !focused }"></i>
                    </div>
                    <input id="password" name="password" type="password" required
                           @focus="focused = true"
                           @blur="focused = false"
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border @error('password') border-red-300 text-red-900 placeholder-red-300 @else border-gray-300 placeholder-gray-500 text-gray-900 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all"
                           placeholder="Mot de passe"
                           autocomplete="new-password">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirmation du mot de passe -->
                <div class="relative" x-data="{ focused: false }">
                    <label for="password-confirm" class="sr-only">Confirmer le mot de passe</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock" :class="{ 'text-blue-500': focused, 'text-gray-400': !focused }"></i>
                    </div>
                    <input id="password-confirm" name="password_confirmation" type="password" required
                           @focus="focused = true"
                           @blur="focused = false"
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all"
                           placeholder="Confirmer le mot de passe"
                           autocomplete="new-password">
                </div>
            </div>

            <!-- Bouton d'inscription -->
            <div>
                <button type="submit" 
                        :class="{ 'opacity-75 cursor-wait': loading }"
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 ease-in-out transform hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas" :class="{ 'fa-spinner fa-spin': loading, 'fa-user-plus': !loading }"></i>
                    </span>
                    <span x-text="loading ? 'Inscription en cours...' : 'S\'inscrire'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 