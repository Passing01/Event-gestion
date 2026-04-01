<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

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
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
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
        $prompt .= "Propose une réponse concise et pertinente pour le panéliste. Si la question est hors-sujet par rapport au thème, indique-le poliment et réponds brièvement si possible.";

        return $this->generateResponse($prompt);
    }
}
