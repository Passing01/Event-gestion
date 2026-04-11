<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Page de connexion (GET).
     */
    public function signinForm()
    {
        return view('auth.signin');
    }

    /**
     * Traitement de la connexion (POST).
     */
    public function signin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        \Illuminate\Support\Facades\Log::info("Tentative de connexion - Email: " . $credentials['email']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            \Illuminate\Support\Facades\Log::info("Connexion réussie - User ID: " . $user->id . " - Role: " . $user->role);
            
            if ($user->role === 'panelist') {
                return redirect()->route('panelist.index');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('dashboard.index'));
        }

        \Illuminate\Support\Facades\Log::warning("Connexion échouée - Email: " . $credentials['email']);

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Identifiants incorrects. Veuillez réessayer.');
    }

    /**
     * Page d'inscription (GET).
     */
    public function signupForm()
    {
        return view('auth.signup');
    }

    /**
     * Traitement de l'inscription (POST).
     */
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'min:8', 'confirmed'],
        ]);

        // Créer l'utilisateur
        $user = \App\Models\User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // Password will be hashed by the model cast
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
