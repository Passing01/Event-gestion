@extends('layouts.dashboard')

@section('title', 'Marketplace - Replays d\'Événements')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Marketplace des Événements</h1>
        <p>Revivez les meilleurs moments de nos conférences et webinaires.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(20rem, 1fr)); gap: 1.5rem;">
        @forelse($events as $event)
        <div class="card" style="display: flex; flex-direction: column; height: 100%;">
            @if($event->image_path)
            <div style="height: 10rem; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1rem;">
                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @else
            <div style="background: var(--brand-light); height: 10rem; border-radius: 0.75rem; margin-bottom: 1rem; display: grid; place-items: center; font-size: 3rem;">
                🎤
            </div>
            @endif
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--foreground);">{{ $event->name }}</h2>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); flex-grow: 1; margin-bottom: 1.5rem;">
                {{ Str::limit($event->description, 100) }}
            </p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem;">
                <div style="font-size: 0.75rem; color: var(--muted-foreground);">
                    <span>📅 {{ $event->date->format('d/m/Y') }}</span>
                    <span style="margin-left: 0.75rem;">💬 {{ $event->questions_count }} Q&A</span>
                </div>
                <a href="{{ route('marketplace.show', $event->id) }}" class="btn-brand" style="width: auto; padding: 0.5rem 1rem; font-size: 0.875rem;">
                    Voir plus
                </a>
            </div>
        </div>
        @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
            <p style="color: var(--muted-foreground);">Aucun replay disponible pour le moment.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
