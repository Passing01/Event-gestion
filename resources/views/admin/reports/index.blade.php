@extends('layouts.dashboard')

@section('title', 'Rapports et Analyses')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Rapports</h1>
            <p class="dash-subtitle">Analyses de performance et statistiques globales.</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.reports.generate') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Exporter PDF
            </a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="card">
            <p class="section-sub">Moyenne questions / event</p>
            <h3 class="dash-title">{{ $data['avg_questions'] }}</h3>
        </div>
        <div class="card">
            <p class="section-sub">Taux d'engagement moyen</p>
            <h3 class="dash-title">{{ $data['engagement_rate'] }}%</h3>
        </div>
        <div class="card">
            <p class="section-sub">Events Premium / mois</p>
            <h3 class="dash-title">{{ $data['premium_events_month'] }}</h3>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3 class="admin-card-title">Répartition des plans</h3>
            <ul style="list-style: none;">
                @foreach($data['plans_breakdown'] as $plan => $count)
                <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                    <span>{{ $plan }}</span>
                    <span style="font-weight: 600;">{{ $count }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="card">
            <h3 class="admin-card-title">Alertes Système</h3>
            <p style="color: var(--muted-foreground); font-size: 0.875rem;">Aucune alerte critique détectée. La plateforme fonctionne normalement.</p>
        </div>
    </div>
</div>
@endsection
