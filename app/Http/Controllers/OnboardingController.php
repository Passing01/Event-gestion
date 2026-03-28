<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Afficher l'étape actuelle de l'onboarding.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Si l'onboarding est déjà terminé, rediriger vers le dashboard
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard.index');
        }

        $step = $user->onboarding_step ?? 1;

        return view("onboarding.step-$step", compact('user', 'step'));
    }

    /**
     * Sauvegarder une étape et passer à la suivante.
     */
    public function saveStep(Request $request, $step)
    {
        $user = Auth::user();
        
        if ($step == 1) {
            $data = $request->validate([
                'organization_name' => 'required|string|max:255',
                'industry' => 'required|string',
            ]);
            
            $user->update([
                'organization_name' => $data['organization_name'],
                'industry' => $data['industry'],
                'onboarding_step' => 2
            ]);
        } elseif ($step == 2) {
            $data = $request->validate([
                'brand_color' => 'required|string',
                'projection_layout' => 'required|string',
            ]);
            
            $user->update([
                'brand_color' => $data['brand_color'],
                'projection_layout' => $data['projection_layout'],
                'onboarding_step' => 3
            ]);
        } elseif ($step == 3) {
            $data = $request->validate([
                'default_moderation' => 'required|boolean',
                'plan' => 'required|string',
            ]);
            
            $user->update([
                'default_moderation' => $data['default_moderation'],
                'plan' => $data['plan'],
                'onboarding_step' => 3,
                'onboarding_completed' => true
            ]);
            
            return redirect()->route('dashboard.index');
        }

        return redirect()->route('onboarding.index');
    }

    /**
     * Marquer l'onboarding comme terminé.
     */
    public function complete()
    {
        Auth::user()->update(['onboarding_completed' => true]);
        return redirect()->route('dashboard.index');
    }
}
