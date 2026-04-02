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
    public function moderateQuestion($content, $eventDescription, $existingQuestions = [])
    {
        $prompt = "Tu es un modérateur IA pour un événement interactif.\n";
        $prompt .= "Thème de l'événement : " . $eventDescription . "\n\n";
        
        if (!empty($existingQuestions)) {
            $prompt .= "Voici les questions déjà posées et répondues :\n";
            foreach ($existingQuestions as $q) {
                $prompt .= "- Question : " . $q->content . " | Réponse : " . ($q->replies->first()->content ?? 'En attente') . "\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "Nouvelle question posée : \"" . $content . "\"\n\n";
        
        $prompt .= "Analyse cette question selon 3 critères :\n";
        $prompt .= "1. Est-elle hors-sujet par rapport au thème ?\n";
        $prompt .= "2. Est-elle un doublon d'une question déjà répondue ?\n";
        $prompt .= "3. Est-elle pertinente et respectueuse ?\n\n";
        
        $prompt .= "Réponds uniquement au format JSON strict avec ces clés :\n";
        $prompt .= "{ \"status\": \"ok|duplicate|off_topic\", \"message\": \"ton message d'explication court en français\", \"suggestion\": \"en cas de doublon, indique la réponse déjà donnée\" }";

        $response = $this->generateResponse($prompt);
        
        // Nettoyage de la réponse si Gemini ajoute du markdown json
        $cleanJson = trim(str_replace(['```json', '```'], '', $response));
        
        return json_decode($cleanJson, true) ?? ['status' => 'ok', 'message' => ''];
    }
}
