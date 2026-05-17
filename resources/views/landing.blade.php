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
            @if($marketplaceEvents->count() > 0)
                @foreach($marketplaceEvents->take(3) as $mEvent)
                    <div class="feature-card" style="padding: 1.5rem; display: flex; flex-direction: column; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s;">
                        <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: var(--brand-light); position: relative;">
                            @if($mEvent->image_path)
                                <img src="{{ asset('storage/' . $mEvent->image_path) }}" alt="{{ $mEvent->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="display: grid; place-items: center; height: 100%; color: var(--brand); font-size: 3rem; background: linear-gradient(135deg, var(--brand-light) 0%, #fff 100%);">🗓️</div>
                            @endif
                            <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">Premium</div>
                            <div style="position: absolute; bottom: 0.75rem; left: 0.75rem; background: rgba(0, 0, 0, 0.6); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                {{ number_format($mEvent->marketplace_price ?? 14990, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-weight: 800; color: var(--foreground);">{{ $mEvent->name }}</h3>
                        <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1rem; flex-grow: 1; line-height: 1.4;">
                            {{ Str::limit($mEvent->ai_summary ?? $mEvent->description ?? "Plongez dans les détails de cet événement et découvrez les synthèses générées par notre intelligence artificielle.", 120) }}
                        </p>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 0.75rem; display: flex; justify-content: space-between;">
                            <span>Par <strong>{{ $mEvent->user->name }}</strong></span>
                            <span>{{ $mEvent->updated_at->format('d M Y') }}</span>
                        </div>

                        <a href="{{ route('marketplace.show', $mEvent->id) }}" class="btn btn-primary" style="width: 100%; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-align: center; display: block;">
                            🔍 Découvrir le Replay
                        </a>
                    </div>
                @endforeach
            @else
                <!-- 1. Tech & IA -->
                <div class="feature-card" style="padding: 1.5rem; display: flex; flex-direction: column; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s;">
                    <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); position: relative; display: grid; place-items: center; color: #fff;">
                        <div style="text-align: center; padding: 1rem;">
                            <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">🧠</span>
                            <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">IA & ÉDUCATION</span>
                        </div>
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">PREMIUM</div>
                        <div style="position: absolute; bottom: 0.75rem; left: 0.75rem; background: rgba(0, 0, 0, 0.6); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">19 990 XOF</div>
                    </div>
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-weight: 800; color: var(--foreground);">Tech & IA : L'Afrique de Demain</h3>
                    <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1rem; flex-grow: 1; line-height: 1.4;">
                        Comment l'intelligence artificielle révolutionne les diagnostics médicaux et personnalise les apprentissages scolaires en Afrique.
                    </p>
                    <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 0.75rem; display: flex; justify-content: space-between;">
                        <span>Par <strong>Amina Bamba</strong></span>
                        <span>CEO TechAfrica</span>
                    </div>
                    <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="width: 100%; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-align: center; display: block;">
                        🔍 Découvrir le Replay
                    </a>
                </div>

                <!-- 2. DeFi & Web3 -->
                <div class="feature-card" style="padding: 1.5rem; display: flex; flex-direction: column; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s;">
                    <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: linear-gradient(135deg, #10b981 0%, #047857 100%); position: relative; display: grid; place-items: center; color: #fff;">
                        <div style="text-align: center; padding: 1rem;">
                            <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">⛓️</span>
                            <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">DEFI & WEB3</span>
                        </div>
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">PREMIUM</div>
                        <div style="position: absolute; bottom: 0.75rem; left: 0.75rem; background: rgba(0, 0, 0, 0.6); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">15 000 XOF</div>
                    </div>
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-weight: 800; color: var(--foreground);">Web3 & DeFi Summit 2026</h3>
                    <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1rem; flex-grow: 1; line-height: 1.4;">
                        Une immersion totale dans la finance décentralisée, les contrats intelligents et les nouveaux modèles économiques émergents.
                    </p>
                    <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 0.75rem; display: flex; justify-content: space-between;">
                        <span>Par <strong>Dr. Koffi Mensah</strong></span>
                        <span>Expert FinTech</span>
                    </div>
                    <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="width: 100%; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-align: center; display: block;">
                        🔍 Découvrir le Replay
                    </a>
                </div>

                <!-- 3. Startup Growth -->
                <div class="feature-card" style="padding: 1.5rem; display: flex; flex-direction: column; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s;">
                    <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); position: relative; display: grid; place-items: center; color: #fff;">
                        <div style="text-align: center; padding: 1rem;">
                            <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">🚀</span>
                            <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">STARTUP GROWTH</span>
                        </div>
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">PREMIUM</div>
                        <div style="position: absolute; bottom: 0.75rem; left: 0.75rem; background: rgba(0, 0, 0, 0.6); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">12 500 XOF</div>
                    </div>
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; font-weight: 800; color: var(--foreground);">Lever des fonds en Afrique</h3>
                    <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1rem; flex-grow: 1; line-height: 1.4;">
                        Les stratégies essentielles pour structurer son pitch deck et capter l'intérêt des grands fonds de capital-risque mondiaux.
                    </p>
                    <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 0.75rem; display: flex; justify-content: space-between;">
                        <span>Par <strong>Jean-Luc Kouadio</strong></span>
                        <span>Investisseur VC</span>
                    </div>
                    <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="width: 100%; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-align: center; display: block;">
                        🔍 Découvrir le Replay
                    </a>
                </div>
            @endif
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
