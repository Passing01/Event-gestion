<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Page principale du tableau de bord.
     */
    public function index()
    {
        $events = Auth::user()->events()->orderBy('date', 'desc')->get();
        return view('dashboard.index', compact('events'));
    }

    /**
     * Créer un nouvel événement.
     */
    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after:today',
        ]);

        $event = Auth::user()->events()->create([
            'name' => $data['name'],
            'date' => $data['date'],
            'code' => strtoupper(substr(md5(uniqid()), 0, 6)),
            'moderation_enabled' => Auth::user()->default_moderation,
        ]);

        return back()->with('success', 'Événement créé avec succès ! Code : ' . $event->code);
    }

    /**
     * Page Messages.
     */
    public function messages()
    {
        return view('dashboard.messages');
    }

    /**
     * Page Statistiques.
     */
    public function statistics()
    {
        return view('dashboard.statistics');
    }

    /**
     * Page Sécurité.
     */
    public function security()
    {
        return view('dashboard.security');
    }

    /**
     * Page Appareils.
     */
    public function devices()
    {
        return view('dashboard.devices');
    }

    /**
     * Page Profil.
     */
    public function profile()
    {
        return view('dashboard.profile');
    }
}
