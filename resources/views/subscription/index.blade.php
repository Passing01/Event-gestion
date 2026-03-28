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

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2rem;">
        
        {{-- Plan Free --}}
        <div class="card" style="display: flex; flex-direction: column; {{ $user->plan == 'free' ? 'border: 2px solid var(--brand);' : '' }}">
            @if($user->plan == 'free')
                <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem;">PLAN ACTUEL</span>
            @endif
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Gratuit</h2>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">Pour vos petits événements entre amis ou collègues.</p>
            <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">0€ <span style="font-size: 1rem; color: var(--muted-foreground); font-weight: 400;">/mois</span></div>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; flex: 1; display: grid; gap: 0.75rem;">
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Jusqu'à 50 participants</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Modération standard</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">❌ IA Insights</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">❌ Exportation PDF</li>
            </ul>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="free">
                <button type="submit" class="btn-brand" style="background: var(--muted); color: var(--foreground);" {{ $user->plan == 'free' ? 'disabled' : '' }}>
                    {{ $user->plan == 'free' ? 'Plan Actuel' : 'Choisir ce plan' }}
                </button>
            </form>
        </div>

        {{-- Plan Premium --}}
        <div class="card" style="display: flex; flex-direction: column; position: relative; {{ $user->plan == 'premium' ? 'border: 2px solid var(--brand);' : 'border: 1px solid var(--brand);' }}">
            <div style="position: absolute; top: -0.75rem; left: 50%; transform: translateX(-50%); background: var(--brand); color: #fff; padding: 0.25rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">RECOMMANDÉ</div>
            @if($user->plan == 'premium')
                <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem;">PLAN ACTUEL</span>
            @endif
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Premium</h2>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">Pour les conférences et événements professionnels.</p>
            <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">29€ <span style="font-size: 1rem; color: var(--muted-foreground); font-weight: 400;">/mois</span></div>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; flex: 1; display: grid; gap: 0.75rem;">
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Participants illimités</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Modération avancée</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ IA Insights & Synthèse</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Exportation PDF illimitée</li>
            </ul>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="premium">
                <button type="submit" class="btn-brand" {{ $user->plan == 'premium' ? 'disabled' : '' }}>
                    {{ $user->plan == 'premium' ? 'Plan Actuel' : 'Passer au Premium' }}
                </button>
            </form>
        </div>

        {{-- Plan Enterprise --}}
        <div class="card" style="display: flex; flex-direction: column; {{ $user->plan == 'enterprise' ? 'border: 2px solid var(--brand);' : '' }}">
            @if($user->plan == 'enterprise')
                <span class="badge" style="background: var(--brand); color: #fff; align-self: flex-start; margin-bottom: 1rem;">PLAN ACTUEL</span>
            @endif
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Entreprise</h2>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">Solutions sur mesure pour les grandes organisations.</p>
            <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">99€ <span style="font-size: 1rem; color: var(--muted-foreground); font-weight: 400;">/mois</span></div>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 2rem 0; flex: 1; display: grid; gap: 0.75rem;">
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Tout du plan Premium</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Support dédié 24/7</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ Marque blanche (Logo perso)</li>
                <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">✅ API & Intégrations</li>
            </ul>

            <form action="{{ route('dashboard.subscription.update') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="enterprise">
                <button type="submit" class="btn-brand" style="background: var(--foreground); color: var(--background);" {{ $user->plan == 'enterprise' ? 'disabled' : '' }}>
                    {{ $user->plan == 'enterprise' ? 'Plan Actuel' : 'Contacter la Vente' }}
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
