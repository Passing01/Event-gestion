@extends('layouts.auth')

@section('title', 'Modification obligatoire du mot de passe')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">SH</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        <h1>Sécurité de votre compte</h1>
        <p>Pour la sécurité de vos données, veuillez définir un mot de passe personnel avant de continuer.</p>

        @if(session('warning'))
            <div style="background:#fffbeb;border:1px solid #fde68a;color:#b45309;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.force-change-password.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="Min. 8 caractères"
                       required autocomplete="new-password">
                @error('password')
                    <span style="font-size:0.75rem;color:var(--destructive);display:block;margin-top:0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                       placeholder="••••••••"
                       required autocomplete="new-password">
                @error('password_confirmation')
                    <span style="font-size:0.75rem;color:var(--destructive);display:block;margin-top:0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-brand">Mettre à jour mon mot de passe</button>
        </form>

        <hr style="border:0; border-top:1px solid var(--border); margin:1.5rem 0;">

        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="auth-link" style="background:none; border:none; color:var(--brand); cursor:pointer; font-size:0.875rem; text-decoration:underline; width:100%; text-align:center;">
                Se déconnecter et revenir à l'accueil
            </button>
        </form>
    </div>
</main>

@endsection
