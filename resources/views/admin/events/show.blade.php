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
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-label">Organisateur</div>
        <div class="stat-value" style="font-size: 1.25rem;">{{ $event->user?->name }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Code d'accès</div>
        <div class="stat-value" style="font-size: 1.25rem;">{{ $event->code }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Statut</div>
        <div class="stat-value" style="font-size: 1.25rem;">{{ ucfirst($event->status) }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Panélistes de l'événement</h3>
    @if($event->panelists->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Secteur</th>
                <th>Status Panelist</th>
                <th>Actions Compte</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->panelists as $panelist)
            <tr>
                <td>{{ $panelist->user?->name }}</td>
                <td>{{ $panelist->sector }}</td>
                <td>
                    <span class="badge {{ $panelist->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $panelist->is_active ? 'Actif' : 'Désactivé' }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="{{ route('admin.events.toggle-panelist', [$event->id, $panelist->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">
                                <i class="fas fa-power-off"></i> Toggle Status
                            </button>
                        </form>
                        <a href="{{ route('admin.users.show', $panelist->user_id) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-user-cog"></i> Gérer Compte
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #94a3b8;">Aucun panéliste pour cet événement.</p>
    @endif
</div>
</div>
@endsection
