@extends('admin.layout')

@section('content')
<header>
    <h1>Tableau de bord</h1>
    <div class="user-profile">
        <span>{{ auth()->user()->name }}</span>
        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=6366f1&color=fff" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
    </div>
</header>

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
@endsection
