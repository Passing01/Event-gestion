@extends('layouts.auth')

@section('title', 'Accéder à l\'événement – Panéliste')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">Q&A</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        <h1>Accès Panéliste</h1>
        <p>Veuillez entrer le code de l'événement pour accéder à votre console.</p>

        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('panelist.join') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="code">Code de l'événement</label>
                <input type="text" id="code" name="code" class="form-input"
                       placeholder="Ex: AB12CD"
                       required style="text-transform: uppercase;">
                @error('code')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-brand">Accéder à la Console</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit" style="background: none; border: none; color: var(--muted-foreground); font-size: 0.75rem; cursor: pointer; text-decoration: underline;">Se déconnecter</button>
            </form>
        </div>
    </div>
</main>

@endsection
