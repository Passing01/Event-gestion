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
     * Afficher l'analyse IA d'un événement spécifique.
     */
    public function show($id, \App\Services\GeminiService $gemini)
    {
        $event = Auth::user()->events()->with(['questions.replies', 'participants'])->findOrFail($id);
        $questions = $event->questions;
        $questionsCount = $questions->count();
        $participantsCount = $event->participants()->count();

        if ($questionsCount === 0) {
            return view('insights.show', [
                'event' => $event,
                'summary' => "Aucune question n'a été posée pour cet événement. L'analyse IA nécessite des données pour générer une synthèse.",
                'topKeywords' => [],
                'sentimentLabel' => 'N/A',
                'participantsCount' => $participantsCount
            ]);
        }

        // --- Appel à Gemini pour l'analyse ---
        $analysis = $gemini->analyzeEvent($event, $questions);

        if (!$analysis) {
            // Fallback en cas d'erreur de l'IA
            $summary = "L'événement '{$event->name}' a généré {$questionsCount} questions. L'IA n'a pas pu générer une synthèse détaillée pour le moment.";
            $topKeywords = ['Événement', 'Questions', 'Réponses'];
            $sentimentLabel = 'Neutre (50%)';
        } else {
            $summary = $analysis['summary'] ?? "Synthèse indisponible.";
            $topKeywords = $analysis['topKeywords'] ?? [];
            $sentimentLabel = $analysis['sentimentLabel'] ?? 'Neutre (50%)';
        }

        return view('insights.show', compact('event', 'summary', 'topKeywords', 'sentimentLabel', 'participantsCount'));
    }

    /**
     * Exporter le rapport AI.
     */
    public function export($id, \App\Services\GeminiService $gemini)
    {
        $event = Auth::user()->events()->with(['questions.replies'])->findOrFail($id);
        $questions = $event->questions;

        if ($questions->count() === 0) {
            return back()->with('error', "Pas assez de données pour générer un rapport.");
        }

        $reportContent = $gemini->generateEventReport($event, $questions);
        
        if (!$reportContent) {
            return back()->with('error', "L'IA n'a pas pu générer le rapport. Veuillez réessayer.");
        }

        return view('insights.export', compact('event', 'reportContent'));
    }
}
