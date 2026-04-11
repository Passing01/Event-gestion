@extends('layouts.dashboard')

@section('title', 'Détails Utilisateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Détails de l'utilisateur</h1>
            <p class="dash-subtitle">{{ $user->name }}</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Retour
            </a>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3 class="admin-card-title">Profil & Informations</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="pref-row">
                    <span class="pref-label">Email</span>
                    <span style="font-weight: 500;">{{ $user->email }}</span>
                </div>
                <div class="pref-row">
                    <span class="pref-label">Rôle</span>
                    <span class="badge {{ $user->role === 'moderator' ? 'badge-info' : '' }}">{{ ucfirst($user->role) }}</span>
                </div>
                <div class="pref-row">
                    <span class="pref-label">Plan</span>
                    <span style="font-weight: 600; color: var(--brand);">{{ ucfirst($user->plan ?? 'Gratuit') }}</span>
                </div>
                <div class="pref-row">
                    <span class="pref-label">Créé le</span>
                    <span>{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="pref-row">
                    <span class="pref-label">Statut du compte</span>
                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $user->is_active ? 'Actif' : 'Bloqué' }}
                    </span>
                </div>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $user->is_active ? 'btn-outline' : 'btn-primary' }}" style="width: 100%; justify-content: center;">
                        {{ $user->is_active ? 'Bloquer l\'utilisateur' : 'Débloquer l\'utilisateur' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3 class="admin-card-title">Événements créés ({{ $user->events->count() }})</h3>
            @if($user->events->count() > 0)
            <div class="admin-table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->events as $event)
                        <tr>
                            <td style="font-weight: 600;">{{ $event->name }}</td>
                            <td style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline btn-sm">
                                    Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding: 2rem; text-align: center; color: var(--muted-foreground);">
                <p>Cet utilisateur n'a créé aucun événement.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
