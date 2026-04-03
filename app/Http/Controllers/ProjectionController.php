<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class ProjectionController extends Controller
{
    /**
     * Interface de projection pour un événement.
     */
    public function index($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $answering = $event->questions()->where('status', 'answering')->first();
        $projectingPanelist = $event->panelists()->where('is_projecting', true)->with('user')->first();
        
        return view('projection.index', compact('event', 'answering', 'projectingPanelist'));
    }

    /**
     * API pour récupérer la question en cours (polling).
     */
    public function getAnswering($code)
    {
        $event = Event::where('code', $code)->firstOrFail();
        $answering = $event->questions()->where('status', 'answering')->first();
        
        $raisedHands = $event->raisedHands()
            ->where('status', '!=', 'dismissed')
            ->orderBy('created_at', 'asc')
            ->get(['pseudo', 'status']);

        $allQuestions = $event->questions()
            ->whereIn('status', ['approved', 'answering', 'answered'])
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        $projectingPanelist = $event->panelists()->where('is_projecting', true)->with('user')->first();

        return response()->json([
            'id' => $answering ? $answering->id : null,
            'pseudo' => $answering ? $answering->pseudo : null,
            'content' => $answering ? $answering->content : null,
            'audio_path' => $answering ? $answering->audio_path : null,
            'status' => $answering ? $answering->status : null,
            'raised_hands' => $raisedHands,
            'all_questions' => $allQuestions,
            'projecting_panelist' => $projectingPanelist ? [
                'name' => $projectingPanelist->user->name,
                'path' => $projectingPanelist->presentation_path,
                'url' => asset('storage/' . $projectingPanelist->presentation_path),
                'extension' => pathinfo($projectingPanelist->presentation_path, PATHINFO_EXTENSION),
                'current_page' => $projectingPanelist->current_page
            ] : null
        ]);
    }
}
