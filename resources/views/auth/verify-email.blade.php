@extends('layouts.auth')

@section('title', 'Vérification d\'email – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">SH</div>
            <span style="font-weight:600;font-size:0.875rem;">Smart Home</span>
        </div>

        <h1>Vérifiez votre email</h1>
        <p>Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?</p>

        @if (session('message'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('message') }}
            </div>
        @endif

        <div style="display:grid;gap:1rem;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-brand">Renvoyer l'email de vérification</button>
            </form>

            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="auth-link" style="background:none;border:none;cursor:pointer;width:100%;text-align:center;">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</main>

@endsection
