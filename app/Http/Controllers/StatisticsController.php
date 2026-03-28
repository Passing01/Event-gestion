<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Question;
use App\Models\Reply;

class StatisticsController extends Controller
{
    /**
     * Afficher les statistiques globales.
     */
    public function index()
    {
        $user = Auth::user();
        $events = $user->events()->withCount('questions')->get();
        
        $totalEvents = $events->count();
        $totalQuestions = $events->sum('questions_count');
        
        // Estimation des participants uniques par pseudo
        $totalParticipants = Question::whereIn('event_id', $events->pluck('id'))->distinct('pseudo')->count('pseudo');
        
        $totalVotes = Question::whereIn('event_id', $events->pluck('id'))->sum('votes_count');
        $totalReplies = Reply::whereIn('question_id', Question::whereIn('event_id', $events->pluck('id'))->pluck('id'))->count();

        // Données pour un graphique simple (questions par événement)
        $chartData = $events->map(function($e) {
            return [
                'name' => $e->name,
                'count' => $e->questions_count
            ];
        })->take(7);

        return view('statistics.index', compact(
            'totalEvents', 
            'totalQuestions', 
            'totalParticipants', 
            'totalVotes', 
            'totalReplies',
            'chartData'
        ));
    }
}
