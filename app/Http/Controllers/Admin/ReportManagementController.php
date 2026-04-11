<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Question;
use Illuminate\Http\Request;

class ReportManagementController extends Controller
{
    public function index()
    {
        $data = [
            'users_by_plan' => User::select('plan', \DB::raw('count(*) as total'))->groupBy('plan')->get(),
            'events_count' => Event::count(),
            'total_questions' => Question::count(),
            'avg_questions_per_event' => Event::count() > 0 ? Question::count() / Event::count() : 0,
        ];
        return view('admin.reports.index', compact('data'));
    }

    public function generate()
    {
        // Simulation de génération de rapport PDF/Excel
        return back()->with('success', 'Rapport généré avec succès (Simulation).');
    }
}
