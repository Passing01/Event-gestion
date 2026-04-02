<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Affiche le formulaire de demande (saisie de l'email).
     */
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Traite la demande : génère le token et affiche la vue d'envoi EmailJS.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // On ne révèle pas si l'email existe ou non (sécurité)
        if (!$user) {
            return redirect()->route('password.request')
                ->with('status', 'Si cet email existe, un lien de réinitialisation vous a été envoyé.');
        }

        // Supprimer les anciens tokens pour cet email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Générer un token sécurisé
        $token = Str::random(64);

        // Stocker le token hashé
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Construire l'URL de réinitialisation
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);

        // Passer à la vue qui enverra via EmailJS
        return view('auth.forgot-password-send', [
            'resetUrl'  => $resetUrl,
            'userEmail' => $request->email,
            'userName'  => $user->name,
        ]);
    }

    /**
     * Affiche le formulaire de réinitialisation du mot de passe.
     */
    public function resetForm(Request $request, string $token)
    {
        $email = $request->query('email');

        // Vérifier que le token existe et n'est pas expiré (1 heure)
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation est invalide ou a expiré.');
        }

        // Vérifier expiration (60 minutes)
        if (Carbon::parse($record->created_at)->addHour()->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau.');
        }

        // Vérifier le token
        if (!Hash::check($token, $record->token)) {
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Réinitialise le mot de passe.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:8', 'confirmed'],
        ]);

        // Vérifier le token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Ce lien de réinitialisation est invalide ou a expiré.');
        }

        // Vérifier expiration
        if (Carbon::parse($record->created_at)->addHour()->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation a expiré.');
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Aucun compte associé à cet email.');
        }

        $user->password = $request->password; // Le cast 'hashed' s'en charge
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
