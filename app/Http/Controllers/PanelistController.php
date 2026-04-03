<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Panelist;
use App\Notifications\PanelistInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PanelistController extends Controller
{
    /**
     * List all events for the current panelist.
     */
    public function index()
    {
        $panelistEntries = Panelist::where('user_id', Auth::id())
            ->with('event')
            ->get();

        return view('panelist.index', compact('panelistEntries'));
    }

    /**
     * Store a new panelist.
     */
    public function store(Request $request, $eventId)
    {
        $event = Auth::user()->events()->findOrFail($eventId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'sector' => 'nullable|string|max:255',
        ]);

        // Check if user exists, or create one
        $user = User::where('email', $data['email'])->first();
        $password = Str::random(10);
        
        \Illuminate\Support\Facades\Log::info("Création panéliste - Email: " . $data['email'] . " - Password généré: " . $password);

        if (!$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password, // Le modèle s'occupe du hachage via le cast
                'role' => 'panelist',
                'onboarding_completed' => true,
                'email_verified_at' => now(), // Auto-vérification pour les panélistes
            ]);
            \Illuminate\Support\Facades\Log::info("Nouvel utilisateur créé pour le panéliste.");
        } else {
            $user->update([
                'role' => 'panelist', 
                'password' => $password, // On met à jour le mot de passe pour qu'il corresponde au mail
                'email_verified_at' => $user->email_verified_at ?? now()
            ]);
            \Illuminate\Support\Facades\Log::info("Utilisateur existant mis à jour en panéliste (mot de passe inclus).");
        }

        // Create Panelist link
        Panelist::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['sector' => $data['sector']]
        );

        // Send notification
        $user->notify(new PanelistInvitation($event, $password));

        return back()->with('success', 'Panelyste ajouté et invité avec succès.');
    }

    /**
     * Display the join form for panelists.
     */
    public function joinForm()
    {
        return view('panelist.join');
    }

    /**
     * Panelist login with event code.
     */
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:events,code',
        ]);

        $event = Event::where('code', $request->code)->firstOrFail();
        
        // Check if current user is a panelist for this event
        $isPanelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($event->scheduled_at && $event->scheduled_at->isFuture()) {
            return back()->with('error', "Cet événement n'a pas encore commencé. Accès autorisé à partir de : " . $event->scheduled_at->format('d/m/Y H:i'));
        }

        return redirect()->route('panelist.dashboard', $event->code);
    }

    /**
     * Panelist Dashboard.
     */
    public function dashboard($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $allQuestions = $event->questions()
            ->with(['replies'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Séparer les questions filtrées (rejetées par l'Assistant Modérateur)
        $filteredByAI = $allQuestions->filter(function($q) {
            return $q->status == 'rejected' && $q->replies->contains('pseudo', 'Assistant Modérateur');
        });

        // Questions normales pour le panéliste (y compris celles en attente)
        $questions = $allQuestions->reject(function($q) use ($filteredByAI) {
            return $filteredByAI->contains('id', $q->id);
        });

        return view('panelist.dashboard', compact('event', 'panelist', 'questions', 'filteredByAI'));
    }

    /**
     * Fetch questions HTML partials for polling (Panelist).
     */
    public function fetchQuestionsPartial($id)
    {
        $event = Event::findOrFail($id);
        
        $allQuestions = $event->questions()
            ->with(['replies'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filteredByAI = $allQuestions->filter(function($q) {
            return $q->status == 'rejected' && $q->replies->contains('pseudo', 'Assistant Modérateur');
        });

        $questions = $allQuestions->reject(function($q) use ($filteredByAI) {
            return $filteredByAI->contains('id', $q->id);
        });

        return response()->json([
            'main_html' => view('panelist.partials.questions_list', compact('questions'))->render(),
            'filtered_html' => view('panelist.partials.filtered_list', compact('filteredByAI'))->render(),
            'counts' => [
                'active' => $questions->count(),
                'filtered' => $filteredByAI->count(),
                'total' => $questions->count() + $filteredByAI->count()
            ]
        ]);
    }

    /**
     * Upload presentation.
     */
    public function upload(Request $request, $code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'presentation' => 'required|file|mimes:pdf,ppt,pptx,txt|max:10240',
        ]);

        if ($request->hasFile('presentation')) {
            $file = $request->file('presentation');
            $path = $file->store('presentations', 'public');
            
            $notes = null;
            if ($file->getClientOriginalExtension() === 'txt') {
                $notes = file_get_contents($file->getRealPath());
            }

            $panelist->update([
                'presentation_path' => $path,
                'notes' => $notes ?? "Document uploaded: " . $file->getClientOriginalName()
            ]);
        }

        return back()->with('success', 'Présentation téléchargée avec succès.');
    }

    /**
     * AI Suggestion for a question.
     */
    public function aiSuggest(Request $request, $code, \App\Services\GeminiService $gemini)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $question = \App\Models\Question::findOrFail($request->question_id);

        \Illuminate\Support\Facades\Log::info("AI Suggestion requested for question ID: " . $request->question_id);
        $suggestion = $gemini->suggestAnswer($question->content, $event->description, $panelist->notes);
        \Illuminate\Support\Facades\Log::info("AI Suggestion generated successfully.");

        return response()->json(['suggestion' => $suggestion]);
    }

    /**
     * Update panelist details.
     */
    public function update(Request $request, $panelistId)
    {
        $panelist = Panelist::findOrFail($panelistId);
        $event = $panelist->event;

        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
        ]);

        $panelist->user->update(['name' => $data['name']]);
        $panelist->update(['sector' => $data['sector']]);

        return back()->with('success', 'Panéliste mis à jour.');
    }

    /**
     * Delete document.
     */
    public function deleteDocument($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($panelist->presentation_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($panelist->presentation_path);
        }

        $panelist->update([
            'presentation_path' => null,
            'notes' => null,
            'is_projecting' => false
        ]);

        return back()->with('success', 'Document supprimé.');
    }

    /**
     * Toggle document share with moderator.
     */
    public function toggleShare($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $panelist->update(['is_document_shared' => !$panelist->is_document_shared]);

        return back()->with('success', $panelist->is_document_shared ? 'Document partagé avec le modérateur.' : 'Document privé.');
    }

    /**
     * Toggle document projection.
     */
    public function toggleProject($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $newState = !$panelist->is_projecting;

        // Si on active, on désactive les autres d'abord
        if ($newState) {
            Panelist::where('event_id', $event->id)->update(['is_projecting' => false]);
            $panelist->current_page = 1; 
        }

        $panelist->is_projecting = $newState;
        $panelist->save();

        return back()->with('success', $panelist->is_projecting ? 'Projection lancée !' : 'Projection arrêtée.');
    }

    /**
     * Sync current page for projection.
     */
    public function syncPage(Request $request, $code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $panelist = Panelist::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $page = $request->input('page', 1);
        $panelist->update(['current_page' => max(1, $page)]);

        return response()->json(['status' => 'ok', 'current_page' => $panelist->current_page]);
    }

    /**
     * Remove a panelist.
     */
    public function destroy($panelistId)
    {
        $panelist = Panelist::findOrFail($panelistId);
        $event = $panelist->event;

        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        // We keep the user but remove the panelist link for this event
        $panelist->delete();

        return back()->with('success', 'Panéliste retiré de l\'événement.');
    }
}
