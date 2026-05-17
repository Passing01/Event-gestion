<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            if (!$request->routeIs('auth.force-change-password') && 
                !$request->routeIs('auth.force-change-password.post') && 
                !$request->routeIs('auth.logout')) {
                return redirect()->route('auth.force-change-password')
                    ->with('warning', 'Vous devez modifier votre mot de passe lors de votre première connexion pour continuer.');
            }
        }

        return $next($request);
    }
}
