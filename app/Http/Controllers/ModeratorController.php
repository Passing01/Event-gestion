<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorController extends Controller
{
    /**
     * Console de modération pour un événement.
     */
    public function index($id)
    {
        $event = Event::findOrFail($id);
        
        // Toutes les questions sauf celles rejetées par l'IA
        $allQuestions = $event->questions()
            ->with(['replies'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Séparer les questions filtrées (rejetées par l'Assistant Modérateur)
        $filteredByAI = $allQuestions->filter(function($q) {
            return $q->status == 'rejected' && $q->replies->contains('pseudo', 'Assistant Modérateur');
        });

        // Questions normales (non filtrées par lia ou filtrées manuellement)
        $questions = $allQuestions->reject(function($q) use ($filteredByAI) {
            return $filteredByAI->contains('id', $q->id);
        });

        return view('moderator.index', compact('event', 'questions', 'filteredByAI'));
    }

    /**
     * Mettre à jour le statut d'une question.
     */
    public function updateStatus(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        $event = $question->event;
        
        // Vérifier que l'utilisateur est soit le propriétaire, soit un panéliste
        $isOwner = $event->user_id === Auth::id();
        $isPanelist = $event->panelists()->where('user_id', Auth::id())->exists();

        if (!$isOwner && !$isPanelist) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,archived,answering,answered',
        ]);

        // Si on passe une question en "answering", on remet les autres questions "answering" en "approved"
        if ($data['status'] === 'answering') {
            $question->event->questions()->where('status', 'answering')->update(['status' => 'approved']);
        }

        $question->update(['status' => $data['status']]);

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Modifier le contenu d'une question (correction rapide).
     */
    public function updateContent(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        if ($question->event->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $question->update(['content' => $data['content']]);

        return back()->with('success', 'Question modifiée.');
    }

    /**
     * Répondre à une question en tant que modérateur.
     */
    public function storeReply(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info("storeReply called for question ID: " . $id, $request->all());
        $question = Question::findOrFail($id);
        $event = $question->event;
        
        // Vérifier que l'utilisateur est soit le propriétaire, soit un panéliste
        $isOwner = $event->user_id === Auth::id();
        $isPanelist = $event->panelists()->where('user_id', Auth::id())->exists();

        if (!$isOwner && !$isPanelist) {
            abort(403);
        }

        try {
            $data = $request->validate([
                'content' => 'nullable|string|max:5000',
                'audio' => 'nullable|file|mimes:webm,mp3,wav,ogg|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error("Validation failed in storeReply: " . json_encode($e->errors()));
            throw $e;
        }

        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('replies/audio', 'public');
        }

        if (!$data['content'] && !$audioPath) {
            return back()->with('error', 'Vous devez fournir une réponse ou un message vocal.');
        }

        $question->replies()->create([
            'pseudo' => Auth::user()->name,
            'content' => $data['content'],
            'audio_path' => $audioPath,
            'is_moderator' => true,
        ]);

        return back()->with('success', 'Réponse envoyée.');
    }

    /**
     * Mettre à jour le statut d'une main levée.
     */
    public function updateHandStatus(Request $request, $id)
    {
        $hand = \App\Models\RaisedHand::findOrFail($id);
        
        if ($hand->event->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:pending,called,dismissed']);
        
        if ($request->status === 'dismissed') {
            $hand->delete();
        } else {
            $hand->update(['status' => $request->status]);
        }

        return back()->with('success', 'Statut mis à jour.');
    }
}
