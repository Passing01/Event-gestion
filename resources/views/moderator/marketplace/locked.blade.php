@extends('layouts.dashboard')

@section('title', 'Mon Marketplace 🔒')

@section('content')
<div class="space-y-6" style="max-width: 800px; margin: 4rem auto; text-align: center; font-family: 'Inter', sans-serif;">
    <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border); padding: 4rem 2rem; border-radius: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
        <div style="font-size: 4rem; margin-bottom: 1.5rem; animation: pulse 2s infinite;">🔒</div>
        <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--foreground); margin-bottom: 1rem; letter-spacing: -0.025em;">
            Mon Marketplace Personnel
        </h1>
        <p style="color: var(--muted-foreground); font-size: 1.125rem; line-height: 1.6; max-width: 550px; margin: 0 auto 3rem;">
            Monétisez vos replays interactifs en direct et percevez des gains passifs. La gestion et la monétisation du Marketplace sont réservées exclusivement aux membres **Premium** et **Entreprise**.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; text-align: left; margin-bottom: 3rem;">
            <div style="background: var(--card); padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--border);">
                <span style="font-size: 1.25rem; font-weight: 700; display: block; margin-bottom: 0.5rem; color: var(--brand);">🌟 Plan Premium</span>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.5; margin: 0;">
                    Percevez **75%** des revenus générés par vos replays d'événements. La plateforme ne conserve que 25% de frais de gestion technique de l'IA.
                </p>
            </div>
            <div style="background: var(--card); padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--border);">
                <span style="font-size: 1.25rem; font-weight: 700; display: block; margin-bottom: 0.5rem; color: var(--brand);">💼 Plan Entreprise</span>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.5; margin: 0;">
                    Profitez d'un partage de revenus équitable de **50%** pour tout votre catalogue d'enregistrements avec support dédié 24/7 et formation sur-mesure.
                </p>
            </div>
        </div>

        <a href="{{ route('dashboard.subscription.index') }}" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.125rem; border-radius: 999px; font-weight: 700; box-shadow: 0 10px 20px var(--brand-soft);">
            Débloquer mon Marketplace
        </a>
    </div>
</div>
@endsection
