@extends('layouts.auth')

@section('title', 'Rejoindre un événement – Q&A')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">Q&A</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        <h1>Bienvenue !</h1>
        <p>Entrez le code de l'événement et votre pseudo pour participer.</p>

        <form method="POST" action="{{ route('participant.join.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="code">Code de l'événement</label>
                <input type="text" id="code" name="code" class="form-input"
                       value="{{ old('code', $code) }}"
                       placeholder="Ex: AB12CD"
                       required style="text-transform: uppercase;">
                @error('code')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="pseudo">Votre Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" class="form-input"
                       value="{{ old('pseudo') }}"
                       placeholder="Ex: Jean D."
                       required>
                @error('pseudo')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-brand">Rejoindre l'événement</button>
        </form>

        <p class="auth-link" style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
            En rejoignant, vous acceptez les conditions d'utilisation.
        </p>
    </div>
</main>

@endsection
