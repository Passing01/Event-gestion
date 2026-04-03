<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\ProjectionLog;

class MarketplaceController extends Controller
{
    /**
     * Liste des événements disponibles sur le Marketplace.
     */
    public function index()
    {
        $events = Event::where('is_on_marketplace', true)
            ->withCount('questions')
            ->orderBy('date', 'desc')
            ->get();

        return view('marketplace.index', compact('events'));
    }

    /**
     * Détails d'un événement sur le Marketplace (Avant Replay).
     */
    public function show($id)
    {
        $event = Event::with(['questions.replies', 'panelists.user'])->findOrFail($id);

        // On utilise la synthèse déjà stockée en base (générée à la clôture)
        // Cela évite d'interroger l'IA inutilement à chaque visite.
        $summary = $event->ai_summary ?? $event->description ?? "Cet événement est maintenant disponible en replay interactif.";

        return view('marketplace.show', compact('event', 'summary'));
    }

    /**
     * Voir le Replay Interactif.
     */
    public function replay($id)
    {
        $event = Event::with(['questions.replies', 'panelists.user', 'projectionLogs'])->findOrFail($id);
        
        // On récupère tout le flux pour la timeline
        $questions = $event->questions()
            ->with(['replies'])
            ->whereIn('status', ['approved', 'answering', 'answered'])
            ->orderBy('created_at', 'asc')
            ->get();

        $logs = $event->projectionLogs()->orderBy('created_at', 'asc')->get();

        return view('marketplace.replay', compact('event', 'questions', 'logs'));
    }
}
