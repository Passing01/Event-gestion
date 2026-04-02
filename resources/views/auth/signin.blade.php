@extends('layouts.auth')

@section('title', 'Connexion – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">SH</div>
            <span style="font-weight:600;font-size:0.875rem;">Smart Home</span>
        </div>

        <h1>Connexion</h1>
        <p>Bienvenue. Entrez vos identifiants.</p>

                @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('status'))
            <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

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
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label class="form-label" for="password">Mot de passe</label>
                    <a href="{{ route('password.request') }}" style="font-size:0.75rem; color:var(--brand); text-decoration:none; margin-bottom:0.25rem;">Mot de passe oublié ?</a>
                </div>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                @error('password')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-brand">Se connecter</button>
        </form>

        <span class="auth-link">
            Pas encore de compte ?
            <a href="{{ route('auth.signup') }}">S'inscrire</a>
        </span>

        <a href="{{ route('dashboard.index') }}" class="auth-link" style="margin-top:0.5rem;">
            ← Retour au tableau de bord
        </a>
    </div>
</main>

@endsection
