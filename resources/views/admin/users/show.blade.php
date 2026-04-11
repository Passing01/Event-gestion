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
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="grid" style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem;">
        <div class="card">
            <h3>Informations</h3>
            <p><strong>Email :</strong> {{ $user->email }}</p>
            <p><strong>Rôle :</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Plan :</strong> {{ ucfirst($user->plan ?? 'Gratuit') }}</p>
            <p><strong>Compte créé le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            <p><strong>Statut :</strong> <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">{{ $user->is_active ? 'Actif' : 'Bloqué' }}</span></p>
        </div>
        <div class="card">
            <h3>Événements créés</h3>
            @if($user->events->count() > 0)
            <ul>
                @foreach($user->events as $event)
                <li>{{ $event->name }} ({{ $event->code }})</li>
                @endforeach
            </ul>
            @else
            <p>Aucun événement.</p>
            @endif
        </div>
    </div>
</div>
@endsection
