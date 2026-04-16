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
                        <div style="display: grid; place-items: center; height: 100%; color: var(--brand); font-size: 3rem; opacity: 0.5;">🗓️</div>
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
            <div style="grid-column: 1 / -1; text-align: center; padding: 6rem 2rem; background: #f8fafc; border-radius: 2rem; border: 2px dashed #e2e8f0;">
                <div style="font-size: 4rem; margin-bottom: 1.5rem;">🌌</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Le Marketplace est en attente</h3>
                <p style="color: var(--muted-foreground);">Revenez bientôt pour découvrir les premiers événements partagés par notre communauté.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
