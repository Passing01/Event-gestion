@extends('layouts.auth')

@section('title', 'Inscription – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">SH</div>
            <span style="font-weight:600;font-size:0.875rem;">Smart Home</span>
        </div>

        <h1>Créer un compte</h1>
        <p>Rejoignez et contrôlez votre maison connectée.</p>

        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.signup.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="{{ old('name') }}"
                       placeholder="Jean Dupont"
                       required autocomplete="name">
                @error('name')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="{{ old('email') }}"
                       placeholder="vous@exemple.com"
                       required autocomplete="email">
                @error('email')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="••••••••"
                       required autocomplete="new-password">
                @error('password')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                       placeholder="••••••••"
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-brand">S'inscrire</button>
        </form>

        <span class="auth-link">
            Déjà un compte ?
            <a href="{{ route('login') }}">Se connecter</a>
        </span>

        <a href="{{ route('dashboard.index') }}" class="auth-link" style="margin-top:0.5rem;">
            ← Retour au tableau de bord
        </a>
    </div>
</main>

@endsection
