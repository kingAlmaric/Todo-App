<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoFile;
use App\Models\TaskSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\TaskSuggestionService;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = auth()->user()->todos()->latest();

        // Filtrage par statut
        if ($request->has('status')) {
            switch ($request->status) {
                case 'completed':
                    $query->where('completed', true);
                    break;
                case 'active':
                    $query->where('completed', false);
                    break;
            }
        }

        // Recherche par titre
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Tri
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'alphabetical':
                    $query->orderBy('title', 'asc');
                    break;
                case 'reverse_alphabetical':
                    $query->orderBy('title', 'desc');
                    break;
            }
        }

        $todos = $query->get();

        if ($request->wantsJson()) {
            return response()->json($todos);
        }

        return view('todos', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'files.*' => 'file|max:10240' // Max 10MB par fichier
        ]);

        $todo = auth()->user()->todos()->create([
            'title' => $request->title,
            'completed' => false,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('todos', $filename, 'public');

                TodoFile::create([
                    'todo_id' => $todo->id,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json($todo->load('files'), 201);
        }

        return redirect()->route('todos.index')->with('success', 'Tâche ajoutée avec succès !');
    }

    public function edit(Todo $todo)
    {
        $this->authorize('update', $todo);
        return view('todos.edit', compact('todo'));
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'files.*' => 'file|max:10240'
        ]);

        $todo->update([
            'title' => $request->title
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('todos', $filename, 'public');

                TodoFile::create([
                    'todo_id' => $todo->id,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json($todo->load('files'));
        }

        return redirect()->route('todos.index')->with('success', 'Tâche mise à jour avec succès !');
    }

    public function toggle(Todo $todo)
    {
        $this->authorize('update', $todo);
        
        $todo->update([
            'completed' => !$todo->completed,
        ]);

        $message = $todo->completed ? 'Tâche marquée comme terminée !' : 'Tâche marquée comme non terminée !';

        if (request()->wantsJson()) {
            return response()->json($todo);
        }

        return redirect()->route('todos.index')->with('success', $message);
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);
        
        // Supprimer les fichiers associés
        foreach ($todo->files as $file) {
            Storage::disk('public')->delete('todos/' . $file->filename);
        }

        $todo->delete();

        if (request()->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('todos.index')->with('success', 'Tâche supprimée avec succès !');
    }

    public function downloadFile(TodoFile $file)
    {
        $this->authorize('download', $file->todo);
        return Storage::disk('public')->download('todos/' . $file->filename, $file->original_filename);
    }

    public function deleteFile(TodoFile $file)
    {
        $this->authorize('update', $file->todo);
        
        Storage::disk('public')->delete('todos/' . $file->filename);
        $file->delete();

        if (request()->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('todos.index')->with('success', 'Fichier supprimé avec succès !');
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'completed' => 'required|boolean'
        ]);

        $todos = auth()->user()->todos()->whereIn('id', $request->ids);
        $todos->update(['completed' => $request->completed]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tâches mises à jour avec succès']);
        }

        return redirect()->route('todos.index')->with('success', 'Tâches mises à jour avec succès !');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        $todos = auth()->user()->todos()->whereIn('id', $request->ids)->get();

        foreach ($todos as $todo) {
            foreach ($todo->files as $file) {
                Storage::disk('public')->delete('todos/' . $file->filename);
            }
        }

        $todos->each->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tâches supprimées avec succès']);
        }

        return redirect()->route('todos.index')->with('success', 'Tâches supprimées avec succès !');
    }

    public function suggestions()
    {
        try {
            $suggestions = TaskSuggestion::where('is_accepted', false)
                ->orderBy('suggested_at', 'desc')
                ->get();
            
            return view('todos.suggestions', compact('suggestions'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des suggestions: ' . $e->getMessage());
            return view('todos.suggestions', ['suggestions' => collect([])])
                ->with('error', 'Une erreur est survenue lors de la récupération des suggestions.');
        }
    }

    public function acceptSuggestion(TaskSuggestion $suggestion)
    {
        // Créer une nouvelle tâche à partir de la suggestion
        $todo = auth()->user()->todos()->create([
            'title' => $suggestion->title,
            'description' => $suggestion->description,
            'category' => $suggestion->category,
            'priority' => $suggestion->priority,
            'completed' => false
        ]);

        // Marquer la suggestion comme acceptée
        $suggestion->update(['is_accepted' => true]);

        return redirect()->route('todos.index')
            ->with('success', 'Suggestion acceptée et ajoutée à votre liste de tâches !');
    }

    public function rejectSuggestion(TaskSuggestion $suggestion)
    {
        $suggestion->delete();
        return redirect()->route('todos.suggestions')
            ->with('success', 'Suggestion rejetée.');
    }

    public function generateSuggestions(TaskSuggestionService $suggestionService)
    {
        try {
            $suggestionService->generateSuggestions();
            return redirect()->route('todos.suggestions')
                ->with('success', 'Nouvelles suggestions générées avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération des suggestions: ' . $e->getMessage());
            return redirect()->route('todos.suggestions')
                ->with('error', 'Une erreur est survenue lors de la génération des suggestions. Veuillez réessayer.');
        }
    }
}
