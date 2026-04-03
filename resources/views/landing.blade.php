<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Q&A — Interagissez en direct avec votre audience</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;500;700&display=swap">
    <style>
        :root {
            --brand: #7c3aed;
            --brand-light: #f5f3ff;
            --brand-dark: #5b21b6;
            --foreground: #0f172a;
            --muted-foreground: #64748b;
            --background: #ffffff;
            --glass: rgba(255, 255, 255, 0.7);
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--foreground);
            background: var(--background);
            line-height: 1.5;
            overflow-x: hidden;
        }

        h1, h2, h3, .brand-font { font-family: 'Outfit', sans-serif; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* --- Header --- */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5rem;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            z-index: 100;
            display: flex;
            align-items: center;
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--brand);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-box {
            width: 2.5rem;
            height: 2.5rem;
            background: var(--brand);
            border-radius: 0.75rem;
            display: grid;
            place-items: center;
            color: #fff;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--foreground);
            font-weight: 500;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--brand); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--foreground);
        }

        .btn-outline:hover {
            background: var(--brand-light);
            border-color: var(--brand);
        }

        /* --- Hero --- */
        .hero {
            padding: 10rem 0 6rem;
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
            padding: 6rem 0;
            background: #f8fafc;
        }

        .section-header {
            text-align: center;
            max-width: 40rem;
            margin: 0 auto 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
            padding: 6rem 0;
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

        /* --- Footer --- */
        footer {
            padding: 4rem 0;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--muted-foreground);
            font-size: 0.875rem;
        }

        @media (max-width: 968px) {
            .hero-grid { grid-template-columns: 1fr; text-align: center; }
            .hero-content h1 { font-size: 3rem; }
            .hero-content p { margin: 0 auto 2.5rem; }
            .hero-image { transform: none; }
            .feature-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container nav-inner">
            <a href="/" class="logo">
                <div class="logo-box">Q&A</div>
                <span>Event Q&A</span>
            </a>
            <nav class="nav-links">
                <a href="#features">Fonctionnalités</a>
                <a href="#marketplace">Marketplace</a>
                <a href="{{ route('participant.join') }}" class="btn btn-outline">Rejoindre</a>
                <a href="{{ route('login') }}" class="btn btn-primary">Mon Espace</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <h1>Donnez la parole à votre audience.</h1>
                <p>La plateforme tout-en-un pour gérer vos sessions de Q&A en direct, modérer les questions et analyser l'impact de vos événements grâce à l'IA.</p>
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">Commencer gratuitement</a>
                    <a href="{{ route('participant.join') }}" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1rem;">Rejoindre un live</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('event_qa_hero_1774710118395.png') }}" alt="Interface Event Q&A">
            </div>
        </div>
    </section>

    {{-- Section Marketplace --}}
    <section id="marketplace" class="container" style="padding: 6rem 0; background: #fff; margin-bottom: 3rem;">
        <div class="section-header">
            <h2>Marketplace des Insights</h2>
            <p>Découvrez les analyses et les rapports d'événements publics partagés par notre communauté.</p>
        </div>

        <div class="feature-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            @forelse($marketplaceEvents as $mEvent)
                <div class="feature-card" style="padding: 1.5rem;">
                    <div style="height: 12rem; border-radius: 1rem; overflow: hidden; margin-bottom: 1.25rem; background: var(--brand-light); position: relative;">
                        @if($mEvent->image_path)
                            <img src="{{ asset('storage/' . $mEvent->image_path) }}" alt="{{ $mEvent->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="display: grid; place-items: center; height: 100%; color: var(--brand); font-size: 2rem;">🗓️</div>
                        @endif
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 999px; text-transform: uppercase;">Premium</div>
                    </div>
                    
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--foreground);">{{ $mEvent->name }}</h3>
                    <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1.5rem; line-height: 1.4;">
                        Par {{ $mEvent->user->name }} • {{ $mEvent->updated_at->format('d M Y') }}
                    </p>

                    <button class="btn btn-outline" style="width: 100%; opacity: 0.5; cursor: not-allowed; background: #f1f5f9; border: none; font-size: 0.8rem; font-weight: 700;" disabled>
                        🔒 Voir les détails (Bientôt)
                    </button>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: #f8fafc; border-radius: 2rem; border: 2px dashed #e2e8f0;">
                    <p style="color: var(--muted-foreground);">Bientôt disponible : Les premiers événements arrivent sur la Marketplace !</p>
                </div>
            @endforelse
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
                <div class="feature-card">
                    <div class="feature-icon">✋</div>
                    <h3>Prise de Parole</h3>
                    <p>Gérez les interventions orales avec la fonction "Main levée". Suivez l'ordre des demandes et notifiez les orateurs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Statistiques Live</h3>
                    <p>Visualisez l'engagement de votre public avec des graphiques en temps réel et des rapports d'activité complets.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📽️</div>
                    <h3>Mode Projection</h3>
                    <p>Une interface épurée et professionnelle pour projeter les questions sur grand écran lors de vos conférences.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>Prêt à dynamiser vos événements ?</h2>
                <p>Rejoignez des milliers d'organisateurs qui font confiance à Event Q&A pour leurs conférences, webinaires et réunions.</p>
                <a href="{{ route('auth.signup') }}" class="btn" style="background: #fff; color: var(--brand); padding: 1rem 3rem; font-size: 1.125rem;">Créer mon premier événement</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Event Q&A. Développé avec passion pour des échanges plus humains.</p>
        </div>
    </footer>

</body>
</html>
