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
            ->with(['replies', 'panelist'])
            ->orderBy('created_at', 'desc')
            ->get();

        $panelists = $event->panelists()->with('user')->get();

        // Séparer les questions filtrées (rejetées par l'Assistant Modérateur)
        $filteredByAI = $allQuestions->filter(function($q) {
            return $q->status == 'rejected' && $q->replies->contains('pseudo', 'Assistant Modérateur');
        });

        // Questions normales (non filtrées par lia ou filtrées manuellement)
        $questions = $allQuestions->reject(function($q) use ($filteredByAI) {
            return $filteredByAI->contains('id', $q->id);
        });

        return view('moderator.index', compact('event', 'questions', 'filteredByAI', 'panelists'));
    }

    /**
     * Mettre à jour le statut d'une question.
     */
    public function updateStatus(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        $event = $question->event;
        $newStatus = $request->status;
        
        // Vérifier que l'utilisateur est soit le propriétaire, soit un panéliste
        $isOwner = $event->user_id === Auth::id();
        $isPanelist = $event->panelists()->where('user_id', Auth::id())->exists();

        if (!$isOwner && !$isPanelist) {
            abort(403);
        }

        // Si on approuve une question qui était rejetée par l'IA (doublon/hors-sujet), on supprime la réponse auto de l'IA
        if ($newStatus === 'approved' && $question->status === 'rejected') {
            $question->replies()->where('pseudo', 'Assistant Modérateur')->delete();
        }

        // Si on passe une question en "answering", on remet les autres questions "answering" en "approved"
        if ($newStatus === 'answering') {
            $question->event->questions()->where('status', 'answering')->update(['status' => 'approved']);
        }

        $question->update(['status' => $newStatus]);

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Fetch questions HTML partials for polling.
     */
    public function fetchQuestionsPartial($id)
    {
        $event = Event::findOrFail($id);
        
        $allQuestions = $event->questions()
            ->with(['replies', 'panelist'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filteredByAI = $allQuestions->filter(function($q) {
            return $q->status == 'rejected' && $q->replies->contains('pseudo', 'Assistant Modérateur');
        });

        $questions = $allQuestions->reject(function($q) use ($filteredByAI) {
            return $filteredByAI->contains('id', $q->id);
        });

        $panelists = $event->panelists()->with('user')->get();
        $hands = $event->raisedHands()->where('status', '!=', 'dismissed')->orderBy('created_at', 'asc')->get();

        return response()->json([
            'main_html' => view('moderator.partials.questions_list', compact('questions'))->render(),
            'filtered_html' => view('moderator.partials.filtered_list', compact('filteredByAI'))->render(),
            'panelists_html' => view('moderator.partials.panelists_list', compact('panelists'))->render(),
            'hands_html' => view('moderator.partials.hands_list', compact('hands'))->render(),
            'counts' => [
                'active' => $questions->count(),
                'filtered' => $filteredByAI->count(),
                'pending' => $questions->where('status', 'pending')->count(),
                'answered' => $questions->where('status', 'answered')->count(),
                'total' => $allQuestions->count()
            ]
        ]);
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
        $hand = \App\Models\RaisedHand::find($id);
        
        if (!$hand) {
            return back()->with('info', "Cette intervention n'existe plus ou a déjà été traitée.");
        }

        $event = $hand->event;
        
        // Vérifier que l'utilisateur est soit le propriétaire, soit un panéliste
        $isOwner = $event->user_id === Auth::id();
        $isPanelist = $event->panelists()->where('user_id', Auth::id())->exists();

        if (!$isOwner && !$isPanelist) {
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

    /**
     * Update event settings.
     */
    public function updateSettings(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        if ($event->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'scheduled_at' => 'nullable|date',
            'moderation_enabled' => 'boolean',
            'anonymous_allowed' => 'boolean',
        ]);

        $event->update([
            'scheduled_at' => $data['scheduled_at'],
            'moderation_enabled' => $request->has('moderation_enabled'),
            'anonymous_allowed' => $request->has('anonymous_allowed'),
        ]);

        return back()->with('success', 'Paramètres mis à jour.');
    }

    /**
     * Start panelist presentation timer.
     */
    public function startPresentation(Request $request, $panelistId)
    {
        $panelist = \App\Models\Panelist::findOrFail($panelistId);
        if ($panelist->event->user_id !== Auth::id()) abort(403);

        $request->validate(['duration' => 'required|integer|min:1']);

        $panelist->update([
            'presentation_duration' => $request->duration,
            'presentation_started_at' => now(),
        ]);

        return back()->with('success', 'Chrono lancé pour ' . $panelist->user->name);
    }

    /**
     * Add time to presentation.
     */
    public function addPresentationTime(Request $request, $panelistId)
    {
        $panelist = \App\Models\Panelist::findOrFail($panelistId);
        if ($panelist->event->user_id !== Auth::id()) abort(403);

        $request->validate(['minutes' => 'required|integer|min:1']);

        $panelist->increment('presentation_duration', $request->minutes);

        return back()->with('success', 'Temps ajouté.');
    }

    /**
     * Stop presentation.
     */
    public function stopPresentation($panelistId)
    {
        $panelist = \App\Models\Panelist::findOrFail($panelistId);
        if ($panelist->event->user_id !== Auth::id()) abort(403);

        $panelist->update([
            'presentation_started_at' => null,
            'is_projecting' => false
        ]);

        return back()->with('success', 'Présentation terminée.');
    }
}
