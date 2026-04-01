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

        if (!$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => 'panelist',
                'onboarding_completed' => true,
                'email_verified_at' => now(), // Auto-vérification pour les panélistes
            ]);
        } else {
            $user->update(['role' => 'panelist', 'email_verified_at' => $user->email_verified_at ?? now()]);
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

        if (!$isPanelist) {
            return back()->with('error', "Vous n'êtes pas autorisé à accéder à cet événement en tant que panelyste.");
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

        $questions = $event->questions()
            ->where('status', 'approved')
            ->with(['replies', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('panelist.dashboard', compact('event', 'panelist', 'questions'));
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

        $suggestion = $gemini->suggestAnswer($question->content, $event->description, $panelist->notes);

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
