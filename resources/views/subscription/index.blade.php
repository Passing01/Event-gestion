@extends('layouts.dashboard')

@section('title', 'Abonnement')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Abonnement</h1>
        <p>Choisissez le plan qui correspond le mieux à vos besoins événementiels.</p>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        
        {{-- Plan Free --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 1.25rem; padding: 2rem; {{ strtolower($user->plan) == 'free' ? 'border: 2px solid var(--brand); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.1);' : 'border: 1px solid var(--border);' }}">
            <div>
                @if(strtolower($user->plan) == 'free')
                    <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; display: inline-block;">PLAN ACTUEL</span>
                @endif
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--foreground);">Gratuit</h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem; min-height: 2.5rem;">Pour vos petits événements ou pour débuter sans frais.</p>
                <div style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem; color: var(--foreground);">0 F CFA <span style="font-size: 0.875rem; color: var(--muted-foreground); font-weight: 400;">/an</span></div>
                
                <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; display: grid; gap: 0.75rem;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Participants Illimités</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Modération standard sans IA</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">❌ Pas de gestion marketplace</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">📌 1 Événement / an</li>
                </ul>
            </div>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="free">
                <button type="submit" class="btn btn-outline" style="width: 100%; border-radius: 0.75rem;" {{ strtolower($user->plan) == 'free' ? 'disabled' : '' }}>
                    {{ strtolower($user->plan) == 'free' ? 'Plan Actuel' : 'Choisir ce plan' }}
                </button>
            </form>
        </div>

        {{-- Plan Standard --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 1.25rem; padding: 2rem; {{ strtolower($user->plan) == 'standard' ? 'border: 2px solid var(--brand); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.1);' : 'border: 1px solid var(--border);' }}">
            <div>
                @if(strtolower($user->plan) == 'standard')
                    <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; display: inline-block;">PLAN ACTUEL</span>
                @endif
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--foreground);">Standard</h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem; min-height: 2.5rem;">Idéal pour les événements réguliers de taille moyenne.</p>
                <div style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem; color: var(--foreground);">39.999 F CFA <span style="font-size: 0.875rem; color: var(--muted-foreground); font-weight: 400;">/an</span></div>
                
                <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; display: grid; gap: 0.75rem;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Tout du plan Gratuit</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Export de présence limité</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">❌ Pas de gestion marketplace</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">📌 5 Événements / an</li>
                </ul>
            </div>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="standard">
                <button type="submit" class="btn-brand" style="width: 100%; border-radius: 0.75rem;" {{ strtolower($user->plan) == 'standard' ? 'disabled' : '' }}>
                    {{ strtolower($user->plan) == 'standard' ? 'Plan Actuel' : 'Choisir ce plan' }}
                </button>
            </form>
        </div>

        {{-- Plan Premium --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 1.25rem; padding: 2rem; position: relative; {{ strtolower($user->plan) == 'premium' ? 'border: 2px solid var(--brand); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.1);' : 'border: 1px solid var(--brand);' }}">
            <div style="position: absolute; top: -0.75rem; left: 50%; transform: translateX(-50%); background: var(--brand); color: #fff; padding: 0.25rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em;">RECOMMANDÉ</div>
            <div>
                @if(strtolower($user->plan) == 'premium')
                    <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; display: inline-block;">PLAN ACTUEL</span>
                @endif
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--foreground);">Premium</h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem; min-height: 2.5rem;">Pour les conférences et événements d'envergure avec IA.</p>
                <div style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem; color: var(--foreground);">99.999 F CFA <span style="font-size: 0.875rem; color: var(--muted-foreground); font-weight: 400;">/an</span></div>
                
                <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; display: grid; gap: 0.75rem;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Tout du plan Gratuit + export</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ IA Gère la marketplace (25%)</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">🧠 Modération avancée par IA</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">📌 10 Événements / an</li>
                </ul>
            </div>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="premium">
                <button type="submit" class="btn-brand" style="width: 100%; border-radius: 0.75rem;" {{ strtolower($user->plan) == 'premium' ? 'disabled' : '' }}>
                    {{ strtolower($user->plan) == 'premium' ? 'Plan Actuel' : 'Choisir ce plan' }}
                </button>
            </form>
        </div>

        {{-- Plan Enterprise --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 1.25rem; padding: 2rem; {{ strtolower($user->plan) == 'enterprise' ? 'border: 2px solid var(--brand); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.1);' : 'border: 1px solid var(--border);' }}">
            <div>
                @if(strtolower($user->plan) == 'enterprise')
                    <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; display: inline-block;">PLAN ACTUEL</span>
                @endif
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--foreground);">Entreprise</h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem; min-height: 2.5rem;">Solutions sur mesure pour les grandes organisations.</p>
                <div style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem; color: var(--foreground);">Sur devis <span style="font-size: 0.875rem; color: var(--muted-foreground); font-weight: 400;">/an</span></div>
                
                <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; display: grid; gap: 0.75rem;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Tout du plan Premium</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Support dédié 24h/7j</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">✅ Marketplace (50%)</li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--foreground);">🎓 Formation à l'utilisation</li>
                </ul>
            </div>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="enterprise">
                <button type="submit" class="btn btn-outline" style="width: 100%; border-radius: 0.75rem;" {{ strtolower($user->plan) == 'enterprise' ? 'disabled' : '' }}>
                    {{ strtolower($user->plan) == 'enterprise' ? 'Plan Actuel' : 'Contacter le Support' }}
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
