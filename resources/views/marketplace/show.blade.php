@extends('layouts.dashboard')

@section('title', 'Détails du Replay : ' . $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <a href="{{ route('marketplace.index') }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">← Retour au Marketplace</a>
        <h1 style="margin-top: 0.5rem;">{{ $event->name }}</h1>
        <p>Résumé du replay interactif généré par l'IA.</p>
    </div>

    @if($event->image_path)
    <div style="width: 100%; height: 20rem; border-radius: 1.5rem; overflow: hidden; margin-bottom: 2rem;">
        <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <div class="space-y-5">
            {{-- Synthèse IA --}}
            <section class="card" style="border-left: 4px solid var(--brand);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--brand-light); color: var(--brand); border-radius: 0.75rem; display: grid; place-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <h2 class="section-title" style="margin-bottom: 0;">Synthèse du Contenu</h2>
                </div>
                <p style="font-size: 1.125rem; line-height: 1.6; color: var(--foreground);">{{ $summary }}</p>
                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <a href="{{ route('marketplace.replay', $event->id) }}" class="btn-brand" style="width: auto; padding: 0.75rem 2rem;">
                        📽️ Voir le Replay Interactif
                    </a>
                </div>
            </section>

            {{-- Panélistes --}}
            <section class="card">
                <h2 class="section-title">Intervenants</h2>
                <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                    @foreach($event->panelists as $p)
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; border-radius: 9999px; background: var(--muted); display: grid; place-items: center; font-size: 1.25rem;">
                            👤
                        </div>
                        <div>
                            <p style="font-weight: 700; margin: 0;">{{ $p->user->name }}</p>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">{{ $p->sector }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="space-y-5">
            {{-- Statistiques --}}
            <section class="card">
                <h2 class="section-title">En Chiffres</h2>
                <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Questions traitées</span>
                        <span style="font-weight: 600;">{{ $event->questions->count() }}</span>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

@endsection
