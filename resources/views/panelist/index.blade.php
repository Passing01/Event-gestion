@extends('layouts.dashboard')

@section('title', 'Mes Événements - Panéliste')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Mes Événements</h1>
        <p>Voici la liste des événements auxquels vous êtes invité en tant que panéliste.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(20rem, 1fr)); gap: 1.5rem;">
        @forelse($panelistEntries as $entry)
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <span class="badge" style="background: var(--brand-light); color: var(--brand);">{{ $entry->event->code }}</span>
                        <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $entry->event->date->format('d/m/Y') }}</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $entry->event->name }}</h3>
                    <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.5; margin-bottom: 1.5rem;">
                        {{ Str::limit($entry->event->description, 100) }}
                    </p>
                </div>
                
                <div style="display: flex; gap: 0.75rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <a href="{{ route('panelist.dashboard', $entry->event->code) }}" class="btn-brand" style="flex: 1; text-align: center;">Accéder à la Console</a>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
                <h2 style="font-weight: 700; margin-bottom: 0.5rem;">Aucun événement trouvé</h2>
                <p style="color: var(--muted-foreground);">Vous n'avez pas encore été invité à un événement en tant que panéliste.</p>
            </div>
        @endforelse
    </div>

    <div class="card" style="max-width: 30rem; margin-top: 2rem;">
        <h2 class="section-title">Rejoindre via un code</h2>
        <p style="margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--muted-foreground);">Si vous avez un code pour un nouvel événement, entrez-le ici.</p>
        <form action="{{ route('panelist.join') }}" method="POST" style="display: flex; gap: 0.75rem;">
            @csrf
            <input type="text" name="code" class="form-input" placeholder="Ex: AB12CD" required style="text-transform: uppercase;">
            <button type="submit" class="btn-brand" style="width: auto; padding: 0 1.5rem;">Rejoindre</button>
        </form>
    </div>
</div>

@endsection
