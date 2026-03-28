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
        
        return view('projection.index', compact('event', 'answering'));
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

        return response()->json([
            'id' => $answering ? $answering->id : null,
            'pseudo' => $answering ? $answering->pseudo : null,
            'content' => $answering ? $answering->content : null,
            'status' => $answering ? $answering->status : null,
            'raised_hands' => $raisedHands
        ]);
    }
}
