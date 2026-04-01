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
        $event = Auth::user()->events()->findOrFail($id);
        $questions = $event->questions()->orderBy('votes_count', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('moderator.index', compact('event', 'questions'));
    }

    /**
     * Mettre à jour le statut d'une question.
     */
    public function updateStatus(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        // Vérifier que l'utilisateur est bien le propriétaire de l'événement
        if ($question->event->user_id !== Auth::id()) {
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
            'content' => 'required|string|max:500',
        ]);

        $question->update(['content' => $data['content']]);

        return back()->with('success', 'Question modifiée.');
    }

    /**
     * Répondre à une question en tant que modérateur.
     */
    public function storeReply(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        if ($question->event->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'content' => 'nullable|string|max:500',
            'audio' => 'nullable|file|mimes:webm,mp3,wav,ogg|max:5120',
        ]);

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
