@extends('layouts.dashboard')

@section('title', 'Analyse IA : ' . $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="{{ route('dashboard.insights.index') }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">← Retour à la liste</a>
                <h1 style="margin-top: 0.5rem;">Analyse IA : {{ $event->name }}</h1>
                <p>Généré automatiquement par notre moteur d'intelligence artificielle.</p>
            </div>
            <a href="{{ route('dashboard.insights.export', $event->id) }}" target="_blank" class="btn-brand">Exporter le Rapport (PDF)</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <div class="space-y-5">
            {{-- Résumé IA --}}
            <section class="card" style="border-left: 4px solid var(--brand);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--brand-light); color: var(--brand); border-radius: 0.75rem; display: grid; place-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <h2 class="section-title" style="margin-bottom: 0;">Synthèse de l'événement</h2>
                </div>
                <p style="font-size: 1.125rem; line-height: 1.6; color: var(--foreground);">{{ $summary }}</p>
            </section>

            {{-- Thématiques Clés --}}
            <section class="card">
                <h2 class="section-title">Mots-clés & Thématiques</h2>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem;">
                    @foreach($topKeywords as $kw)
                    <span style="background: var(--muted); padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;"># {{ $kw }}</span>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="space-y-5">
            {{-- Sentiment --}}
            <section class="card">
                <h2 class="section-title">Sentiment Global</h2>
                <div style="text-align: center; padding: 1rem 0;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        @if(str_contains($sentimentLabel, 'Positif')) 😊 
                        @elseif(str_contains($sentimentLabel, 'Critique')) 😠 
                        @else 😐 @endif
                    </div>
                    <p style="font-size: 1.25rem; font-weight: 700; color: {{ str_contains($sentimentLabel, 'Positif') ? '#059669' : (str_contains($sentimentLabel, 'Critique') ? '#dc2626' : '#6b7280') }};">{{ $sentimentLabel }}</p>
                    <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.5rem;">Basé sur l'analyse sémantique des questions.</p>
                </div>
            </section>

            {{-- Stats Rapides --}}
            <section class="card">
                <h2 class="section-title">Données Brutes</h2>
                <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Questions</span>
                        <span style="font-weight: 600;">{{ $event->questions->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Réponses</span>
                        <span style="font-weight: 600;">{{ $event->questions->sum(fn($q) => $q->replies->count()) }}</span>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

@endsection
