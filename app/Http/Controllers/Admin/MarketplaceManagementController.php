<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class MarketplaceManagementController extends Controller
{
    public function index()
    {
        $events = Event::where('is_on_marketplace', true)->with('user')->latest()->paginate(15);
        return view('admin.marketplace.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('admin.marketplace.show', compact('event'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['is_on_marketplace' => false]);
        return redirect()->route('admin.marketplace.index')->with('success', 'Événement retiré du marketplace.');
    }
}
