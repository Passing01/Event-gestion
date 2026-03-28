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
    public function show($id)
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

        // --- Extraction de mots-clés (Logique simple) ---
        $allText = $questions->pluck('content')->implode(' ');
        $words = str_word_count(strtolower($allText), 1);
        $stopWords = ['le', 'la', 'les', 'de', 'des', 'un', 'une', 'et', 'est', 'pour', 'que', 'qui', 'dans', 'sur', 'avec', 'pas', 'plus', 'comment', 'pourquoi', 'quel', 'quelle'];
        $filteredWords = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopWords));
        $wordCounts = array_count_values($filteredWords);
        arsort($wordCounts);
        $topKeywords = array_slice(array_keys($wordCounts), 0, 5);
        $topKeywords = array_map('ucfirst', $topKeywords);

        // --- Simulation de Sentiment ---
        $positiveWords = ['bien', 'bon', 'super', 'merci', 'bravo', 'top', 'génial', 'intéressant', 'utile'];
        $negativeWords = ['problème', 'mauvais', 'difficile', 'nul', 'lent', 'erreur', 'bug', 'déçu'];
        
        $posCount = 0;
        $negCount = 0;
        foreach ($words as $w) {
            if (in_array($w, $positiveWords)) $posCount++;
            if (in_array($w, $negativeWords)) $negCount++;
        }

        $sentimentScore = 50 + ($posCount * 10) - ($negCount * 10);
        $sentimentScore = max(0, min(100, $sentimentScore));
        $sentimentLabel = $sentimentScore > 60 ? "Positif ({$sentimentScore}%)" : ($sentimentScore < 40 ? "Critique ({$sentimentScore}%)" : "Neutre ({$sentimentScore}%)");

        // --- Génération de Synthèse ---
        $mainTopic = count($topKeywords) > 0 ? $topKeywords[0] : "divers sujets";
        $summary = "L'événement '{$event->name}' a réuni {$participantsCount} participants et généré {$questionsCount} questions. ";
        $summary .= "L'analyse sémantique montre que l'audience s'est principalement intéressée à : " . implode(', ', $topKeywords) . ". ";
        $summary .= "Le sujet dominant semble être '{$mainTopic}'. Globalement, les échanges ont été jugés comme étant d'un ton " . strtolower(explode(' ', $sentimentLabel)[0]) . ".";

        return view('insights.show', compact('event', 'summary', 'topKeywords', 'sentimentLabel', 'participantsCount'));
    }

    /**
     * Exporter le rapport (Simulation).
     */
    public function export($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        
        // Ici on pourrait générer un PDF avec dompdf
        // Pour l'instant, on redirige vers une vue d'impression
        return view('insights.export', compact('event'));
    }
}
