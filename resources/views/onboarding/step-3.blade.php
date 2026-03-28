@extends('layouts.auth')

@section('title', 'Configuration - Étape 3 – Smart Home')

@section('content')

<main class="auth-page">
    <div class="auth-card" style="max-width: 32rem;">

        {{-- Progress Bar --}}
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">
                <span>Étape 3 sur 3</span>
                <span>100%</span>
            </div>
            <div style="height: 0.5rem; background: var(--muted); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: 100%; background: var(--brand); transition: width 0.3s;"></div>
            </div>
        </div>

        <h1>Préférences de l'espace</h1>
        <p>Derniers réglages avant d'accéder à votre console de gestion.</p>

        <form method="POST" action="{{ route('onboarding.save-step', 3) }}">
            @csrf

            <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600;">Modération par défaut</h3>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground);">Activer la modération manuelle pour tous les nouveaux événements.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="hidden" name="default_moderation" value="0">
                        <input type="checkbox" name="default_moderation" value="1" class="toggle-input" checked
                               onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                        <span class="toggle-track" style="background:var(--brand);"></span>
                        <span class="toggle-thumb" style="transform:translateX(1.125rem);"></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Choisissez votre plan</label>
                <div style="display: grid; gap: 1rem; margin-top: 0.5rem;">
                    <label style="cursor: pointer; display: block;">
                        <input type="radio" name="plan" value="free" class="hidden" checked style="display: none;">
                        <div class="plan-card" style="padding: 1rem; border: 1px solid var(--border); border-radius: 0.75rem; transition: border-color 0.2s, background 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600;">Gratuit (Core)</span>
                                <span style="font-size: 0.75rem; background: var(--muted); padding: 0.25rem 0.5rem; border-radius: 9999px;">Actif</span>
                            </div>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem;">Réception illimitée, modération manuelle.</p>
                        </div>
                    </label>
                    <label style="cursor: pointer; display: block;">
                        <input type="radio" name="plan" value="premium" class="hidden" style="display: none;">
                        <div class="plan-card" style="padding: 1rem; border: 1px solid var(--border); border-radius: 0.75rem; transition: border-color 0.2s, background 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600;">Premium (IA)</span>
                                <span style="font-size: 0.75rem; color: var(--brand);">Essai 14j offert</span>
                            </div>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem;">Tri automatique par IA, analyse de sentiment.</p>
                        </div>
                    </label>
                </div>
                @error('plan')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('onboarding.index') }}" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; text-align: center; line-height: 2.5rem;">← Retour</a>
                <button type="submit" class="btn-brand" style="flex: 2;">Terminer la configuration →</button>
            </div>
        </form>
    </div>
</main>

<script>
    document.querySelectorAll('input[name="plan"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.plan-card').forEach(sw => {
                sw.style.borderColor = 'var(--border)';
                sw.style.background = 'transparent';
            });
            if (this.checked) {
                this.nextElementSibling.style.borderColor = 'var(--brand)';
                this.nextElementSibling.style.background = 'var(--brand-light)';
            }
        });
    });
    // Init first one
    const checkedPlan = document.querySelector('input[name="plan"]:checked');
    if (checkedPlan) {
        checkedPlan.nextElementSibling.style.borderColor = 'var(--brand)';
        checkedPlan.nextElementSibling.style.background = 'var(--brand-light)';
    }
</script>

@endsection
