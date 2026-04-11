@extends('layouts.dashboard')

@section('title', 'Administration - Tableau de bord')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Administration</h1>
            <p class="dash-subtitle">Bienvenue sur votre espace de gestion global.</p>
        </div>
    </div>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-label">Utilisateurs totaux</div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Administrateurs</div>
        <div class="stat-value">{{ $stats['total_admins'] }}</div>
    </div>
    <div class="card stat-card" style="background: rgba(236, 72, 153, 0.1); border-color: rgba(236, 72, 153, 0.2);">
        <div class="stat-label">Événements</div>
        <div class="stat-value">{{ $stats['total_events'] }}</div>
    </div>
    <div class="card stat-card" style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.2);">
        <div class="stat-label">Événements Actifs</div>
        <div class="stat-value">{{ $stats['active_events'] }}</div>
    </div>
</div>

<div class="grid">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem;">Activités récentes</h3>
        <p style="color: #94a3b8;">Aucune activité récente à afficher.</p>
    </div>
</div>
</div>
@endsection
