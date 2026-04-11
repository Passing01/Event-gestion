@extends('layouts.dashboard')

@section('title', 'Détails Administrateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Détails Administrateur</h1>
            <p class="dash-subtitle">{{ $admin->name }}</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <h3>Informations</h3>
        <p><strong>Nom :</strong> {{ $admin->name }}</p>
        <p><strong>Email :</strong> {{ $admin->email }}</p>
        <p><strong>Rôle :</strong> Administrateur</p>
        <p><strong>Statut :</strong> <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-error' }}">{{ $admin->is_active ? 'Actif' : 'Inactif' }}</span></p>
    </div>
</div>
@endsection
