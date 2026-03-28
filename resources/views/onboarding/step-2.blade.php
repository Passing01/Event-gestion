@extends('layouts.auth')

@section('title', 'Configuration - Étape 2 – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card" style="max-width: 32rem;">

        {{-- Progress Bar --}}
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">
                <span>Étape 2 sur 3</span>
                <span>66%</span>
            </div>
            <div style="height: 0.5rem; background: var(--muted); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: 66%; background: var(--brand); transition: width 0.3s;"></div>
            </div>
        </div>

        <h1>Identité visuelle</h1>
        <p>Configurez l'apparence de votre interface de projection pour vos futurs événements.</p>

        <form method="POST" action="{{ route('onboarding.save-step', 2) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Couleur principale de la charte</label>
                <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                    @foreach(['#7c3aed', '#2563eb', '#0d9488', '#ea580c', '#db2777'] as $color)
                    <label style="cursor: pointer;">
                        <input type="radio" name="brand_color" value="{{ $color }}" class="hidden" {{ $loop->first ? 'checked' : '' }} style="display: none;">
                        <div class="color-swatch-ui" style="background: {{ $color }}; width: 2.5rem; height: 2.5rem; border-radius: 9999px; border: 3px solid transparent; transition: border-color 0.2s;"></div>
                    </label>
                    @endforeach
                </div>
                @error('brand_color')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label" for="projection_layout">Style de projection par défaut</label>
                <select id="projection_layout" name="projection_layout" class="form-input" required style="appearance: auto;">
                    <option value="modern" selected>Moderne (Épuré, gros caractères)</option>
                    <option value="classic">Classique (Avec bordures et ombres)</option>
                    <option value="minimal">Minimaliste (Texte seul)</option>
                </select>
                @error('projection_layout')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('onboarding.index') }}" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; text-align: center; line-height: 2.5rem;">← Retour</a>
                <button type="submit" class="btn-brand" style="flex: 2;">Continuer →</button>
            </div>
        </form>
    </div>
</main>

<script>
    document.querySelectorAll('input[name="brand_color"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.color-swatch-ui').forEach(sw => sw.style.borderColor = 'transparent');
            if (this.checked) {
                this.nextElementSibling.style.borderColor = 'var(--foreground)';
            }
        });
    });
    // Init first one
    document.querySelector('input[name="brand_color"]:checked').nextElementSibling.style.borderColor = 'var(--foreground)';
</script>

@endsection
