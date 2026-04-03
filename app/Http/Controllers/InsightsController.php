<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Question;

class InsightsController extends Controller
{
    /**
     * Afficher la liste des événements pour analyse IA.
     */
    public function index()
    {
        $events = Auth::user()->events()->withCount('questions')->orderBy('date', 'desc')->get();
        return view('insights.index', compact('events'));
    }

    /**
     * Afficher l'analyse IA d'un événement spécifique (Seulement si clôturé).
     */
    public function show($id)
    {
        $event = Auth::user()->events()->with(['questions.replies', 'participants'])->findOrFail($id);
        
        if (!$event->closed_at) {
            return redirect()->route('dashboard.insights.index')->with('error', "L'analyse IA n'est disponible qu'après la clôture de l'événement.");
        }

        // On utilise les données IA stockées en base lors de la clôture
        $summary = $event->ai_summary ?? "Aucune synthèse générée lors de la clôture.";
        $topKeywords = is_array($event->ai_keywords) ? $event->ai_keywords : json_decode($event->ai_keywords, true);
        $sentimentLabel = $event->ai_sentiment ?? 'Neutre (50%)';
        $participantsCount = $event->participants()->count();
        
        return view('insights.show', compact('event', 'summary', 'topKeywords', 'sentimentLabel', 'participantsCount'));
    }

    /**
     * Exporter le rapport AI (Seulement si clôturé).
     */
    public function export($id)
    {
        $event = Auth::user()->events()->with(['questions.replies'])->findOrFail($id);

        if (!$event->closed_at) {
            return back()->with('error', "L'analyse IA n'est disponible qu'après la clôture de l'événement.");
        }

        $reportContent = $event->ai_report;

        if (!$reportContent) {
            return back()->with('error', "Aucun rapport n'a pu être trouvé pour cet événement.");
        }

        return view('insights.export', compact('event', 'reportContent'));
    }

    /**
     * Publier/Dépublier sur le Marketplace.
     */
    public function toggleMarketplace($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        $event->update([
            'is_on_marketplace' => !$event->is_on_marketplace
        ]);

        $status = $event->is_on_marketplace ? 'publié sur le Marketplace.' : 'retiré du Marketplace.';
        return back()->with('success', "L'événement a été " . $status);
    }
}
