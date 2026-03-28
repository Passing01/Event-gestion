@extends('layouts.dashboard')

@section('title', 'Tableau de bord – Event Q&A')

@section('content')

<div class="space-y-5">
    {{-- ---- En-tête de bienvenue ---- --}}
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Bonjour, {{ Auth::user()->name }} 👋</h1>
                <p>Bienvenue dans votre espace de gestion Q&A.</p>
            </div>
            <a href="{{ route('dashboard.events.create') }}" class="btn-brand">+ Créer un événement</a>
        </div>
    </div>

    {{-- ---- Statistiques rapides ---- --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Événements Actifs</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $events->where('status', 'active')->count() }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Total Questions</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $events->sum('questions_count') }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Organisation</h3>
            <p style="font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem;">{{ Auth::user()->organization_name }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Plan</h3>
            <p style="font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem;">
                <span class="badge" style="background: var(--brand-light); color: var(--brand);">{{ ucfirst(Auth::user()->plan) }}</span>
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        {{-- ---- Événements récents ---- --}}
        <section class="card">
            <div class="section-header">
                <h2 class="section-title">Événements récents</h2>
                <a href="{{ route('dashboard.events.index') }}" style="font-size: 0.75rem; color: var(--brand); text-decoration: none;">Voir tout</a>
            </div>

            <div style="display: grid; gap: 1rem;">
                @forelse($events->take(5) as $event)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--muted); border-radius: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--brand); border-radius: 0.5rem; display: grid; place-items: center; color: #fff;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">{{ $event->name }}</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $event->date->format('d M Y') }} • Code : {{ $event->code }}</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('dashboard.moderator.index', $event->id) }}" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Console</a>
                        <a href="{{ route('dashboard.events.show', $event->id) }}" style="padding: 0.375rem; color: var(--muted-foreground);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
                    Aucun événement récent.
                </div>
                @endforelse
            </div>
        </section>

        {{-- ---- Aide & Quick Tips ---- --}}
        <section class="card" style="background: var(--brand); color: #fff;">
            <h2 class="section-title" style="color: #fff;">Besoin d'aide ?</h2>
            <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 1.5rem;">Apprenez à tirer le meilleur parti de votre espace Q&A.</p>
            
            <div style="display: grid; gap: 1rem;">
                <div style="background: rgba(255,255,255,0.1); padding: 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;">
                    <strong>Astuce :</strong> Partagez le QR Code de l'événement sur vos slides de présentation.
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;">
                    <strong>Modération :</strong> Activez la modération pour filtrer les questions avant qu'elles ne soient visibles.
                </div>
                <a href="#" style="color: #fff; font-size: 0.75rem; text-decoration: underline; margin-top: 0.5rem;">Consulter la documentation</a>
            </div>
        </section>

    </div>
</div>

@endsection
