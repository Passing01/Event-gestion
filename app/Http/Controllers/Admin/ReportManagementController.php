<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportManagementController extends Controller
{
    public function index()
    {
        $total_events = Event::count();
        $total_questions = Question::count();
        
        $data = [
            'users_by_plan' => User::select('plan', DB::raw('count(*) as total'))
                ->where('role', '!=', 'admin')
                ->groupBy('plan')
                ->get()
                ->pluck('total', 'plan')
                ->all(),
            'events_count' => $total_events,
            'total_questions' => $total_questions,
            'avg_questions' => $total_events > 0 ? round($total_questions / $total_events, 2) : 0,
            'engagement_rate' => $total_events > 0 ? 85 : 0, // Mocked for now
            'premium_events_month' => Event::where('plan', '!=', 'Free')->count(), // Simplified
            'plans_breakdown' => User::select('plan', DB::raw('count(*) as total'))
                ->where('role', '!=', 'admin')
                ->groupBy('plan')
                ->get()
                ->pluck('total', 'plan')
                ->toArray()
        ];
        
        return view('admin.reports.index', compact('data'));
    }

    public function generate()
    {
        return back()->with('success', 'Rapport généré avec succès (Simulation).');
    }
}
