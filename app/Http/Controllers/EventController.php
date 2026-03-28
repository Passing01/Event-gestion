<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Liste des événements.
     */
    public function index()
    {
        $events = Auth::user()->events()->orderBy('date', 'desc')->get();
        return view('events.index', compact('events'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Enregistrer un nouvel événement.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'moderation_enabled' => 'boolean',
            'anonymous_allowed' => 'boolean',
        ]);

        $event = Auth::user()->events()->create([
            'name' => $data['name'],
            'date' => $data['date'],
            'code' => strtoupper(Str::random(6)),
            'moderation_enabled' => $request->has('moderation_enabled'),
            'anonymous_allowed' => $request->has('anonymous_allowed'),
            'status' => 'active',
        ]);

        return redirect()->route('dashboard.events.index')->with('success', 'Événement créé avec succès !');
    }

    /**
     * Détails d'un événement.
     */
    public function show($id)
    {
        $event = Auth::user()->events()->withCount('questions')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        return view('events.edit', compact('event'));
    }

    /**
     * Mettre à jour un événement.
     */
    public function update(Request $request, $id)
    {
        $event = Auth::user()->events()->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|string|in:active,archived',
        ]);

        $event->update([
            'name' => $data['name'],
            'date' => $data['date'],
            'status' => $data['status'],
            'moderation_enabled' => $request->has('moderation_enabled'),
            'anonymous_allowed' => $request->has('anonymous_allowed'),
        ]);

        return redirect()->route('dashboard.events.index')->with('success', 'Événement mis à jour.');
    }

    /**
     * Supprimer un événement.
     */
    public function destroy($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        $event->delete();

        return redirect()->route('dashboard.events.index')->with('success', 'Événement supprimé.');
    }

    /**
     * Activer ou désactiver un événement.
     */
    public function toggleStatus($id)
    {
        $event = Auth::user()->events()->findOrFail($id);
        $event->status = ($event->status === 'active') ? 'archived' : 'active';
        $event->save();

        $msg = ($event->status === 'active') ? 'Événement activé.' : 'Événement désactivé.';
        return back()->with('success', $msg);
    }
}
