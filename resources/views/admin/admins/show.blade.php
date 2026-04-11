@extends('layouts.dashboard')

@section('title', 'Détails Administrateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Détails de l'administrateur</h1>
            <p class="dash-subtitle">{{ $admin->name }}</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <h3 class="admin-card-title">Informations de compte</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <span style="color: var(--muted-foreground);">Email</span>
                <span style="font-weight: 500;">{{ $admin->email }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <span style="color: var(--muted-foreground);">Rôle</span>
                <span class="badge">Administrateur</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <span style="color: var(--muted-foreground);">Statut</span>
                <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-error' }}">
                    {{ $admin->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem;">
                <span style="color: var(--muted-foreground);">Membre depuis</span>
                <span>{{ $admin->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-primary" style="flex: 1; justify-content: center;">
                Modifier
            </a>
            @if(auth()->id() != $admin->id)
            <form action="{{ route('admin.admins.toggle', $admin->id) }}" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: center;">
                    {{ $admin->is_active ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
