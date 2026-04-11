@extends('layouts.dashboard')

@section('title', 'Détails de l\'événement')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Détails de l'événement</h1>
            <p class="dash-subtitle">{{ $event->name }}</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Retour
            </a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="card stat-card">
            <p class="section-sub">Organisateur</p>
            <h3 class="dash-title" style="font-size: 1.15rem;">{{ $event->user?->name }}</h3>
            <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $event->user?->email }}</p>
        </div>
        <div class="card stat-card">
            <p class="section-sub">Code d'accès</p>
            <h3 class="dash-title" style="font-size: 1.15rem;"><code>{{ $event->code }}</code></h3>
        </div>
        <div class="card stat-card">
            <p class="section-sub">Statut Session</p>
            <h3 class="dash-title" style="font-size: 1.15rem;">
                <span class="badge {{ $event->status === 'active' ? 'badge-success' : 'badge-info' }}">
                    {{ ucfirst($event->status) }}
                </span>
            </h3>
        </div>
    </div>

    <div class="card admin-table-card">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border);">
            <h3 class="admin-card-title" style="margin-bottom: 0;">Panélistes de l'événement</h3>
        </div>
        
        @if($event->panelists && $event->panelists->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom / Email</th>
                    <th>Secteur</th>
                    <th>Statut Panéliste</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->panelists as $panelist)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $panelist->user?->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $panelist->user?->email }}</div>
                    </td>
                    <td>{{ $panelist->sector }}</td>
                    <td>
                        <span class="badge {{ $panelist->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $panelist->is_active ? 'Actif' : 'Désactivé' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <form action="{{ route('admin.events.toggle-panelist', [$event->id, $panelist->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" title="Toggle Status">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 12.728M5.636 5.636a9 9 0 0112.728 12.728m-12.728-12.728L18.364 18.364" />
                                    </svg>
                                </button>
                            </form>
                            <a href="{{ route('admin.users.show', $panelist->user_id) }}" class="btn btn-outline btn-sm" title="Gérer le compte">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 2rem; text-align: center; color: var(--muted-foreground);">
            <p>Aucun panéliste pour cet événement.</p>
        </div>
        @endif
    </div>
</div>
@endsection
