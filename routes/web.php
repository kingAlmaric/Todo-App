<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Auth::routes();

// Routes protégées (nécessitant une authentification)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('todos.index');
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::patch('/todos/{todo}', [TodoController::class, 'toggle'])->name('todos.toggle');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::get('/todos/files/{file}', [TodoController::class, 'downloadFile'])->name('todos.download');
    Route::delete('/todos/files/{file}', [TodoController::class, 'deleteFile'])->name('todos.delete-file');

    // Routes pour les suggestions de tâches
    Route::get('/todos/suggestions', [TodoController::class, 'suggestions'])->name('todos.suggestions');
    Route::post('/todos/suggestions/{suggestion}/accept', [TodoController::class, 'acceptSuggestion'])->name('todos.accept-suggestion');
    Route::delete('/todos/suggestions/{suggestion}/reject', [TodoController::class, 'rejectSuggestion'])->name('todos.reject-suggestion');
    Route::post('/todos/generate-suggestions', [TodoController::class, 'generateSuggestions'])->name('todos.generate-suggestions');
});
