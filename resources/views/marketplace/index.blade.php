@extends('layouts.public')

@section('title', 'Marketplace des Insights — Event Q&A')

@section('extra_css')
<style>
    .marketplace-hero {
        background: linear-gradient(135deg, var(--brand-light) 0%, #ffffff 100%);
        padding: 6rem 0;
        text-align: center;
    }
    
    .marketplace-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
        margin-top: -3rem;
    }

    .event-card {
        background: #fff;
        border-radius: 1.5rem;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .event-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--brand);
    }

    .event-image {
        height: 14rem;
        width: 100%;
        position: relative;
        background: var(--brand-light);
    }

    .event-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-premium {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--brand);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .event-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .event-meta {
        font-size: 0.8125rem;
        color: var(--muted-foreground);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .event-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--foreground);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .event-description {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin-bottom: 1.5rem;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .event-footer {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-badge {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--muted-foreground);
        background: var(--brand-light);
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
    }

    @media (max-width: 768px) {
        .marketplace-hero h1 { font-size: 2.5rem; }
    }
</style>
@endsection

@section('content')
<section class="marketplace-hero">
    <div class="container">
        <h1>Explorez l'Intelligence Collective</h1>
        <p style="max-width: 600px; margin: 0 auto; color: var(--muted-foreground); font-size: 1.125rem;">
            Découvrez les synthèses IA, les questions marquantes et les enseignements des événements transformés par Event Q&A.
        </p>
    </div>
</section>

<section style="padding-top: 0; background: #fff;">
    <div class="container">
        <div class="event-grid">
            @forelse($events as $event)
            <article class="event-card">
                <div class="event-image">
                    @if($event->image_path)
                        <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}">
                    @else
                        <div style="display: grid; place-items: center; height: 100%; color: var(--brand); font-size: 3rem; background: linear-gradient(135deg, var(--brand-light) 0%, #fff 100%); opacity: 0.5;">🗓️</div>
                    @endif
                    <div class="badge-premium">Premium</div>
                </div>
                
                <div class="event-content">
                    <div class="event-meta">
                        <span>{{ $event->date->format('d M Y') }}</span>
                        <span>•</span>
                        <span>Par {{ $event->user->name }}</span>
                    </div>
                    
                    <h3 class="event-title">{{ $event->name }}</h3>
                    
                    <p class="event-description">
                        {{ $event->ai_summary ?? $event->description ?? 'Plongez dans les détails de cet événement et découvrez les synthèses générées par notre intelligence artificielle.' }}
                    </p>
                    
                    <div class="event-footer">
                        <div class="stat-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            {{ $event->questions_count }} Q&A
                        </div>
                        
                        <a href="{{ route('marketplace.show', $event->id) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                            Détails du Replay
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <!-- 1. Tech & IA -->
            <article class="event-card">
                <div class="event-image" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); display: grid; place-items: center; color: #fff;">
                    <div style="text-align: center; padding: 1rem;">
                        <span style="font-size: 3.5rem; display: block; margin-bottom: 0.5rem;">🧠</span>
                        <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">IA & ÉDUCATION</span>
                    </div>
                    <div class="badge-premium">Premium</div>
                </div>
                
                <div class="event-content">
                    <div class="event-meta">
                        <span>17 Mai 2026</span>
                        <span>•</span>
                        <span>Par Amina Bamba</span>
                    </div>
                    
                    <h3 class="event-title">Tech & IA : L'Afrique de Demain</h3>
                    
                    <p class="event-description">
                        Comment l'intelligence artificielle révolutionne les diagnostics médicaux et personnalise les apprentissages scolaires en Afrique.
                    </p>
                    
                    <div class="event-footer">
                        <div class="stat-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            48 Q&A
                        </div>
                        
                        <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 700;">
                            Détails du Replay
                        </a>
                    </div>
                </div>
            </article>

            <!-- 2. DeFi & Web3 -->
            <article class="event-card">
                <div class="event-image" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); display: grid; place-items: center; color: #fff;">
                    <div style="text-align: center; padding: 1rem;">
                        <span style="font-size: 3.5rem; display: block; margin-bottom: 0.5rem;">⛓️</span>
                        <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">DEFI & WEB3</span>
                    </div>
                    <div class="badge-premium">Premium</div>
                </div>
                
                <div class="event-content">
                    <div class="event-meta">
                        <span>15 Mai 2026</span>
                        <span>•</span>
                        <span>Par Dr. Koffi Mensah</span>
                    </div>
                    
                    <h3 class="event-title">Web3 & DeFi Summit 2026</h3>
                    
                    <p class="event-description">
                        Une immersion totale dans la finance décentralisée, les contrats intelligents et les nouveaux modèles économiques émergents.
                    </p>
                    
                    <div class="event-footer">
                        <div class="stat-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            32 Q&A
                        </div>
                        
                        <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 700;">
                            Détails du Replay
                        </a>
                    </div>
                </div>
            </article>

            <!-- 3. Startup Growth -->
            <article class="event-card">
                <div class="event-image" style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); display: grid; place-items: center; color: #fff;">
                    <div style="text-align: center; padding: 1rem;">
                        <span style="font-size: 3.5rem; display: block; margin-bottom: 0.5rem;">🚀</span>
                        <span style="font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em; text-transform: uppercase;">STARTUP GROWTH</span>
                    </div>
                    <div class="badge-premium">Premium</div>
                </div>
                
                <div class="event-content">
                    <div class="event-meta">
                        <span>12 Mai 2026</span>
                        <span>•</span>
                        <span>Par Jean-Luc Kouadio</span>
                    </div>
                    
                    <h3 class="event-title">Lever des fonds en Afrique</h3>
                    
                    <p class="event-description">
                        Les stratégies essentielles pour structurer son pitch deck et capter l'intérêt des grands fonds de capital-risque mondiaux.
                    </p>
                    
                    <div class="event-footer">
                        <div class="stat-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            60 Q&A
                        </div>
                        
                        <a href="{{ route('auth.signup') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 700;">
                            Détails du Replay
                        </a>
                    </div>
                </div>
            </article>
            @endforelse
        </div>
    </div>
</section>
@endsection
