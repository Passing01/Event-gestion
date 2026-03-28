<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'industry' => 'required|string',
            'brand_color' => 'required|string',
            'projection_layout' => 'required|string',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Mettre à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }
}
