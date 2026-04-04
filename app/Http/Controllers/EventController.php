<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Liste des événements.
     */
    public function index()
    {
        $events = Auth::user()->events()->orderBy('date', 'desc')->get();
        return view('events.index', compact('events'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Enregistrer un nouvel événement.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'scheduled_at' => 'nullable|date_format:Y-m-d\TH:i',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'collect_presence' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event = Auth::user()->events()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'date' => $data['date'],
            'end_date' => $data['end_date'] ?? null,
            'scheduled_at' => $data['scheduled_at'],
            'code' => Str::upper(Str::random(6)),
            'status' => 'active',
            'image_path' => $imagePath,
            'collect_presence' => $request->has('collect_presence'),
        ]);

        return redirect()->route('dashboard.events.index')->with('success', 'Événement créé avec succès.');
    }

    /**
     * Voir les détails d'un événement.
     */
    public function show($id)
    {
        $event = Auth::user()->events()->with(['questions', 'panelists.user', 'participants'])->withCount('questions')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    /**
     * Modifier un événement.
     */
    public function edit($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        return view('events.edit', compact('event'));
    }

    /**
     * Mettre à jour un événement.
     */
    public function update(Request $request, $id)
    {
        $event = Auth::user()->events()->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'scheduled_at' => 'nullable|date_format:Y-m-d\TH:i',
            'status' => 'required|in:active,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $imagePath = $request->file('image')->store('events', 'public');
            $event->image_path = $imagePath;
        }

        $event->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'date' => $data['date'],
            'end_date' => $data['end_date'] ?? null,
            'scheduled_at' => $data['scheduled_at'],
            'status' => $data['status'],
            'moderation_enabled' => $request->has('moderation_enabled'),
            'anonymous_allowed' => $request->has('anonymous_allowed'),
            'is_on_marketplace' => $request->has('is_on_marketplace'),
            'collect_presence' => $request->has('collect_presence'),
        ]);

        return redirect()->route('dashboard.events.index')->with('success', 'Événement mis à jour.');
    }

    /**
     * Clôturer l'événement et générer les rapports finaux.
     */
    public function close($id, \App\Services\GeminiService $gemini)
    {
        $event = Auth::user()->events()->with(['questions.replies'])->findOrFail($id);

        if ($event->closed_at) {
            return back()->with('info', "Cet événement est déjà clôturé.");
        }

        $questions = $event->questions;

        // 1. Générer l'analyse finale (Summary, Keywords, Sentiment)
        $analysis = $gemini->analyzeEvent($event, $questions);
        
        // 2. Générer le rapport final
        $reportContent = $gemini->generateEventReport($event, $questions);

        // 3. Sauvegarder tout
        $event->update([
            'closed_at' => now(),
            'status' => 'archived',
            'ai_summary' => $analysis['summary'] ?? "Synthèse indisponible.",
            'ai_keywords' => $analysis['topKeywords'] ?? [],
            'ai_sentiment' => $analysis['sentimentLabel'] ?? 'Neutre (50%)',
            'ai_report' => $reportContent ?? "Rapport indisponible."
        ]);

        return redirect()->route('dashboard.insights.show', $event->id)->with('success', 'Événement clôturé avec succès. Les rapports finaux ont été générés.');
    }

    /**
     * Supprimer un événement.
     */
    public function destroy($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        $event->delete();

        return redirect()->route('dashboard.events.index')->with('success', 'Événement supprimé.');
    }

    /**
     * Activer ou désactiver un événement.
     */
    public function toggleStatus($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        $event->status = ($event->status === 'active') ? 'archived' : 'active';
        $event->save();

        $msg = ($event->status === 'active') ? 'Événement activé.' : 'Événement désactivé.';
        return back()->with('success', $msg);
    }
    /**
     * Exporter la liste de présence en PDF.
     */
    public function exportPresence($id)
    {
        $event = Auth::user()->events()
            ->with(['participants', 'user', 'panelists.user'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.presence_pdf', compact('event'));

        $filename = "presence_" . Str::slug($event->name) . "_" . date('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }
}
