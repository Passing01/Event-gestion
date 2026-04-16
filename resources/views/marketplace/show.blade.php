@extends('layouts.public')

@section('title', $event->name . ' — Insights Marketplace')

@section('extra_css')
<style>
    .details-hero {
        background: var(--foreground);
        color: #fff;
        padding: 5rem 0;
        position: relative;
        overflow: hidden;
    }

    .details-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.4) 0%, transparent 60%);
        z-index: 1;
    }

    .details-container {
        position: relative;
        z-index: 2;
        max-width: 900px;
        margin: 0 auto;
    }

    .event-label {
        display: inline-block;
        background: var(--brand);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        text-transform: uppercase;
        margin-bottom: 2rem;
        letter-spacing: 0.05em;
    }

    .details-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 2rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        margin-top: -4rem;
        padding-bottom: 6rem;
    }

    .main-card {
        background: #fff;
        border-radius: 2rem;
        padding: 3rem;
        border: 1px solid var(--border);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }

    .side-card {
        background: #fff;
        border-radius: 2rem;
        padding: 2rem;
        border: 1px solid var(--border);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        height: fit-content;
    }

    .insight-section {
        margin-bottom: 3rem;
    }

    .insight-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: var(--foreground);
    }

    .insight-content {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--foreground);
        opacity: 0.9;
    }

    .panelist-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--brand-light);
        border-radius: 1rem;
        margin-bottom: 0.75rem;
    }

    .panelist-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        background: var(--brand);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border);
    }

    .stat-row:last-child { border-bottom: none; }

    @media (max-width: 968px) {
        .details-grid { grid-template-columns: 1fr; margin-top: -2rem; gap: 2rem; }
        .details-hero h1 { font-size: 2.5rem; }
        .main-card { padding: 2rem 1.5rem; }
    }
</style>
@endsection

@section('content')
<div class="details-hero">
    <div class="container details-container">
        <span class="event-label">Premium Insights</span>
        <h1>{{ $event->name }}</h1>
        <p style="opacity: 0.7; font-size: 1.125rem;">
            Capturé le {{ $event->date->format('d F Y') }} • Partagé par {{ $event->user->name }}
        </p>
    </div>
</div>

<div class="container">
    <div class="details-grid">
        <div class="main-card">
            {{-- Synthèse IA --}}
            <div class="insight-section">
                <div class="insight-title">
                    <div style="color: var(--brand);">🧠</div>
                    Synthèse de l'événement
                </div>
                <div class="insight-content">
                    {{ $summary }}
                </div>
            </div>

            @if($event->ai_keywords)
            <div class="insight-section">
                <div class="insight-title">
                    <div style="color: var(--brand);">🏷️</div>
                    Thématiques clés
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    @foreach(json_decode($event->ai_keywords, true) as $kw)
                        <span style="background: var(--brand-light); color: var(--brand); padding: 0.5rem 1rem; border-radius: 999px; font-weight: 600; font-size: 0.875rem;">
                            #{{ $kw }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="margin-top: 4rem; text-align: center;">
                <a href="{{ route('marketplace.replay', $event->id) }}" class="btn btn-primary" style="padding: 1.25rem 3rem; font-size: 1.125rem; width: 100%; border-radius: 1.5rem;">
                    🎬 Accéder au Replay Interactif
                </a>
                <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--muted-foreground);">
                    Visualisez le flux live, les questions posées et les documents partagés.
                </p>
            </div>
        </div>

        <div class="side-card">
            <h3 style="margin-bottom: 1.5rem; font-weight: 800;">Intervenants</h3>
            <div style="margin-bottom: 2.5rem;">
                @foreach($event->panelists as $p)
                <div class="panelist-row">
                    <div class="panelist-avatar">👤</div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.9375rem;">{{ $p->user->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $p->sector }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <h3 style="margin-bottom: 1.5rem; font-weight: 800;">Métriques</h3>
            <div class="stat-row">
                <span style="color: var(--muted-foreground);">Questions</span>
                <span style="font-weight: 700;">{{ $event->questions->count() }}</span>
            </div>
            <div class="stat-row">
                <span style="color: var(--muted-foreground);">Participants</span>
                <span style="font-weight: 700;">{{ $event->participants()->count() }}</span>
            </div>
            <div class="stat-row">
                <span style="color: var(--muted-foreground);">Collecté par</span>
                <span style="font-weight: 700;">IA Insight</span>
            </div>

            <div style="margin-top: 2rem; padding: 1.5rem; background: var(--brand-light); border-radius: 1.5rem; text-align: center;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✨</div>
                <p style="font-size: 0.8125rem; font-weight: 600; color: var(--brand);">Inclus dans votre accès Premium</p>
            </div>
        </div>
    </div>
</div>
@endsection
