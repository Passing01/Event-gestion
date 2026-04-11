@extends('admin.layout')

@section('content')
<header>
    <h1>Rapports et Statistiques</h1>
    <a href="{{ route('admin.reports.generate') }}" class="btn btn-primary">
        <i class="fas fa-download"></i> Générer un rapport complet
    </a>
</header>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-label">Total Événements</div>
        <div class="stat-value">{{ $data['events_count'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Total Questions</div>
        <div class="stat-value">{{ $data['total_questions'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Moy. Questions / Event</div>
        <div class="stat-value">{{ number_format($data['avg_questions_per_event'], 1) }}</div>
    </div>
</div>

<div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3>Répartition par Plan</h3>
        <ul style="list-style: none; margin-top: 1.5rem;">
            @foreach($data['users_by_plan'] as $plan)
            <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--glass-border);">
                <span>{{ ucfirst($plan->plan ?? 'Gratuit') }}</span>
                <span style="font-weight: 600;">{{ $plan->total }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <h3>Performance Plateforme</h3>
        <p style="color: #94a3b8; margin-top: 1rem;">Les données de performance sont stables.</p>
    </div>
</div>
@endsection
