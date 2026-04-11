@extends('layouts.dashboard')

@section('title', 'Gestion des Événements')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Événements</h1>
            <p class="dash-subtitle">Surveillez tous les événements créés sur la plateforme.</p>
        </div>
    </div>

    <div class="card admin-table-card">
        <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
            <form action="{{ route('admin.events.index') }}" method="GET" style="display: flex; gap: 0.5rem; max-width: 400px;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-input" placeholder="Rechercher par nom ou code..." style="flex: 1;">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Événement</th>
                    <th>Code</th>
                    <th>Organisateur</th>
                    <th>Date</th>
                    <th>Panélistes</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $event->name }}</div>
                        @if($event->marketplace_published) <span class="badge badge-success" style="font-size: 0.6rem;">Marketplace</span> @endif
                    </td>
                    <td><code>{{ $event->code }}</code></td>
                    <td>
                        <div style="font-size: 0.875rem;">{{ $event->user?->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $event->user?->email }}</div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</td>
                    <td>{{ $event->panelists_count ?? 0 }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline btn-sm" title="Voir détails">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($events->hasPages())
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
