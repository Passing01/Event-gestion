<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Page d'accueil pour rejoindre un événement.
     */
    public function joinForm(Request $request)
    {
        $code = $request->query('code');
        return view('participant.join', compact('code'));
    }

    /**
     * Rejoindre un événement avec un code et un pseudo.
     */
    public function join(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|exists:events,code',
            'pseudo' => 'required|string|max:50',
        ]);

        $event = Event::where('code', $data['code'])->firstOrFail();

        if ($event->status !== 'active') {
            return back()->withErrors(['code' => 'Cet événement est actuellement désactivé ou archivé.']);
        }

        // Stocker le pseudo et l'event en session
        session([
            'participant_pseudo' => $data['pseudo'],
            'current_event_id' => $event->id,
            'current_event_code' => $event->code,
        ]);

        return redirect()->route('participant.event', $event->code);
    }

    /**
     * Interface de l'événement pour le participant.
     */
    public function eventInterface($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        
        if ($event->status !== 'active') {
            return redirect()->route('participant.join')->withErrors(['code' => 'Cet événement est actuellement désactivé ou archivé.']);
        }

        if (!session('participant_pseudo') || session('current_event_code') != $code) {
            return redirect()->route('participant.join', ['code' => $code]);
        }

        $pseudo = session('participant_pseudo');

        // Récupérer les questions approuvées, en cours, répondues 
        // OU les questions en attente de l'utilisateur actuel
        $questions = $event->questions()
            ->where(function($query) use ($pseudo) {
                $query->whereIn('status', ['approved', 'answering', 'answered'])
                      ->orWhere(function($q) use ($pseudo) {
                          $q->where('status', 'pending')
                            ->where('pseudo', $pseudo);
                      });
            })
            ->orderBy('votes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('participant.event', compact('event', 'questions'));
    }

    /**
     * Poser une question.
     */
    public function storeQuestion(Request $request, $code, \App\Services\GeminiService $gemini)
    {
        $event = Event::where('code', $code)->firstOrFail();
        
        if (!session('participant_pseudo')) {
            return redirect()->route('participant.join', ['code' => $code]);
        }

        $data = $request->validate([
            'content' => 'nullable|string|max:5000',
            'audio' => 'nullable|file|mimes:webm,mp3,wav,ogg|max:5120', // 5MB max
        ]);

        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('questions/audio', 'public');
        }

        if (!$data['content'] && !$audioPath) {
            return back()->with('error', 'Vous devez fournir une question ou un message vocal.');
        }

        // AI Auto-Moderation (only if content exists)
        if ($data['content'] && $event->description) {
            $moderation = $gemini->moderateQuestion($data['content'], $event);

            if ($moderation['status'] !== 'ok') {
                // Créer la question quand même, mais en mode "rejected" (filtrée par IA)
                $question = $event->questions()->create([
                    'pseudo' => session('participant_pseudo'),
                    'content' => $data['content'],
                    'audio_path' => $audioPath,
                    'status' => 'rejected', // On utilise rejected pour les cacher du flux principal
                ]);

                $replyContent = $moderation['message'];
                if ($moderation['status'] === 'duplicate' && !empty($moderation['suggestion'])) {
                    $replyContent .= " Voici la réponse déjà donnée : " . $moderation['suggestion'];
                }

                $question->replies()->create([
                    'pseudo' => 'Assistant Modérateur', // Pseudo spécial
                    'content' => $replyContent,
                    'is_moderator' => true,
                ]);

                $msg = $moderation['status'] === 'duplicate' ? 'Cette question a déjà été traitée.' : 'Cette question semble hors-sujet.';
                return back()->with('success', 'Votre question a été traitée automatiquement : ' . $msg);
            }
        }

        // Si tout est OK, on crée la question normalement
        $event->questions()->create([
            'pseudo' => session('participant_pseudo'),
            'content' => $data['content'],
            'audio_path' => $audioPath,
            'status' => $event->moderation_enabled ? 'pending' : 'approved',
        ]);

        return back()->with('success', 'Votre question a été envoyée !' . ($event->moderation_enabled ? ' Elle sera visible après modération.' : ''));
    }

    /**
     * Voter pour une question.
     */
    public function vote(Request $request, $id)
    {
        $question = \App\Models\Question::findOrFail($id);
        
        // On pourrait limiter à un vote par session/question
        $voted = session('voted_questions', []);
        if (in_array($id, $voted)) {
            return back()->with('error', 'Vous avez déjà voté pour cette question.');
        }

        $question->increment('votes_count');
        
        $voted[] = $id;
        session(['voted_questions' => $voted]);

        return back()->with('success', 'Vote enregistré !');
    }

    /**
     * Répondre à une question en tant que participant.
     */
    public function storeReply(Request $request, $id)
    {
        $question = \App\Models\Question::findOrFail($id);
        
        if (!session('participant_pseudo')) {
            return redirect()->route('participant.join', ['code' => $question->event->code]);
        }

        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $question->replies()->create([
            'pseudo' => session('participant_pseudo'),
            'content' => $data['content'],
            'is_moderator' => false,
        ]);

        return back()->with('success', 'Votre réponse a été envoyée !');
    }

    /**
     * Lever la main.
     */
    public function raiseHand($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $pseudo = session('participant_pseudo');

        if (!$pseudo) return back();

        // Vérifier si déjà levée
        $exists = $event->raisedHands()->where('pseudo', $pseudo)->where('status', '!=', 'dismissed')->exists();
        
        if (!$exists) {
            $event->raisedHands()->create([
                'pseudo' => $pseudo,
                'status' => 'pending'
            ]);
        }

        return back()->with('success', 'Vous avez levé la main !');
    }

    /**
     * Baisser la main.
     */
    public function lowerHand($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $pseudo = session('participant_pseudo');

        if (!$pseudo) return back();

        $event->raisedHands()->where('pseudo', $pseudo)->delete();

        return back()->with('success', 'Vous avez baissé la main.');
    }

    /**
     * Mise à jour de la présence (Heartbeat).
     */
    public function heartbeat(Request $request, $code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $pseudo = session('participant_pseudo');

        if (!$pseudo) return response()->json(['status' => 'error']);

        \App\Models\EventParticipant::updateOrCreate(
            ['event_id' => $event->id, 'pseudo' => $pseudo],
            ['last_seen_at' => now()]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Mise à jour du statut "en train d'écrire".
     */
    public function updateTyping(Request $request, $code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $pseudo = session('participant_pseudo');

        if (!$pseudo) return response()->json(['status' => 'error']);

        \App\Models\EventParticipant::where('event_id', $event->id)
            ->where('pseudo', $pseudo)
            ->update(['is_typing' => $request->is_typing]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Récupérer les participants actifs.
     */
    public function getActiveParticipants($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        
        // Participants vus il y a moins de 30 secondes
        $activeParticipants = $event->participants()
            ->where('last_seen_at', '>=', now()->subSeconds(30))
            ->get(['pseudo', 'is_typing']);

        // Ajouter le statut "parle" (main levée et appelée)
        $calledPseudos = $event->raisedHands()
            ->where('status', 'called')
            ->pluck('pseudo')
            ->toArray();

        $data = $activeParticipants->map(function($p) use ($calledPseudos) {
            return [
                'pseudo' => $p->pseudo,
                'is_typing' => $p->is_typing,
                'is_speaking' => in_array($p->pseudo, $calledPseudos)
            ];
        });

        return response()->json($data);
    }
}
