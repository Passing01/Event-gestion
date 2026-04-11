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

<div class="admin-stats-grid">
    <div class="card stat-card">
        <div class="flex items-center gap-3">
            <div class="room-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div>
                <p class="section-sub">Utilisateurs totaux</p>
                <h3 class="dash-title" style="font-size: 1.25rem;">{{ $stats['total_users'] }}</h3>
            </div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="flex items-center gap-3">
            <div class="room-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 5.578 3.65 10.301 8.72 12.13a11.97 11.97 0 0010.56-12.13c0-1.308-.205-2.567-.582-3.744A11.946 11.946 0 0112 2.714z" />
                </svg>
            </div>
            <div>
                <p class="section-sub">Administrateurs</p>
                <h3 class="dash-title" style="font-size: 1.25rem;">{{ $stats['total_admins'] }}</h3>
            </div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="flex items-center gap-3">
            <div class="room-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <div>
                <p class="section-sub">Événements</p>
                <h3 class="dash-title" style="font-size: 1.25rem;">{{ $stats['total_events'] }}</h3>
            </div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="flex items-center gap-3">
            <div class="room-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-1.568-4.56A5.99 5.99 0 008.11 10.39a3.75 3.75 0 103.89 7.61z" />
                </svg>
            </div>
            <div>
                <p class="section-sub">Événements Actifs</p>
                <h3 class="dash-title" style="font-size: 1.25rem;">{{ $stats['active_events'] }}</h3>
            </div>
        </div>
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
