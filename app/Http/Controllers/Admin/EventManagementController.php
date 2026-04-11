<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Panelist;
use Illuminate\Http\Request;

class EventManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('user');
        
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $events = $query->latest()->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with(['user', 'panelists.user'])->findOrFail($id);
        return view('admin.events.show', compact('event'));
    }

    public function togglePanelistStatus($eventId, $panelistId)
    {
        $panelist = Panelist::where('event_id', $eventId)->where('id', $panelistId)->firstOrFail();
        $panelist->is_active = !$panelist->is_active;
        $panelist->save();

        return back()->with('success', 'Statut du panéliste mis à jour.');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Événement supprimé.');
    }
}
