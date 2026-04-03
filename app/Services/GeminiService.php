<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

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
}
