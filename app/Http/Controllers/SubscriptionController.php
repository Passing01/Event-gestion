<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Afficher les plans d'abonnement.
     */
    public function index()
    {
        $user = Auth::user();
        return view('subscription.index', compact('user'));
    }

    /**
     * Mettre à jour le plan (Simulation).
     */
    public function update(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:free,premium,enterprise',
        ]);

        Auth::user()->update([
            'plan' => $request->plan,
        ]);

        return back()->with('success', 'Votre abonnement a été mis à jour avec succès !');
    }
}
