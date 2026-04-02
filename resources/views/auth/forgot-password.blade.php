@extends('layouts.auth')

@section('title', 'Mot de passe oublié – Event Q&A')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">Q&A</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        {{-- Icône --}}
        <div style="width:3.5rem;height:3.5rem;border-radius:1rem;background:color-mix(in srgb,var(--brand) 12%,transparent);display:grid;place-items:center;margin-bottom:1.25rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <h1>Mot de passe oublié ?</h1>
        <p style="margin-bottom:1.5rem;">Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

        {{-- Erreur --}}
        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;display:flex;gap:0.5rem;align-items:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Succès --}}
        @if(session('status'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;display:flex;gap:0.5rem;align-items:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="{{ old('email') }}"
                       placeholder="vous@exemple.com"
                       required autocomplete="email">
                @error('email')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-brand" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Envoyer le lien de réinitialisation
            </button>
        </form>

        <span class="auth-link" style="margin-top:1rem;">
            <a href="{{ route('login') }}" style="display:flex;align-items:center;gap:0.25rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour à la connexion
            </a>
        </span>

    </div>
</main>

@endsection
