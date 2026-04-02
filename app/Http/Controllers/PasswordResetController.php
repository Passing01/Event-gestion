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

        // Envoyer le mail via Laravel (SMTP Brevo)
        try {
            \Illuminate\Support\Facades\Mail::send('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'userName' => $user->name,
                'appName'  => config('app.name', 'Event Q&A'),
            ], function($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('[' . config('app.name', 'Event Q&A') . '] Réinitialisation de votre mot de passe');
            });

            return redirect()->route('password.request')
                ->with('status', 'Un lien de réinitialisation vous a été envoyé par e-mail.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur envoi mail reset: ' . $e->getMessage());
            return back()->with('error', 'Désolé, une erreur est survenue lors de l\'envoi du mail. Veuillez réessayer.');
        }
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
