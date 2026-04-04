<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateResponse($prompt)
    {
        if (!$this->apiKey) {
            Log::error('Gemini API key is missing.');
            return null;
        }

        try {
            $response = Http::timeout(60)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::error('Gemini API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function suggestAnswer($question, $eventDescription, $panelistNotes = null)
    {
        $prompt = "Tu es un assistant IA pour un panéliste lors d'un événement.\n";
        $prompt .= "Thème de l'événement : " . $eventDescription . "\n";
        if ($panelistNotes) {
            $prompt .= "Notes du panéliste : " . $panelistNotes . "\n";
        }
        $prompt .= "Question du public : " . $question . "\n";
        $prompt .= "Propose une réponse concise et pertinente pour le panéliste. Réponds uniquement en texte brut, sans aucun formatage Markdown (pas de #, pas de **, etc.). Si la question est hors-sujet par rapport au thème, indique-le poliment et réponds brièvement si possible.";

        return $this->generateResponse($prompt);
    }

    /**
     * Analyse une question pour détecter les doublons ou le hors-sujet.
     */
    public function moderateQuestion($content, $event)
    {
        $existingQuestions = $event->questions()
            ->whereIn('status', ['pending', 'approved', 'answering', 'answered'])
            ->pluck('content')
            ->implode("\n- ");

        $prompt = "Vous êtes un Assistant Modérateur EXPERT pour l'événement '{$event->name}'.
        Thème : {$event->description}.
        
        CRITÈRES DE FILTRAGE STRICTS :
        1. DOUBLON (duplicate) : La question a déjà été posée ou une question très similaire existe déjà. Ne soyez pas seulement sur l'exactitude des mots, mais sur le SENS. Si l'idée est la même, c'est un DOUBLON.
        2. HORS-SUJET (off_topic) : La question n'a aucun lien direct avec le thème.
        
        LISTE DES QUESTIONS DÉJÀ PRÉSENTES (À COMPARER) :
        - {$existingQuestions}
        
        NOUVELLE QUESTION À ANALYSER : \"{$content}\"
        
        ACTIONS :
        - Si c'est un DOUBLON : status = 'duplicate'. Message = 'Cette question a déjà été posée.'. Suggestion = [Donnez un résumé de la réponse si elle existe déjà dans la liste, sinon laissez vide].
        - Si c'est HORS-SUJET : status = 'off_topic'. Message = 'Désolé, cette question n'est pas dans le thème de l'événement.'.
        - Sinon : status = 'ok'.
        
        RÉPONDEZ UNIQUEMENT EN JSON STRICT :
        { \"status\": \"ok|duplicate|off_topic\", \"message\": \"...\", \"suggestion\": \"...\" }";

        $response = $this->generateResponse($prompt);
        Log::info("Gemini Moderation Raw Response: " . $response);

        if (!$response) {
            return ['status' => 'ok', 'message' => ''];
        }

        // Extraction robuste du JSON avec une regex
        preg_match('/\{.*\}/s', $response, $matches);
        $cleanJson = $matches[0] ?? $response;
        
        $result = json_decode($cleanJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Gemini JSON Parse Error: " . json_last_error_msg());
            return ['status' => 'ok', 'message' => ''];
        }

        return $result;
    }

    /**
     * Analyse complète d'un événement pour le tableau de bord d'insights.
     */
    public function analyzeEvent($event, $questions)
    {
        $questionsText = $questions->map(function($q) {
            return "- Q: {$q->content}\n  R: " . ($q->replies->first()->content ?? "Pas de réponse");
        })->implode("\n");

        $prompt = "Analyse l'événement '{$event->name}' (Thème : {$event->description}) basé sur les questions suivantes :\n\n{$questionsText}\n\n" .
                 "Génère une analyse concise en JSON avec les champs suivants :\n" .
                 "- summary: Une synthèse de 3-4 phrases de l'événement et des échanges.\n" .
                 "- topKeywords: Un tableau des 5 mots-clés les plus importants.\n" .
                 "- sentimentLabel: 'Positif', 'Neutre' ou 'Critique' suivi d'un pourcentage (ex: 'Positif (85%)').\n" .
                 "RÉPONDEZ UNIQUEMENT EN JSON STRICT.";

        $response = $this->generateResponse($prompt);
        
        if (!$response) return null;

        preg_match('/\{.*\}/s', $response, $matches);
        $cleanJson = $matches[0] ?? $response;
        
        return json_decode($cleanJson, true);
    }

    /**
     * Génère un rapport d'événement complet et professionnel.
     */
    public function generateEventReport($event, $questions)
    {
        $questionsText = $questions->map(function($q) {
            return "- Question : {$q->content}\n  Réponses fournies : " . $q->replies->pluck('content')->implode(' ; ');
        })->implode("\n\n");

        $prompt = "Rédige un rapport de synthèse professionnel et humain pour l'événement suivant :\n" .
                 "Nom de l'événement : {$event->name}\n" .
                 "Description/Thème : {$event->description}\n" .
                 "Date : " . $event->date->format('d/m/Y') . "\n\n" .
                 "Contenu des échanges (Questions et Réponses) :\n" .
                 "{$questionsText}\n\n" .
                 "Le rapport doit inclure :\n" .
                 "1. Une introduction (contexte).\n" .
                 "2. Une analyse des thématiques principales abordées.\n" .
                 "3. Une synthèse qualitative de la participation de l'audience.\n" .
                 "4. Des recommandations ou points à retenir pour de futurs événements.\n\n" .
                 "Rédige cela comme un consultant expert, avec un ton professionnel, structuré et engageant. Utilise du Markdown pour la mise en forme.";

        return $this->generateResponse($prompt);
    }
}
