@extends('layouts.public')

@section('title', 'Event Q&A — Interagissez en direct avec votre audience')

@section('extra_css')
<style>
    /* --- Hero --- */
    .hero {
        padding: 5rem 0 6rem;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -10%;
        width: 40rem;
        height: 40rem;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.1) 0%, transparent 70%);
        z-index: -1;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .hero-content h1 {
        font-size: 4rem;
        line-height: 1.1;
        font-weight: 800;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-content p {
        font-size: 1.25rem;
        color: var(--muted-foreground);
        margin-bottom: 2.5rem;
        max-width: 32rem;
    }

    .hero-image {
        position: relative;
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        transform: perspective(1000px) rotateY(-10deg) rotateX(5deg);
        transition: transform 0.5s ease;
    }

    .hero-image:hover {
        transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
    }

    .hero-image img {
        width: 100%;
        display: block;
    }

    /* --- Features --- */
    .features {
        background: #f8fafc;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    .feature-card {
        background: #fff;
        padding: 2.5rem;
        border-radius: 1.5rem;
        border: 1px solid var(--border);
        transition: all 0.3s;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--card-shadow);
        border-color: var(--brand);
    }

    .feature-icon {
        width: 3.5rem;
        height: 3.5rem;
        background: var(--brand-light);
        color: var(--brand);
        border-radius: 1rem;
        display: grid;
        place-items: center;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .feature-card h3 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .feature-card p {
        color: var(--muted-foreground);
        font-size: 0.875rem;
    }

    /* --- CTA Section --- */
    .cta-section {
        text-align: center;
    }

    .cta-card {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        padding: 4rem;
        border-radius: 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .cta-card h2 {
        font-size: 3rem;
        margin-bottom: 1.5rem;
    }

    .cta-card p {
        font-size: 1.25rem;
        margin-bottom: 2.5rem;
        opacity: 0.9;
    }

    @media (max-width: 968px) {
        .hero-grid { grid-template-columns: 1fr; text-align: center; }
        .hero-content h1 { font-size: 2.75rem; }
        .hero-content p { margin: 0 auto 2.5rem; }
        .hero-image { transform: none; max-width: 500px; margin: 0 auto; }
        .feature-grid { grid-template-columns: 1fr; }
        .hero-btns { flex-direction: column; width: 100%; }
        .cta-card h2 { font-size: 2rem; }
    }
</style>
@endsection

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>Donnez la parole à votre audience.</h1>
            <p>La plateforme tout-en-un pour gérer vos sessions de Q&A en direct, modérer les questions et analyser l'impact de vos événements grâce à l'IA.</p>
            <div class="hero-btns" style="display: flex; gap: 1rem;">
                <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">Commencer gratuitement</a>
                <a href="{{ route('participant.join') }}" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1rem;">Rejoindre un live</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="{{ asset('event_qa_hero_1774710118395.png') }}" alt="Interface Event Q&A">
        </div>
    </div>
</section>

{{-- Section Marketplace Teaser --}}
<section id="marketplace" style="background: #fff;">
    <div class="container">
        <div class="section-header">
            <h2>Marketplace des Insights</h2>
            <p>Découvrez les dernières analyses partagées par notre communauté.</p>
        </div>

        <div class="feature-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            @forelse($marketplaceEvents->take(3) as $mEvent)
                <div class="feature-card" style="padding: 1.5rem; display: flex; flex-direction: column;">
                    <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: var(--brand-light); position: relative;">
                        @if($mEvent->image_path)
                            <img src="{{ asset('storage/' . $mEvent->image_path) }}" alt="{{ $mEvent->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="display: grid; place-items: center; height: 100%; color: var(--brand); font-size: 2rem;">🗓️</div>
                        @endif
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">Premium</div>
                    </div>
                    
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">{{ $mEvent->name }}</h3>
                    <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1.5rem; flex-grow: 1;">
                        Par {{ $mEvent->user->name }} • {{ $mEvent->updated_at->format('d M Y') }}
                    </p>

                    <a href="{{ route('marketplace.show', $mEvent->id) }}" class="btn btn-primary" style="width: 100%; border-radius: 0.75rem; font-size: 0.8rem;">
                        🔍 Voir les détails
                    </a>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: #f8fafc; border-radius: 2rem; border: 2px dashed #e2e8f0;">
                    <p style="color: var(--muted-foreground);">Les premiers événements arrivent bientôt sur la Marketplace !</p>
                </div>
            @endforelse
        </div>

        <div style="text-align: center; margin-top: 4rem;">
            <a href="{{ route('marketplace.index') }}" class="btn btn-outline" style="padding: 1rem 3rem;">
                Explorer tout le Marketplace
            </a>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <div class="section-header">
            <h2>Tout ce dont vous avez besoin</h2>
            <p>Des outils puissants pour transformer vos présentations en véritables échanges interactifs.</p>
        </div>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Q&A en Direct</h3>
                <p>Collectez les questions de votre audience en temps réel. Les participants peuvent voter pour les questions les plus pertinentes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Modération Avancée</h3>
                <p>Gardez le contrôle total. Approuvez, rejetez ou corrigez les questions avant qu'elles ne soient projetées sur grand écran.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧠</div>
                <h3>IA Insights</h3>
                <p>Obtenez une synthèse automatique de vos événements. Notre IA analyse les thématiques et le sentiment de votre audience.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <h2>Prêt à dynamiser vos événements ?</h2>
            <p>Rejoignez des milliers d'organisateurs qui font confiance à Event Q&A.</p>
            <a href="{{ route('auth.signup') }}" class="btn" style="background: #fff; color: var(--brand); padding: 1rem 3rem; font-size: 1.125rem;">Créer mon premier événement</a>
        </div>
    </div>
</section>
@endsection
