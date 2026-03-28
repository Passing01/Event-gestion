@extends('layouts.auth')

@section('title', 'Configuration - Étape 1 – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card" style="max-width: 32rem;">

        {{-- Progress Bar --}}
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">
                <span>Étape 1 sur 3</span>
                <span>33%</span>
            </div>
            <div style="height: 0.5rem; background: var(--muted); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: 33%; background: var(--brand); transition: width 0.3s;"></div>
            </div>
        </div>

        <h1>Parlez-nous de votre organisation</h1>
        <p>Aidez-nous à personnaliser votre expérience de gestion d'événements.</p>

        <form method="POST" action="{{ route('onboarding.save-step', 1) }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="organization_name">Nom de l'organisation</label>
                <input type="text" id="organization_name" name="organization_name" class="form-input"
                       value="{{ old('organization_name', $user->organization_name) }}"
                       placeholder="Ex: Event Pro S.A."
                       required>
                @error('organization_name')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="industry">Secteur d'activité</label>
                <select id="industry" name="industry" class="form-input" required style="appearance: auto;">
                    <option value="">Sélectionnez un secteur</option>
                    <option value="corporate" {{ old('industry', $user->industry) == 'corporate' ? 'selected' : '' }}>Entreprise / Corporate</option>
                    <option value="entertainment" {{ old('industry', $user->industry) == 'entertainment' ? 'selected' : '' }}>Divertissement / Spectacle</option>
                    <option value="education" {{ old('industry', $user->industry) == 'education' ? 'selected' : '' }}>Éducation / Conférence</option>
                    <option value="other" {{ old('industry', $user->industry) == 'other' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('industry')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn-brand">Continuer →</button>
            </div>
        </form>
    </div>
</main>

@endsection
