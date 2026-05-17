<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActivePlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user has not chosen a plan ('none')
        if ($user && strtolower($user->plan ?? '') === 'none') {
            // Allow all GET requests for browsing the platform
            if ($request->isMethod('get')) {
                return $next($request);
            }

            // Exceptions: allow choosing/updating the plan and logging out
            $allowedRoutes = [
                'dashboard.subscription.update',
                'logout',
            ];

            $currentRouteName = $request->route() ? $request->route()->getName() : null;

            if ($currentRouteName && in_array($currentRouteName, $allowedRoutes)) {
                return $next($request);
            }

            // Redirect back with an elegant error message
            return redirect()->route('dashboard.subscription.index')
                ->with('error', "Vous devez choisir un plan d'abonnement pour effectuer cette action. Votre accès actuel est limité à la consultation.");
        }

        return $next($request);
    }
}
